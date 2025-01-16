<?php
/**
 * Action after submit to add a records to Google Spreadsheet
 * @since 1.0.0
 */
use ElementorPro\Plugin;
use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Controls\Fields_Map;
use ElementorPro\Modules\Forms\Submissions\Database\Query as ele_submission_db;


/**
 * Class GSC_Elementor_Actions_Free
 */
class GSC_Elementor_Actions_Free extends \ElementorPro\Modules\Forms\Classes\Action_Base {

    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name() {
        return esc_html('gsc_elementorentor');
    }

    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label() {
        return esc_html__('GSheetConnector', 'gsheetconnector-for-elementor-forms');
    }

    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record Record.
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler Ajax handler.
     */
    public function run($record, $ajax_handler) {

        try {
            $gs_ele_settings = $record->get('form_settings');
            $spreadsheetsId = isset($gs_ele_settings['gs_spreadsheet_id']) ? $gs_ele_settings['gs_spreadsheet_id'] : '';
            $tab_id = isset($gs_ele_settings['gs_spreadsheet_tab_name']) ? $gs_ele_settings['gs_spreadsheet_tab_name'] : '';
            $gsele_value_data = array();
            if ($spreadsheetsId != '') {
                $sheet_data = get_option('elefgs_sheetId');
                $tabsData = get_option('elefgs_tabsId');
            }
            // Get entry ID value
            global $wpdb;
            $table = $wpdb->prefix . 'e_submissions';
            $query = "SELECT MAX(id) as latest_id FROM {$wpdb->prefix}e_submissions";
              
            $result = $wpdb->get_results($query);
            $latest_id = $result[0]->latest_id;

            // $entry_id = $latest_id + 1;

            // Header values
            $gsele_value_data = array();
            
            $gsele_value_data['Entry ID'] = $latest_id;

            $gsele_raw_fields = $record->get('fields');
            foreach ($gsele_raw_fields as $id => $field) {
                $gsele_value_data[$field['title']] = $field['value'];
            }
            if (!empty($gsele_value_data)) {
                $doc = new GSC_Elementor_Free();
                $doc->auth();
                $doc->setSpreadsheetId($spreadsheetsId);
                $doc->setWorkTabId($tab_id);
                $doc->add_row($gsele_value_data);
            }
            return;
        } catch (Exception $e) {
            GsEl_Connector_Utility::ele_gs_debug_log($e->getMessage());
            return;
        }
        
    }

    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget settings.
     */
    public function register_settings_section($widget) {
        $gsc_elementor_document = Plugin::elementor()->documents->get(get_the_ID());
        global $gsc_elementor_headers, $gsc_elementor_exclude_headertype;
        global $gsc_elementor_spreadsheetid, $gsc_elementor_sheetname, $gsc_elementor_sheet_headers, $gsc_elementor_sheetheaders, $existincurrentpage, $gsc_elementor_sheetheaders_new, $gsc_elementor_form_fields;

        // Get the verification and token options.
        $elefgs_verify = get_option('elefgs_verify');
        $elefgs_token = get_option('elefgs_token');
        $elefgs_token_manual = get_option('elefgs_token_manual');

        // Check if the verification is invalid or the token is not set.
        if (empty($elefgs_token) && $elefgs_verify == "invalid-auth") {
            $elefgs_verify = 'invalid-auth';
            }
         elseif(empty($elefgs_token_manual) && $elefgs_verify == "invalid-auth"){
           $elefgs_verify = 'invalid-auth';
            }
         else {
            // $elefgs_verify = 'valid';
        }
      

        if ($gsc_elementor_document) {
            $gsc_elementor_data = $gsc_elementor_document->get_elements_data();
            $gsc_elementor_data_global = $gsc_elementor_data;

            $gsc_elementor_data = Plugin::elementor()->db->iterate_data(
                $gsc_elementor_data,
                function ($element) use (&$do_update) {
                    if (isset($element['widgetType']) && 'form' === (string) $element['widgetType']) {
                        global $gsc_elementor_headers, $gsc_elementor_exclude_headertype;
                        global $gsc_elementor_spreadsheetid, $gsc_elementor_sheetname, $gsc_elementor_sheet_headers;
                        $gsc_elementor_exclude_headertype = array('honeypot', 'recaptcha', 'recaptcha_v3', 'html');
                        if (isset($element['settings']['gs_spreadsheet_id'])) {
                            $gsc_elementor_spreadsheetid = $element['settings']['gs_spreadsheet_id'];
                        }
                        if (isset($element['settings']['sheet_name'])) {
                            $gsc_elementor_sheetname = $element['settings']['sheet_name'];
                        }

                        // Add "Entry ID" to the headers unconditionally
                        $gsc_elementor_headers['Entry ID'] = 'Entry ID';

                        foreach ($element['settings']['form_fields'] as $formdata) {
                            if (!isset($formdata['field_type']) || (isset($formdata['field_type']) && !in_array($formdata['field_type'], $gsc_elementor_exclude_headertype, true))) {
                               $gsc_elementor_headers[$formdata['custom_id']] = isset($formdata['field_label']) && !empty($formdata['field_label']) ? $formdata['field_label'] : ucfirst($formdata['custom_id']);
                                // $gsc_elementor_headers[$formdata['custom_id']] = $formdata['field_label'] ? $formdata['field_label'] : ucfirst($formdata['custom_id']);
                            }
                        }
                        return $gsc_elementor_headers;
                    }
                }
            );
            if (empty($gsc_elementor_headers)) {
                Plugin::elementor()->db->iterate_data(
                    $gsc_elementor_data_global,
                    function ($element) use (&$do_update) {
                        if (isset($element['widgetType']) && 'global' === (string) $element['widgetType']) {
                            if (!empty($element['templateID'])) {
                                $global_form = get_post_meta($element['templateID'], '_elementor_data', true);
                                $global_form_meta = json_decode($global_form, true);
                                if ($global_form_meta) {
                                    global $gsc_elementor_headers, $gsc_elementor_exclude_headertype;
                                    global $gsc_elementor_spreadsheetid, $gsc_elementor_sheetname, $gsc_elementor_sheet_headers;
                                    $gsc_elementor_exclude_headertype = array('honeypot', 'recaptcha', 'recaptcha_v3', 'html');
                                    if (isset($global_form_meta[0]['settings']['gs_spreadsheet_id'])) {
                                        $gsc_elementor_spreadsheetid = $global_form_meta[0]['settings']['gs_spreadsheet_id'];
                                    }
                                    if (isset($global_form_meta[0]['settings']['gs_spreadsheet_tab_name'])) {
                                        $gsc_elementor_sheetname = $global_form_meta[0]['settings']['gs_spreadsheet_tab_name'];
                                    }
                                    if (is_array($global_form_meta[0]['settings']['form_fields'])) {
                                        

                                        foreach ($global_form_meta[0]['settings']['form_fields'] as $formdata) {
                                            if (!isset($formdata['field_type']) || (isset($formdata['field_type']) && !in_array($formdata['field_type'], $gsc_elementor_exclude_headertype, true))) {
                                                $gsc_elementor_headers[$formdata['custom_id']] = $formdata['field_label'] ? $formdata['field_label'] : ucfirst($formdata['custom_id']);
                                            }
                                        }
                                    }
                                    return $gsc_elementor_headers;
                                }
                            }
                        }
                    }
                );
            }
        }
        $widget->start_controls_section(
                'section_gsce',
                array(
                    'label' => esc_attr__('GSheetConnector', 'gsheetconnector-for-elementor-forms'),
                    'condition' => array(
                        'submit_actions' => $this->get_name(),
                    ),
                )
        );
        // Fetch and display Sheet details
        $sheet_data = get_option('elefgs_sheetId');
        $sheetId_array = isset($sheet_data) ? $sheet_data : array();
        $tabId_data = get_option('elefgs_tabsId');;
        $tabId_array = isset($tabId_data) ? $tabId_data : array();

        $sheet_id_name = array(
            '' => esc_html__('Select Google Spreadsheet', 'gsheetconnector-for-elementor-forms'),
        );

        $widget->add_control(
                'auth_integration_verify',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => $elefgs_verify,
                )
        );

        $widget->add_control(
                'gs_spreadsheet_id',
                array(
                    'label' => esc_attr__('Select Sheet name', 'gsheetconnector-for-elementor-forms'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $sheetId_array,
                    'label_block' => true,
                    'separator' => 'before',
                    'classes' => 'elefgs_sheet_id',
                    'event' => 'selectspreadsheet',
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                    ],
                )
        );

   
        $tab_arr = isset($tabId_array[$gsc_elementor_spreadsheetid]) ? $tabId_array[$gsc_elementor_spreadsheetid] : array();
       
        $widget->add_control(
                'gs_spreadsheet_tab_name',
                array(
                    'label' => esc_attr__('Select Sheet Tab name', 'gsheetconnector-for-elementor-forms').'<span class="elementor-state-icon tabselectionloading" style="display:none; margin-left: 15px;">
                    <i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
                </span>',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => isset($tab_arr) ? $tab_arr : array(),
                    'label_block' => true,
                    'separator' => 'before',
                    'classes' => 'elefgs_sheet_id',
                    'event' => 'selectspreadsheet',
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                    ],
                )
        );

        $widget->add_control(
                'gs_spreadsheet_selected_tab_name',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => "Sheet1",
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                    ],
                )
        );

         $widget->add_control(
                'gs_elmentor_all_sheet_data',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => isset($tabId_array) ? json_encode($tabId_array) : "",
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                    ],
                )
        );


        $widget->add_control(
                'tab_selection_process_log',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => '0',
                )
        );

        $widget->add_control(
                'gs-ajax-nonce-ele',
                array(
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => wp_create_nonce('gs-ajax-nonce-ele'),
                )
        );

        $widget->add_control(
                'gs_view_spreadsheet',
                array(
                    'label' => 'VIEW SPREAD SHEET',
                    'type' => \Elementor\Controls_Manager::BUTTON,
                    'button_type' => 'viewspreadsheet',
                    'text' => __('Click here', 'gsheetconnector-for-elementor-forms'),
                    'event' => 'namespace:editor:gsceviewsheet',
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                    ],

                )
        );

        $widget->add_control(
                'gs_view_fetchsheet',
                array(
                    'label' => 'SPREAD SHEET FETCHING',
                    'type' => \Elementor\Controls_Manager::BUTTON,
                    'button_type' => 'gscfetchsheet',
                    'text' => __('Click here', 'gsheetconnector-for-elementor-forms'),
                    'event' => 'namespace:editor:gscfetchsheet',
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                    ],
                )
        );

        $widget->add_control(
                'gs_view_process_fetchsheet',
                array(
                    'label' => '',
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<span class="loading-sign-process-fetch">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>',
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                    ],
                    
                )
        );

        $widget->add_control(
            'gs_feed_spreadsheet',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'text' => __('Click here to configure Form Feeds', 'gsheetconnector-for-elementor-forms'),
                'raw' => '<a href='.esc_url( admin_url( "admin.php?page=gsheetconnector-elementor-config&tab=form_feed_settings" ) ).' " target="_blank" style="color: #0073AA; text-decoration: underline;">Click here to Setup form feeds settings to easily configure multiple feeds simultaneously</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                ],
            ]
        );
        
        // Add a title for "Unlock More Features"
        $widget->add_control(
            'unlock_more_features_title',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<h3 style="font-weight:bold; font-size:16px; margin-bottom:15px; color:#333;">Unlock More Features</h3>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                ],
            ]
        );

        // Add a heading for "Default Form Fields"
        $widget->add_control(
            'default_form_fields_heading',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<h4 style="font-weight:bold; font-size:16px; margin-bottom:15px; color:#333;">Default Form Fields (Headers)</h3>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                    
                ],
            ]
        );

        // Add switcher controls for each header
        $widget->add_control(
            'headers[Entry Date]',
            [
                'label' => esc_attr__('Entry Date', 'gsheetconnector-for-elementor-forms'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                            font-size: 12px;
                            margin-bottom: 15px;
                            color: #873d10;
                            position: absolute;
                            right: 23px;
                            top: 0;
                            background: #FFD700;
                            padding: 5px 10px 2px;
                            border-radius: 5px;">PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                   
                ],
            ]
        );

        $widget->add_control(
            'headers[Post ID]',
            [
                'label' => esc_attr__('Post ID', 'gsheetconnector-for-elementor-forms'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                            font-size: 12px;
                            margin-bottom: 15px;
                            color: #873d10;
                            position: absolute;
                            right: 23px;
                            top: 0;
                            background: #FFD700;
                            padding: 5px 10px 2px;
                            border-radius: 5px;">PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                   
                ],
            ]
        );

        $widget->add_control(
            'headers[User Name]',
            [
                'label' => esc_attr__('User Name', 'gsheetconnector-for-elementor-forms'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                            font-size: 12px;
                            margin-bottom: 15px;
                            color: #873d10;
                            position: absolute;
                            right: 23px;
                            top: 0;
                            background: #FFD700;
                            padding: 5px 10px 2px;
                            border-radius: 5px;">PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                   
                ],
            ]
        );

        $widget->add_control(
            'headers[User IP]',
            [
                'label' => esc_attr__('User IP', 'gsheetconnector-for-elementor-forms'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                            font-size: 12px;
                            margin-bottom: 15px;
                            color: #873d10;
                            position: absolute;
                            right: 23px;
                            top: 0;
                            background: #FFD700;
                            padding: 5px 10px 2px;
                            border-radius: 5px;">PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                   
                ],
            ]
        );

        $widget->add_control(
            'headers[User Agent]',
            [
                'label' => esc_attr__('User Agent', 'gsheetconnector-for-elementor-forms'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                            font-size: 12px;
                            margin-bottom: 15px;
                            color: #873d10;
                            position: absolute;
                            right: 23px;
                            top: 0;
                            background: #FFD700;
                            padding: 5px 10px 2px;
                            border-radius: 5px;">PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                   
                ],
            ]
        );

        $widget->add_control(
            'headers[User ID]',
            [
                'label' => esc_attr__('User ID', 'gsheetconnector-for-elementor-forms'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                            font-size: 12px;
                            margin-bottom: 15px;
                            color: #873d10;
                            position: absolute;
                            right: 23px;
                            top: 0;
                            background: #FFD700;
                            padding: 5px 10px 2px;
                            border-radius: 5px;">PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                   
                ],
            ]
        );

        $widget->add_control(
            'headers[Referrer]',
            [
                'label' => esc_attr__('Referrer', 'gsheetconnector-for-elementor-forms'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                            font-size: 12px;
                            margin-bottom: 15px;
                            color: #873d10;
                            position: absolute;
                            right: 23px;
                            top: 0;
                            background: #FFD700;
                            padding: 5px 10px 2px;
                            border-radius: 5px;">PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                   
                ],
            ]
        );


        // Add a heading for "Form Fields"
        $widget->add_control(
            'form_fields_heading',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<h3 style="font-weight:bold; font-size:16px; margin-bottom:15px; color:#333;">Form Fields (Headers)</h3>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                ],
            ]
        );

        // Add switcher controls for each header
        if(!empty($gsc_elementor_headers)){
          foreach ($gsc_elementor_headers as $key => $value) {
            $widget->add_control(
                'headers[' . $value . ']',
                [
                    'label' => esc_attr__($value, 'gsheetconnector-for-elementor-forms'),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="  font-weight: bold;
                                font-size: 12px;
                                margin-bottom: 15px;
                                color: #873d10;
                                position: absolute;
                                right: 23px;
                                top: 0;
                                background: #FFD700;
                                padding: 5px 10px 2px;;
                                border-radius: 5px;">PRO</a>',
                    'condition' => [
                        'auth_integration_verify' => 'valid',
                       
                    ],
                ]
            );
        }
    }

        $widget->add_control(
            'gs_view_auth_license_pending',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'text' => __('Upgrade To Pro', 'gsheetconnector-for-elementor-forms'),
                'raw' => '<a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank" style="display: inline-block; padding: 10px 12px; background-color: #FFD700; color: #873d10; font-weight: bold; border-radius: 5px; text-decoration: none; overflow: hidden; cursor: pointer; white-space: nowrap; margin: 8px 0; font-size: 12px; width: 100%; text-align: center;">Upgrade To PRO</a>',
                'condition' => [
                    'auth_integration_verify' => 'valid',
                ],
            ]
        );
        
        $widget->add_control(
                'gs_view_auth_pending_url',
                array(
                    'label' => '',
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => "<p class='elementor-gs-display-note' style='border: 1px solid #c3c4c7;
    border-left-width: 4px;
    border-left-color: #d63638;
    margin: 5px 0 15px;
    background: #fff;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 12px 12px;'><strong>Authentication Required:</strong> You must have to <a href='admin.php?page=gsheetconnector-elementor-config' target='_blank'>Authenticate using your Google Account</a> along with Google Drive and Google Sheets Permissions in order to enable the settings for configuration.</p>",
                    'condition' => [
                        'auth_integration_verify' => 'invalid-auth',
                    ],
                    
                )
        );

       

        $widget->end_controls_section();
    }

    /**
     * On Export
     *
     * Clears form settings on export
     *
     * @access Public
     * @param array $element_sheets clear settings.
     */
    public function on_export($element_sheets) {
        
    }

}

$gsc_elementor_actions = new GSC_Elementor_Actions_Free;