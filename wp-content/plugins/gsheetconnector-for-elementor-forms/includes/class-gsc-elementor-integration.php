<?php

/**
 * Integration class for Google Sheet Connector
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Gs_Connector_Service Class
 *
 * @since 1.0.0
 */
class GSC_Elementor_Integration {

    /**
     *  Set things up.
     *  @since 1.0
     */
    public function __construct() {
        add_action('wp_ajax_verify_ele_integation', array($this, 'verify_ele_integation'));
        add_action('wp_ajax_deactivate_ele_integation', array($this, 'deactivate_ele_integation'));
        add_action('wp_ajax_gs_clear_log_elementor', array($this, 'gs_clear_log_elementor'));
        add_action('wp_ajax_sync_google_account_ele', array($this, 'sync_google_account_ele'));

        add_action('wp_ajax_sync_google_account_ele_elementor', array($this, 'sync_google_account_ele_elementor'));
       // get metforms settings using ajax
       add_action('wp_ajax_get_gsc_metforms', array($this, 'get_gsc_metforms'));
        //Save integration method using ajax
        add_action('wp_ajax_save_method_api_ele', array($this, 'save_method_api_ele'));

        //save client id and secret id
        add_action('wp_ajax_ele_save_client_id_sec_id_gapi', array($this, 'ele_save_client_id_sec_id_gapi'));

        //deactivate auth token
        add_action('wp_ajax_ele_deactivate_auth_token_gapi', array($this, 'ele_deactivate_ele_integation_manual'));

        add_action('wp_ajax_get_google_tab_list_by_sheetname', array($this, 'get_google_tab_list_by_sheetname'));
        // clear debug logs method using ajax for system status tab
        add_action('wp_ajax_elemnt_clear_debug_logs', array($this, 'elemnt_clear_debug_logs'));

        // save met form sheet details in table
        add_action('admin_init', array($this, 'execute_post_data'));

        // Add Feed
        add_action( 'wp_ajax_save_elementor_feed', array($this, 'save_elementor_feed') );
        
        add_action( 'admin_init', array($this,'execute_post_data_elementor'));

        // form feed submit entry in google sheet
        add_action( 'elementor_pro/forms/new_record', array($this, 'send_form_submission_to_google_sheets_feed'), 10, 2 );

    }

     /**
     * form feed submit entry in google sheet.
     *
     * @since 1.0.0
     */
    public function send_form_submission_to_google_sheets_feed($record, $handler) {
        // Get Elementor form settings and fields
        $gs_ele_settings = $record->get('form_settings');
        $gsele_raw_fields = $record->get('fields');
        
        $form_id = $gs_ele_settings['form_post_id']; // Get the form ID from Elementor form settings

        global $wpdb;
        $table = $wpdb->prefix . 'postmeta';

        // Fetch associated feeds for the form
        $feeds = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table WHERE meta_value = %s AND post_id = %d", 'gscele_form_feeds', $form_id)
        );

        if (empty($feeds)) {
            return; // Exit early if no feeds are found
        }

        $spreadsheetData = [];
        foreach ($feeds as $feed) {
            $feed_id = $feed->meta_id;

            // Fetch feed configuration data
            $spreadsheetDataRaw = get_post_meta($feed_id, 'gscele_form_feeds', true);
            $spreadsheetData = maybe_unserialize($spreadsheetDataRaw);

            // Prepare data for Google Sheets
            $data = [];
            foreach ($gsele_raw_fields as $field_key => $field_value) {
                // Map Elementor field keys to user-friendly names
                $field_label = $field_value['title'] ?? $field_key;
                $field_data = $field_value['value'] ?? '';

                $data[$field_label] = is_array($field_data)
                    ? implode(',', array_map('esc_url', $field_data)) // Handle file upload fields
                    : esc_html($field_data);
            }

            // Extract Google Sheets configuration
            $spreadsheet_id = esc_attr($spreadsheetData['sheet-id'] ?? '');
            $tab_name = esc_attr($spreadsheetData['sheet-tab-name'] ?? '');
            $tab_id = esc_attr($spreadsheetData['tab-id'] ?? '');

            // Send data to Google Sheets for each feed (different tab per feed)
            if ($spreadsheet_id != "" && $tab_name != "" && $tab_id != "") {
                try {
                    include_once GS_CONN_ELE_ROOT . '/lib/google-sheets.php';
                    $doc = new GSC_Elementor_Free();
                    $doc->auth();
                    $doc->setSpreadsheetId($spreadsheet_id);
                    $doc->setWorkTabId($tab_id);

                    // Send data to the appropriate tab (ensure correct tab name and tab ID per feed)
                    $doc->add_row_feed($spreadsheet_id, $tab_name, $data, false);
                    // Log success
                    // error_log("Data successfully sent to Google Sheets for feed ID: $feed_id, Tab: $tab_name");
                } catch (Exception $e) {
                    error_log("Error sending data to Google Sheets for feed ID: $feed_id. " . $e->getMessage());
                }
            } else {
                error_log("Missing spreadsheet configuration for feed ID: $feed_id");
            }
        }
    }

    /**
     * Save feed settings in the database.
     *
     * @since 1.0.0
     */
    public function execute_post_data_elementor() {
        try {
            if (isset($_POST['execute-edit-feed-elementor'])) {
                // Nonce check
                if (!wp_verify_nonce($_POST['gs-ajax-nonce'], 'gs-ajax-nonce')) {
                    wp_die('Invalid nonce'); // Die with an error message if nonce fails verification
                }

                // Check if the user is logged in and has permissions to edit feeds
                if (!is_user_logged_in() || !current_user_can('edit_posts')) {
                    echo 'You do not have permission to edit feeds.';
                    exit;
                }

                // Get the feed ID and form ID from the form
                $feed_id = isset($_POST['feed_id']) ? sanitize_text_field($_POST['feed_id']) : "";
                $form_id = isset($_POST['form_id']) ? sanitize_text_field($_POST['form_id']) : "";

                // Get custom sheet name and tab name as per manual checkbox selection
                $sheet_name_custom = isset($_POST['elementor-gs']['sheet-name-custom']) ? stripslashes($_POST['elementor-gs']['sheet-name-custom']) : "";
                $tab_name_custom = isset($_POST['elementor-gs']['sheet-tab-name-custom']) ? stripslashes($_POST['elementor-gs']['sheet-tab-name-custom']) : "";
                $sheet_id_custom = isset($_POST['elementor-gs']['sheet-id-custom']) ? $_POST['elementor-gs']['sheet-id-custom'] : "";
                $tab_id_custom = isset($_POST['elementor-gs']['tab-id-custom']) ? $_POST['elementor-gs']['tab-id-custom'] : "";

                // Update the feed data in the database
                if ($feed_id !== "" && $sheet_name_custom !== "" && $sheet_id_custom !== "" && $tab_name_custom !== "" && $tab_id_custom !== "") {
                    $meta_key = 'gscele_form_feeds';
                    $meta_value = array(
                        'sheet-name' => $sheet_name_custom,
                        'sheet-id' => $sheet_id_custom,
                        'sheet-tab-name' => $tab_name_custom,
                        'tab-id' => $tab_id_custom,
                    );

                    update_post_meta($feed_id, $meta_key, $meta_value);

                    $success_message = __('Settings saved successfully.', 'gsheetconnector-for-elementor-forms');
                }
            }
        } catch (Exception $e) {
            throw new LogicException("Error saving feed: " . $e->getMessage());
        }
    }

    // Add Feed Name Function
    public function save_elementor_feed(){
        
        // nonce checksave_avadaforms_gs_settings
          check_ajax_referer( 'elementorform-ajax-nonce', 'security' );

          /* sanitize incoming data */
          $feedName = sanitize_text_field( $_POST['feed_name'] );
          $elementorForms = sanitize_text_field( $_POST['elementorForms'] );

           $message ='';
           if(isset($feedName) && isset($elementorForms) && !empty($feedName) && !empty($elementorForms)){
              /*check same name feed exist or not */
                  $feed_check = get_post_meta($_POST['elementorForms'], $_POST['feed_name'], 'gscele_form_feeds');
                 
                  if(empty($feed_check)){
                      update_post_meta($_POST['elementorForms'], $_POST['feed_name'], 'gscele_form_feeds');
                    $message .='Feed has been successfully created.';
                                      
                  }else{
                   $message .='Feed name already exists in the list, Please enter unique name of feed.';

                  }
                  wp_send_json_success($message);
                  
         }
    }

    /**
     * Deleting Feed.
     *
     * @since 1.0.0
     */
    public function delete_feed() {
        try {
            check_ajax_referer( 'elementorform-ajax-nonce', 'security' );
            $feedId = intval($_POST['feed_id']);
            
            if ($feedId) {
                $deleted = delete_metadata('post', $feedId, 'gscele_form_feeds');
                $deleted1 = delete_metadata_by_mid('post', $feedId);

                if ( $deleted1) {
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
            wp_die();
        } catch (Exeption $e) {
            GsEl_Connector_Utility::ele_gs_debug_log($e->getMessage());
        }
        
    }


   /**
    * function save met form sheet details in table
    *
    * @since 1.0
    */
    public function execute_post_data() {
      if (isset($_POST ['mf-save-btn'])) {
     
         $met_form_id = $_POST['met-form-id'];

         $get_existing_data = get_post_meta($met_form_id, 'metform_gs_settings');


         $gs_sheet_name = isset($_POST['metform-gs']['sheet-name']) ? $_POST['metform-gs']['sheet-name'] : "";
         $gs_sheet_id = isset($_POST['metform-gs']['sheet-id']) ? $_POST['metform-gs']['sheet-id'] : "";
         $gs_tab_name = isset($_POST['metform-gs']['sheet-tab-name']) ? $_POST['metform-gs']['sheet-tab-name'] : "";
         $gs_tab_id = isset($_POST['metform-gs']['tab-id']) ? $_POST['metform-gs']['tab-id'] : "";
         // If data exist and user want to disconnect
         if (!empty($get_existing_data) && $gs_sheet_name == "") {
            update_post_meta($met_form_id, 'metform_gs_settings', "");
         }

         if (!empty($gs_sheet_name) && (!empty($gs_tab_name) )) {
            update_post_meta($met_form_id, 'metform_gs_settings', $_POST['metform-gs']);
         }



         $input_widgets = \Metform\Widgets\Manifest::instance()->get_input_widgets();

        $widget_input_data = get_post_meta($met_form_id, '_elementor_data', true);

        $widget_input_data = json_decode($widget_input_data);
        $fieldDetails = \MetForm\Core\Entries\Map_El::data($widget_input_data, $input_widgets)->get_el();
 
        $fields = [];
        foreach ($fieldDetails as $key => $field) {
            $widgetType = $field->widgetType;
            $type = substr($widgetType, 3);
            $withoutText = ['radio', 'checkbox', 'select', 'date', 'time', 'attachment', 'email', 'poll', 'signature', 'file', 'file-upload', 'multi-select'];
            if ($type == 'file-upload') {
                $type = 'file';
            } elseif (!in_array($type, $withoutText)) {
                $type = 'text';
            }
            $fields[] = [
                'name' => $key,
                'type' => $type,
                'label' => $field->mf_input_label,
            ];
        }
        $gsc_elementor_header_list = [];
         
          if(!empty($fields)){
           foreach($fields as $fs){
            $gsc_elementor_header_list[] = $fs['name'];

             }
          }
       
        if (!empty($gsc_elementor_header_list)) {
         $doc = new GSC_Elementor_Free();
         $doc->auth();
         $doc->add_header($gs_sheet_id, $gs_tab_name, $gsc_elementor_header_list, true);
         $message = GsEl_Connector_Utility::instance()->admin_notice(array(
                    'type' => 'update',
                    'message' => 'The Metform settings have been saved.'
                    ));
                  echo $message;
        }
      
        
      }
   }



  /**
     * AJAX function - get metforms settings
     * @since 1.2
     */
    public function get_gsc_metforms(){
          // nonce check
      check_ajax_referer('wp-ajax-nonce', 'security');

      $form = get_post($_POST['metformsId']);
      $met_form_id = $_POST['metformsId'];

      ob_start();
      $this->metforms_googlesheet_settings_content($met_form_id);
      $result = ob_get_contents();
      ob_get_clean();
      wp_send_json_success(htmlentities($result));
    }

   /**
    * Function - save the setting data of google sheet with sheet name and tab name
    *  @since 1.2
    */
   public function metforms_googlesheet_settings_content($met_form_id) {

      $get_data = get_post_meta($met_form_id, 'metform_gs_settings');

      $saved_sheet_name = isset($get_data[0]['sheet-name']) ? $get_data[0]['sheet-name'] : "";
      $saved_tab_name = isset($get_data[0]['sheet-tab-name']) ? $get_data[0]['sheet-tab-name'] : "";

      echo '<div class="metforms-panel-content-section-googlesheet-tab">';
      echo '<div class="metforms-panel-content-section-title">';
      ?>
      <div class="metforms-gs-fields">
         <h3><?php esc_html_e('Google Sheet Settings', 'gsheetconnector-for-elementor-forms'); ?></h3>

         <p>
            <label><?php echo esc_html(__('Google Sheet Name', 'gsheetconnector-for-elementor-forms')); ?></label>
            <input type="text" name="metform-gs[sheet-name]" id="metforms-gs-sheet-name" 
                   value="<?php echo ( isset($get_data[0]['sheet-name']) ) ? esc_attr($get_data[0]['sheet-name']) : ''; ?>"/>

          <a href="" class="gs-name help-link"><img src="<?php echo GS_CONN_ELE_URL; ?>assets/img/help.png" class="help-icon"><span class='hover-data'><?php echo esc_html( __( 'Go to your google account and click on"Google apps" icon and than click "Sheets". Select the name of the appropriate sheet you want to link your contact form or create new sheet.', 'gsheetconnector-for-elementor-forms' ) ); ?> </span></a>

         
         </p>
         <p>
            <label><?php echo esc_html(__('Google Sheet Id', 'gsheetconnector-for-elementor-forms')); ?></label>
            <input type="text" name="metform-gs[sheet-id]" id="metforms-gs-sheet-id"
                   value="<?php echo ( isset($get_data[0]['sheet-id']) ) ? esc_attr($get_data[0]['sheet-id']) : ''; ?>"/>
          <a href="" class=" gs-name help-link"><img src="<?php echo GS_CONN_ELE_URL; ?>assets/img/help.png" class="help-icon"><span class='hover-data'><?php echo esc_html(__('you can get sheet id from your sheet URL', 'gsheetconnector-for-elementor-forms')); ?></span></a>
            
         </p>
         <p>
            <label><?php echo esc_html(__('Google Sheet Tab Name', 'gsheetconnector-for-elementor-forms')); ?></label>
            <input type="text" name="metform-gs[sheet-tab-name]" id="metforms-sheet-tab-name"
                   value="<?php echo ( isset($get_data[0]['sheet-tab-name']) ) ? esc_attr($get_data[0]['sheet-tab-name']) : ''; ?>"/>
                   <a href="" class=" gs-name help-link"><img src="<?php echo GS_CONN_ELE_URL; ?>assets/img/help.png" class="help-icon"><span class='hover-data'><?php echo esc_html( __( 'Open your Google Sheet with which you want to link your contact form . You will notice a tab names at bottom of the screen. Copy the tab name where you want to have an entry of contact form.', 'gsheetconnector-for-elementor-forms' ) ); ?></span></a>
           
         </p>
         <p>
            <label><?php echo esc_html(__('Google Tab Id', 'gsheetconnector-for-elementor-forms')); ?></label>
            <input type="text" name="metform-gs[tab-id]" id="metforms-gs-tab-id"
                   value="<?php echo ( isset($get_data[0]['tab-id']) ) ? esc_attr($get_data[0]['tab-id']) : ''; ?>"/>
            <a href="" class=" gs-name help-link"><img src="<?php echo GS_CONN_ELE_URL; ?>assets/img/help.png" class="help-icon"><span class='hover-data'><?php echo esc_html(__('you can get tab id from your sheet URL', 'gsheetconnector-for-elementor-forms')); ?></span></a>

          
         </p>
         <?php if(isset($get_data[0]['sheet-id']) && !empty($get_data[0]['sheet-id'])){
               $sheet_id = $get_data[0]['sheet-id'];
               $tab_id = $get_data[0]['tab-id'];
         ?>
        <p>
            <label><?php echo esc_html(__('Google Sheet URL', 'gsheetconnector-for-elementor-forms')); ?>
            </label>
                <a href="https://docs.google.com/spreadsheets/d/<?php echo $sheet_id ;?>/edit#gid=<?php echo $tab_id ?>" target="_blank" class="mf-sheet-url">Sheet URL
                </a>
      </p>
      <?php } ?>

      </div>
      <input type="submit" align="middle" value="Submit Data" id="mf-save-btn" class="mf-save-btn" name="mf-save-btn">
      <input type="hidden" name="met-form-id" id="met-form-id" value="<?php echo $met_form_id; ?>">
      </div>
      <?php
   }




      /**
     * AJAX function - deactivate activation - Manual
     * @since 1.2
     */
    public function ele_deactivate_ele_integation_manual()
    {
        // nonce check
        check_ajax_referer('gs-ajax-nonce-ele', 'security');
        //$ele_manual_setting = get_option('elefgs_manual_setting');
        //if(isset($ele_manual_setting) || $ele_manual_setting=="1"){
        if (get_option('elefgs_token_manual') !== '') {
            delete_option('elefgs_feeds');
            delete_option('elefgs_sheetId');
            delete_option('elefgs_sheetTabs');
            delete_option('elefgs_token_manual');
            delete_option('elefgs_verify');
            delete_option('elefgs_access_manual_code');
            delete_option('elefgs_email_account_manual');
            update_option('elefgs_manual_setting', '1');
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
        //}
    }


        /**
     * AJAX function - Save Client Id and Secret Id
     *
     * @since 1.0
     */
    public function ele_save_client_id_sec_id_gapi()
    {
        // nonce checksave_ele_settings
        check_ajax_referer('gs-ajax-nonce-ele', 'security');
        /* sanitize incoming data */
        $client_id = sanitize_text_field($_POST["client_id"]);
        $secret_id = sanitize_text_field($_POST["secret_id"]);
        //save google setting with manual client id and secret id
        if ((!empty($client_id)) && (!empty($secret_id))) {
            update_option('elefgs_client_id', $client_id);
            update_option('elefgs_secret_id', $secret_id);
            $Code = "";
            if (isset($_POST["ele_client_token"]))
                $Code = sanitize_text_field($_POST["ele_client_token"]);
            if (!empty($Code)) {
                update_option('elefgs_access_manual_code', $Code);
            } else {
                wp_send_json_success();
                return;
            }
            if (get_option('elefgs_access_manual_code') != '') {
                include_once(GS_CONN_ELE_ROOT . '/lib/google-sheets.php');
                $manual_access_code = get_option('elefgs_access_manual_code');
                $client_id = get_option('elefgs_client_id');
                $secret_id = get_option('elefgs_secret_id');

                GSC_Elementor_Free::preauth_manual($manual_access_code, $client_id,$secret_id, esc_html(admin_url('admin.php?page=gsheetconnector-elementor-config')));
                
                    update_option('elefgs_verify', 'valid');
                    update_option('elefgs_manual_setting', '1');
                //deactivate auto setting
                //delete_option('elefgs_token');
                //delete_option('elefgs_access_code');
                //deactivate auto setting
                wp_send_json_success();
            } else {
                update_option('elefgs_verify', 'invalid');
                wp_send_json_error();
            }
        } else {
            update_option('elefgs_client_id', '');
            update_option('elefgs_secret_id', '');
            wp_send_json_success();
            return;
        }
        }

    /*Save integration method using Ajax*/
     public function save_method_api_ele(){

        try {
            $msg = array();
            // nonce check
            check_ajax_referer('gs-ajax-nonce-ele', 'security');
            
            if($_POST['method_api_ele'] == "ele_manual")
                update_option('elefgs_manual_setting', '1');
            else
                update_option('elefgs_manual_setting', '0');

            wp_send_json_success();
            
        } catch (Exception $e) {
            $msg['ERROR_MSG'] = $e->getMessage();
            $msg['TRACE_STK'] = $e->getTraceAsString();
            GsEl_Connector_Utility::ele_gs_debug_log($msg);
            wp_send_json_error();
        }

    }

    /**
     * AJAX function - deactivate activation
     * @since 1.4
     */
    public function deactivate_ele_integation() {
        // nonce check
        check_ajax_referer('gs-ajax-nonce-ele', 'security');

        if (get_option('elefgs_token') !== '') {
            //delete_option('gs_feeds');
            delete_option('elefgs_sheetId');
            delete_option('elefgs_token');
            delete_option('elefgs_access_code');
            delete_option('elefgs_verify');

            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    /**
     * AJAX function - verifies the token
     * @since 1.0
     */
    public function verify_ele_integation() {
        // nonce check
        check_ajax_referer('gs-ajax-nonce-ele', 'security');

        /* sanitize incoming data */
        $Code = sanitize_text_field($_POST["code"]);

        update_option('elefgs_access_code', $Code);

        if (get_option('elefgs_access_code') != '') {
            include_once( GS_CONN_ELE_ROOT . '/lib/google-sheets.php');
            GSC_Elementor_Free::preauth(get_option('elefgs_access_code'));
            wp_send_json_success();
        } else {
            update_option('elefgs_verify', 'invalid');
            wp_send_json_error();
        }
    }
    
    /**
     * Function - sync with google account to fetch sheet and tab name
     * @since 1.0
     */
    public function sync_google_account_ele() {
        $return_ajax = false;

        if (isset($_POST['isajax']) && $_POST['isajax'] == 'yes') {
            // nonce check
            check_ajax_referer('gs-ajax-nonce-ele', 'security');
            $init = sanitize_text_field($_POST['isinit']);
            $return_ajax = true;
        }

        include_once( GS_CONN_ELE_ROOT . '/lib/google-sheets.php');
        $sheetId = array();
        $doc = new GSC_Elementor_Free();
        $doc->auth();
        // Get all spreadsheets
        // $spreadsheetFeed = $doc->get_spreadsheets();
        // $sheetId_array = array();
        // $tabId_array = array();
        // foreach ($spreadsheetFeed as $sheetfeeds) {
        //     $tabsData = array();
        //     $sheetId = $sheetfeeds['id'];
        //     $sheetname = $sheetfeeds['title'];
        //     //Get Worksheets
        //     $sheetId_array[$sheetId] = $sheetname;
        //     $tabsData = $doc->get_worktabs($sheetId);
        //     $tabId_array[$sheetId] = $tabsData;
        // }
        
        // $sheetData = array('sheetId_array'=>$sheetId_array, 'tabId_array'=>$tabId_array);
        // update_option('elefgs_sheetId', $sheetData);


        $spreadsheetFeed = $doc->get_spreadsheets();
        $sheetId_array = array();
        $sheetId_array = array_column($spreadsheetFeed, 'title', 'id');
        update_option('elefgs_sheetId', $sheetId_array);
        

        if ($return_ajax == true) {
            if ($init == 'yes') {
                wp_send_json_success(array("success" => 'yes'));
            } else {
                wp_send_json_success(array("success" => 'no'));
            }
        }

        if ($return_ajax == true) {
            if ($init == 'yes') {
                wp_send_json_success(array("success" => 'yes'));
            } else {
                wp_send_json_success(array("success" => 'no'));
            }
        }
    }

    /**
     * Function - sync with google account to fetch sheet and tab name
     * @since 1.0
     */
    public function sync_google_account_ele_elementor() {
        $return_ajax = false;

        if (isset($_POST['isajax']) && $_POST['isajax'] == 'yes') {
            // nonce check
            check_ajax_referer('gs-ajax-nonce-ele', 'security');
            $init = sanitize_text_field($_POST['isinit']);
            $return_ajax = true;
        }

        include_once( GS_CONN_ELE_ROOT . '/lib/google-sheets.php');
        $sheetId = array();
        $doc = new GSC_Elementor_Free();
        $doc->auth();
        // Get all spreadsheets
        // $spreadsheetFeed = $doc->get_spreadsheets();
        // $sheetId_array = array();
        // $tabId_array = array();
        // foreach ($spreadsheetFeed as $sheetfeeds) {
        //     $tabsData = array();
        //     $sheetId = $sheetfeeds['id'];
        //     $sheetname = $sheetfeeds['title'];
        //     //Get Worksheets
        //     $sheetId_array[$sheetId] = $sheetname;
        //     $tabsData = $doc->get_worktabs($sheetId);
        //     $tabId_array[$sheetId] = $tabsData;
        // }
        
        // $sheetData = array('sheetId_array'=>$sheetId_array, 'tabId_array'=>$tabId_array);
        // update_option('elefgs_sheetId', $sheetData);

        $spreadsheetFeed = $doc->get_spreadsheets();
        $sheetId_array = array();
        $sheetId_array = array_column($spreadsheetFeed, 'title', 'id');
        update_option('elefgs_sheetId', $sheetId_array);

        
        if ($return_ajax == true) {
            if ($init == 'yes') {
                wp_send_json_success(array("success" => 'yes'));
            } else {
                wp_send_json_success(array("success" => 'no'));
            }
        }
    }

    // old settings get forms list
    public function get_forms_connected_to_sheet(){
        global $wpdb;
        $query = $wpdb->get_results("SELECT ID,post_title,meta_value,meta_key from " . $wpdb->prefix . "posts as p JOIN " . $wpdb->prefix . "postmeta as pm on p.ID = pm.post_id where pm.meta_key='__elementor_forms_snapshot' AND p.post_type='page'");
          return $query;
    }

   // feed settings get forms list
    public function get_forms_feeds_connected_to_sheet(){
        global $wpdb;
        $query = $wpdb->get_results("SELECT ID,post_title,meta_value,meta_key,meta_id from " . $wpdb->prefix . "posts as p JOIN " . $wpdb->prefix . "postmeta as pm on p.ID = pm.post_id where pm.meta_value='gscele_form_feeds' AND p.post_type='page'");
          return $query;
    }



    /**
    * AJAX function - clear log file
    * @since 2.1
    */
   public function gs_clear_log_elementor() {
      // nonce check
      check_ajax_referer( 'gs-ajax-nonce-ele', 'security' );
      $existDebugFile = get_option('ele_gs_debug_log_file');
      $clear_file_msg ='';
      // check if debug unique log file exist or not then exists to clear file
      if (!empty($existDebugFile) && file_exists($existDebugFile)) {
       
         $handle = fopen ( $existDebugFile, 'w');
        
        fclose( $handle );
        $clear_file_msg ='Logs are cleared.';
       }
       else{
        $clear_file_msg = 'No log file exists to clear logs.';
       }
     
      
      wp_send_json_success($clear_file_msg);
   }

    /**
    * AJAX function - clear log file for system status tab
    * @since 2.1
    */
    public function elemnt_clear_debug_logs() {
        // nonce check
        check_ajax_referer('gs-ajax-nonce-ele', 'security');
        $handle = fopen(WP_CONTENT_DIR . '/debug.log', 'w');
        fclose($handle);
        wp_send_json_success();
    }

   /**
     * AJAX function - Fetch tab list by sheet name
     * @since 1.0
     */
    public function get_google_tab_list_by_sheetname() {

        // nonce check
        check_ajax_referer('gs-ajax-nonce-ele', 'security');
        $spreadsheet_id = sanitize_text_field($_POST['sheetname']);
        $refresh = sanitize_text_field($_POST['refresh']);
        $temp1 = array();
        $TabsId_array1 = array();
        $TabsId_array = array();
        $divifgs_sheetTabs = get_option('elefgs_tabsId');
        include_once( GS_CONN_ELE_ROOT . "/lib/google-sheets.php" );
        $doc = new GSC_Elementor_Free();
        $doc->auth();
        $TabsId_array = $doc->get_worktabs($spreadsheet_id);
        /* refresh tabs */
        if($refresh == 1){
          $temp1[$spreadsheet_id] = $divifgs_sheetTabs;
          update_option('elefgs_tabsId',$temp1);
        }else{
          //echo "---2---";
          if(empty($divifgs_sheetTabs)){
              $temp1[$spreadsheet_id] = $TabsId_array;
              update_option('elefgs_tabsId',$temp1);
              //update_option('divifgs_sheetTabs',"");
            }else{
              $TabsId_array1[$spreadsheet_id] = $TabsId_array;
              $temp = array_merge($divifgs_sheetTabs, $TabsId_array1);
              update_option('elefgs_tabsId',$temp);
            }
          }
            /* refresh tabs */
            $divifgs_sheetTabs = get_option('elefgs_tabsId');
            echo json_encode($divifgs_sheetTabs);
            wp_die();
      }

}

$gsc_elementor_integration = new GSC_Elementor_Integration();
