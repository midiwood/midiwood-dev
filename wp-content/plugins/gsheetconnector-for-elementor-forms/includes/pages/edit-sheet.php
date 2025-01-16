<?php

$gsc_elementor_integration = new GSC_Elementor_Integration();

// Get the feed ID and form ID from the form
$feed_id = isset($_GET['feed_id']) ? filter_var($_GET['feed_id'], FILTER_SANITIZE_NUMBER_INT) : "";
$form_id = isset($_GET['form_id']) ? filter_var($_GET['form_id'], FILTER_SANITIZE_NUMBER_INT) : "";

// Get the saved feed data from the database
$feed_data = get_post_meta($feed_id, 'gscele_form_feeds', true);

$sheet_name = isset($feed_data['sheet-name']) ? esc_attr($feed_data['sheet-name']) : '';
$sheet_id = isset($feed_data['sheet-id']) ? esc_attr($feed_data['sheet-id']) : '';
$tab_name = isset($feed_data['sheet-tab-name']) ? esc_attr($feed_data['sheet-tab-name']) : '';
$tab_id = isset($feed_data['tab-id']) ? esc_attr($feed_data['tab-id']) : '';


?>

<div class="frmn-main-div">
    <div class="frmn-bread-crumb">
        <ul class="breadcrumb_frmntr">
            <li>
                <a href="?page=gsheetconnector-elementor-config&tab=form_feed_settings">
                    <button class="button button-secondary">
                        <span class="back-icon">&#8592;</span><?php echo __('Back to Feeds List', 'gsheetconnector-for-elementor-forms'); ?> 
                    </button>
                </a>
            </li>
        </ul>
    </div>
    <!-- form feed sheet settigns -->
    <form id="edit-feed-form" method="post" action="">

        <?php if (isset($_POST['execute-edit-feed-elementor']) && !empty($success_message)): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html($success_message); ?></p>
            </div>
        <?php endif; ?>

        <input type="hidden" id="feed-id" name="feed_id" value="<?php echo $feed_id; ?>">
        <input type="hidden" id="form-id" name="form_id" value="<?php echo $form_id; ?>">

        <!-- MANUAL METHOD OF 1st DiV -->
        <div class="manual-section <?php echo $class; ?>">
             <h2 class="info-headers"><?php echo esc_html(__('Edit Feed and Integrate with Google Sheets', 'gsheetconnector-for-elementor-forms')); ?></h2>
                <div class="field-row">
                    <label for="edit-sheet-name"><?php echo esc_html(__('Sheet Name', 'gsheetconnector-for-elementor-forms')); ?></label> 
                    <input type="text" id="edit-sheet-name" name="elementor-gs[sheet-name-custom]" value="<?php echo esc_attr($sheet_name); ?>">
                    <div class="tooltip-new">
                        <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
                        <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("Go to your google account and click on Google apps icon and than click Sheet, Select the name of the appropriate sheet you want to link your contact form or create new sheet.", "gsheetconnector-for-elementor-forms")); ?></span>
                    </div>
                </div> <!-- field row #end -->

            <div class="field-row">
                <label for="edit-sheet-id"><?php echo esc_html(__('Sheet ID', 'gsheetconnector-for-elementor-forms')); ?></label> 
               <input type="text" id="edit-sheet-id" name="elementor-gs[sheet-id-custom]" value="<?php echo esc_attr($sheet_id); ?>">
                <div class="tooltip-new">
                    <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
                    <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("you can get sheet id from your sheet URL", "gsheetconnector-for-elementor-forms")); ?></span>
                </div>
           </div> <!-- field row #end -->

           <div class="field-row">
                <label for="edit-tab-name"><?php echo esc_html(__('Tab Name', 'gsheetconnector-for-elementor-forms')); ?></label> 
                <input type="text" id="edit-tab-name" name="elementor-gs[sheet-tab-name-custom]" value="<?php echo esc_attr($tab_name); ?>">
                <div class="tooltip-new">
                    <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
                    <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("Open your Google Sheet with which you want to link your contact form . You will notice a tab names at bottom of the screen. Copy the tab name where you want to have an entry of contact form.", "gsheetconnector-for-elementor-forms")); ?></span>
                </div>
            </div> <!-- field row #end -->

            <div class="field-row">

                <label for="edit-tab-id"><?php echo esc_html(__('Tab ID', 'gsheetconnector-for-elementor-forms')); ?></label> 
                <input type="text" id="edit-tab-id" name="elementor-gs[tab-id-custom]" value="<?php echo esc_attr($tab_id); ?>">
                    <div class="tooltip-new">
                        <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
                        <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("you can get tab id from your sheet URL", "gsheetconnector-for-elementor-forms")); ?></span>
                    </div>
                </div> <!-- field row #end -->
              <div class="sheet-url" id="sheet-url" style="display: flex;">
            <?php if ((isset($sheet_id) && $sheet_id!="") && (isset($tab_id) && $tab_id!="")) {
                                     ?>
            <label><?php echo __('Google Sheet URL', 'gsheetconnector-for-elementor-forms'); ?> 
            </label> 
            <a class="sheet-url-elementor button-secondary" href="https://docs.google.com/spreadsheets/d/<?php echo $sheet_id; ?>/edit#gid=<?php echo $tab_id; ?>" target="_blank"><?php echo __('Sheet URL', 'gsheetconnector-for-elementor-forms'); ?></a>
               <?php
               }
               ?>
        </div>

       <input type="hidden" name="gs-ajax-nonce" id="gs-ajax-nonce" value="<?php echo esc_attr(wp_create_nonce('gs-ajax-nonce')); ?>" />
        <input type="submit" name="execute-edit-feed-elementor" id="execute-save" class="button button-primary" value="Save Changes">

</div>
 
    </form>
<!-- Free setting End -->

<div class="system-debug-logs" id="opener">
            <div class="auto-section" style="display:block;">
                
                <h2 class="oneoforall"><span><?php echo esc_html(__('Auto Google Sheet Settings :', 'gsheetconnector-for-elementor-forms')); ?></span><span class="pro-ver"><?php echo __('PRO', 'gsheetconnector-for-elementor-forms'); ?></span></h2>
                
                <div class="gs-fields">
                    
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                     <div class="sheet-details <?php echo $class; ?>">
                        <p>
                            <label><?php echo esc_html(__('Google Spreadsheet Name', 'gsheetconnector-for-elementor-forms')); ?></label>
                            <select name="elementor-gs[gs-elementor-sheet-id]" id="gs-elementor-sheet-id">
                                <option value=""><?php echo esc_html(__('Select', 'gsheetconnector-for-elementor-forms')); ?></option>
                                
                                <option value="create_new"><?php echo esc_html(__('Create New', 'gsheetconnector-for-elementor-forms')); ?></option>
                            </select>

                            <span class="tooltip-new">
                                <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
                                <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("Not fetching sheet details to the dropdown than add sheet and tab name manually.", "gsheetconnector-for-elementor-forms")); ?></span>
                            </span>
                            <span class="error_msg" id="error_spread"></span>
                            <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            
                        </p>
                        <i class="errorSelect errorSelectsheet"></i>
                    
                    <p>
                        <label><?php echo esc_html(__('Google Sheet Tab Name', 'gsheetconnector-for-elementor-forms')); ?></label>
                        <select name="elementor-gs[gs-sheet-tab-name]" id="gs-sheet-tab-name">
                            <option value=""><?php echo esc_html(__('Select', 'gsheetconnector-for-elementor-forms')); ?></option>
                            
                        </select>

                        <span class="tooltip-new">
                            <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
                            <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("Not fetching sheet Tab details to the dropdown than add sheet and tab name manually.", "gsheetconnector-for-elementor-forms")); ?></span>
                        </span>
                    </p>
         
                        </div>
                    
                     <!-- <input type="submit" name=""  class="button button-primary google-setting" value="Save Changes"> -->
                    
                    <div class="system-debug-logs" id="opener">
                    <i class="errorSelect errorSelecttabs"></i>

                    <div class="create-ss-wrapper" style="display: none;">
                        <label>
                            <?php echo esc_html(__('Create Spreadsheet', 'gsheetconnector-for-elementor-forms')); ?>
                        </label>
                        <input type="text" name="_gs_elementor_setting_create_sheet" value="" id="_gs_elementor_setting_create_sheet">
                        <span class="error_msg" id="error_new_spread"></span>
                    </div>

                    <p id="gs-validation-message"></p>
                    <p id="gs-valid-message"></p>

                    <?php if (!empty(get_option('elefgs_verify')) && (get_option('elefgs_verify') == "valid")) { ?>
                        <p class="gscelementorform-sync-row">
                          <?php echo __('<a id="gscelementorform-sync" data-init="yes"class="sync-button"> Click Here </a> to fetch spreadsheet details.', 'gsheetconnector-for-elementor-forms'); ?><span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </p>
                    <?php } ?>
                </div>
            </div>

       </div>      

        <?php
        // Retrieve form data from post meta using form ID
        $form_data = get_post_meta($form_id, '_elementor_data', true);

        $field_labels = [];
        $field_labels['Entry ID'] = 'Entry ID';

        // Ensure $form_data is valid and has expected structure
        if ($form_data) {
            $form_data = json_decode($form_data, true);

            $elements = $this->get_elements_form_data($form_data, "form_fields");

            if (!empty($elements)) {
                foreach ($elements as $field) {
                    if (isset($field['field_label'])) {
                        // Decode HTML entities and strip tags for safe display
                        $field_label = htmlspecialchars_decode(strip_tags($field['field_label']));
                        $field_labels[$field_label] = $field_label;
                    }
                }
            }
        }

        // Array of default WordPress mail tags
        $default_mail_tags = [
            'Entry Date' => 'Entry Date',
            'Post ID' => 'Post ID',
            'User Name' => 'User Name',
            'User IP' => 'User IP',
            'User Agent' => 'User Agent',
            'User ID' => 'User ID',
            'Referrer' => 'Referrer',
            // Add more default mail tags as needed
        ];

        $merged_fields = array_merge($field_labels, $default_mail_tags);

        // Displaying the fields in the interface
        ?>

        <div class="form-fields-list elementor-list-set">
            <div class="elementorgs-color-code">
                <div class="color-elementorgs">
                    <h2>Field List | Special Mail Tags <span class="pro-ver"><?php echo __('PRO', 'gsheetconnector-for-elementor-forms'); ?></span></h2>
                </div>
               
            </div>

            <div class="toggle-button select-all-toggle">
                <label class="switch">
                    <input type="checkbox" id="select-all-checkbox">
                    <span class="slider round"></span>
                </label>
                <span class="label-text"><?php echo __('Select All', 'gsheetconnector-for-elementor-forms'); ?></span>
            </div>

            <div id="sortable">
                <?php
                // Initialize variables to prevent warnings
                $header_name = ''; // Default value for header name
                $is_selected = ''; // Default value for checkbox status
                $non_sortable_class = ''; // Default value for non-sortable class

                foreach ($merged_fields as $key => $field) {
                    $field_label = $key;

                    // Check if the field is a special mail tag
                    if (in_array($key, ['Entry ID', 'Entry Date', 'Post ID', 'User Name', 'User IP', 'User Agent', 'User ID', 'Referrer'])) {
                        $non_sortable_class = ' non-sortable';
                        echo '<div class="field-item special_mail_tags_bg' . esc_attr($non_sortable_class) . '">';
                    } else {
                        $non_sortable_class = ''; // Reset class for non-special fields
                        echo '<div class="field-item field_list_bg' . esc_attr($non_sortable_class) . '">';
                    }

                    // Example logic for checkbox selection (you may modify based on actual logic)
                    $is_selected = isset($_POST['sheet_header'][$key]) ? 'checked' : '';

                    echo '<label class="switch">';
                    echo '<input type="checkbox" name="sheet_header[' . esc_attr($key) . ']" value="1" ' . $is_selected . '>';
                    echo '<span class="slider round"></span>';
                    echo '</label>';
                    echo '<span class="label-text">' . esc_html($key) . '</span>';

                    // Placeholder and value logic for header name
                    $header_name = isset($_POST['sheet_header'][$key]) ? sanitize_text_field($_POST['sheet_header'][$key]) : $key;
                    echo '<input type="text" name="sheet_header[' . esc_attr($key) . ']" placeholder="' . esc_attr($key) . '" value="' . esc_attr($header_name) . '">';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
 <!-- Field List #end -->         
                
                
        <div class="form-fields-list elementor-list-set">
                
            <div class="header-manage field-list">

                <h2 class="elementor-title lbl_heading">
                    <?php echo esc_html(__('Header Management ', 'gsheetconnector-for-elementor-forms')); ?></h2>

                <!-- Freeze header START-->
                <div class="misc-head">
                    
                    <div class="toggle-button sheet_formatting-header-toggle">
                        <label for="freeze-header-option" class="switch"  >
                        <input type="checkbox" name="elementor-gs[freeze_header]" id="freeze-header-option"  class="check-toggle-elemgsc" value="">

                            <span class="slider round"></span>
                        </label>
                            <span class="label-text"><?php echo __('Freeze Header Row', 'gsheetconnector-for-elementor-forms'); ?></span>
                            <span class="pro-ver"><?php echo __('PRO', 'gsheetconnector-for-elementor-forms'); ?></span>
                    </div>
                
                    <span class="tooltip-new">
                        <img src="<?php echo GS_CONN_ELE_URL; ?>assets/img/help.png" class="help-icon">
                        <span
                            class="tooltiptext tooltip-right-msg"><?php echo __("Freeze First Header Row.", "gsheetconnector-for-elementor-forms"); ?>

                        </span>
                    </span>
                </div>
                <!-- Freeze header END-->

                    <!-- Colors START-->
                    <div class="sheet_formatting">
                        <div class="elemgsc-sheet_formatting elemgsc-sheet_formatting">
                            <div class="toggle-button sheet_formatting-header-toggle">
                                <label class="switch" for="sheet_formatting-header-checkbox">
                                    <input type="checkbox" id="sheet_formatting-header-checkbox" name="elementor-gs[sheet_formatting_header]" value="1" >
                                    <span class="slider round"></span>
                                </label>
                                <span class="label-text"><?php echo __('Header - Font Settings', 'gsheetconnector-for-elementor-forms'); ?></span>
                                <span class="pro-ver"><?php echo __('PRO', 'gsheetconnector-for-elementor-forms'); ?></span>
                                <span class="tooltip-new">
                                    <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon" alt="Help">
                                    <span class="tooltiptext tooltip-right-msg"><?php echo __("This feature locks the top row in your selected sheet, providing a consistent reference point as you scroll through your data.", "gsheetconnector-for-elementor-forms"); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Colors END-->

                    <!-- 4th Div OF PRO Sheet Background Colors -->
                    <div class="back-formating">
                        
                            <div class="toggle-button sheet-bg-toggle">
                                <label class="switch">
                                    <input type="checkbox" id="sheet-bg-toggle-checkbox" name="elementor-gs[sheet_bg]" value="1" >
                                    <span class="slider round"></span>
                                </label>
                                <span class="label-text"><?php echo __('Sheet Background Color', 'gsheetconnector-for-elementor-forms'); ?></span>
                                <span class="pro-ver"><?php echo __('PRO', 'gsheetconnector-for-elementor-forms'); ?></span>
                                <span class="tooltip-new">
                                    <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
                                    <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("Apply colors to the entire sheet, distinguishing odd and even rows for enhanced readability.", 'gsheetconnector-for-elementor-forms')); ?></span>
                                </span>
                            </div>
                    </div>

                    <!--Row Font Setings 4th div of header settigns  -->
                    <!-- FOR DATA OF FORM STYLES -->
                    <div class="sheet_formatting">  
                        <div class="elemgsc-sheet_formatting_row elemgsc-sheet_formatting_row">
                            <div class="toggle-button sheet_formatting-row-toggle">
                                <label class="switch" for="sheet_formatting-row-checkbox">
                                    <input type="checkbox" id="sheet_formatting-row-checkbox" name="elementor-gs[sheet_formatting_row]" value="1" >
                                    <span class="slider round"></span>
                                </label>
                                <span class="label-text"><?php echo __('Row - Font Settings', 'gsheetconnector-for-elementor-forms'); ?></span>
                                <span class="pro-ver"><?php echo __('PRO', 'gsheetconnector-for-elementor-forms'); ?></span>
                                <span class="tooltip-new">
                                    <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon" alt="Help">
                                    <span class="tooltiptext tooltip-right-msg"><?php echo esc_html(__("This feature locks the top row in your selected sheet, providing a consistent reference point as you scroll through your data.", 'gsheetconnector-for-elementor-forms')); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
            </div>
            
    </div> <!-- header management #end -->      
            
    </form>

        <div class="header-manage">
    <?php

    global $wpdb;

    // Ensure form ID is passed
    $form_id = isset($_GET['form_id']) ? filter_var($_GET['form_id'], FILTER_SANITIZE_NUMBER_INT) : "";


    if ($form_id) {
        // Fetch the earliest entry date for the specific form ID
        $from_date = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MIN(`created_at`) FROM {$wpdb->prefix}e_submissions WHERE `post_id` = %d",
                $form_id
            )
        );

        // Fetch the latest entry date for the specific form ID
        $to_date = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MAX(`created_at`) FROM {$wpdb->prefix}e_submissions WHERE `post_id` = %d",
                $form_id
            )
        );
    } else {
        // Default to current date if no form ID or entries exist
        $from_date = $to_date = date('Y-m-d');
    }

    // Format the dates or set defaults if no entries exist
    $from_date = $from_date ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d');
    $to_date = $to_date ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
    ?>

    <h3>
        <?php echo esc_html(__('Sync Elementor form submissions with Google Sheets.', 'gsheetconnector-for-elementor-forms')); ?>
    </h3>

    <span class="sync-elemgsc-msg"></span>

    <div class="sync-date">

        <label for='sync-from-date'><?php echo __('From Date', 'gsheetconnector-for-elementor-forms'); ?></label>
        <span class="tooltip">
            <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
            <!-- Tooltip for From Date -->
            <span class="tooltiptext tooltip-right-msg">
                <?php echo esc_html(__("Select the starting date for the sync. The From Date determines the lower limit of the date range.", "gsheetconnector-for-elementor-forms")); ?>
            </span>
        </span>
        <input type='date' id='sync-from-date' name='sync_from_date' value="<?php echo $from_date; ?>" min="<?php echo $from_date; ?>" max="<?php echo $to_date; ?>" class='wpgs-date-picker'>

        <label for='sync-to-date'><?php echo __('To Date', 'gsheetconnector-for-elementor-forms'); ?>:</label>
        <span class="tooltip">
            <img src="<?php echo esc_url(GS_CONN_ELE_URL . 'assets/img/help.png'); ?>" class="help-icon">
            <!-- Tooltip for To Date -->
            <span class="tooltiptext tooltip-right-msg">
                <?php echo esc_html(__("Select the ending date for the sync. The To Date determines the upper limit of the date range.", "gsheetconnector-for-elementor-forms")); ?>
            </span>
        </span>
        <input type='date' id='sync-to-date' name='sync_to_date' value="<?php echo $to_date; ?>" min="<?php echo $from_date; ?>" max="<?php echo $to_date; ?>" class='wpgs-date-picker'>

        <div class="sync_div_design syncronous_elemgsc_form_entry_gsheet">
            <!-- <span class="dashicons dashicons-image-rotate-right "></span> -->
            <h4><?php echo __('Sync Entries', 'gsheetconnector-for-elementor-forms'); ?></h4>
            <span class="sync-elemgsc-load">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
    </div> 

    <input type="hidden" id="form-id" value="<?php echo esc_attr($form_id); ?>">
    <input type="hidden" id="feed-id" value="<?php echo esc_attr($feed_id); ?>">

    <input type="hidden" name="elementor-sync-gs-ajax-nonce" id="elementor-sync-gs-ajax-nonce" value="<?php echo wp_create_nonce('elementor-sync-gs-ajax-nonce'); ?>" />
    
    <?php ?>

    <?php ?>
     
<!-- popup file include herre -->
<?php include( GS_CONN_ELE_PATH . "includes/pages/pro-popup.php" ) ;?>