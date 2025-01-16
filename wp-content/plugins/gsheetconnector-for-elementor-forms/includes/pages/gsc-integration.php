<div class="main-promotion-box"> <a href="#" class="close-link"></a>
  <div class="promotion-inner">
    <h2>A way to connect WordPress <br />
      and <span>Google Sheets Pro</span></h2>
    <p class="ratings">Ratings : <span></span></p>
    <p>The Most Powerful Bridge Between WordPress  and <strong>Google Sheets</strong>, <br />
      Now available for popular <strong>Contact Forms</strong>, <strong>Page Builder Forms</strong>,<br />
      and <strong>E-commerce</strong> Platforms like <strong>WooCommerce</strong> <br />
      and <strong>Easy Digital Downloads</strong> (EDD).</p>
    <div class="button-bar"> <a href="https://www.gsheetconnector.com/" target="_blank">Buy Now</a> <a href="https://demo.gsheetconnector.com/" target="_blank">Check Demo</a> </div>
  </div>
  <div class="gsheet-plugins"></div>
</div> <!-- main-promotion-box #end -->
<?php
$ele_client_id      = get_option('elefgs_client_id');
$ele_secret_id      = get_option('elefgs_secret_id');
$ele_code_db        = get_option('elefgs_access_code');
$ele_manual_code_db = get_option('elefgs_access_manual_code');
$ele_manual_setting = get_option('elefgs_manual_setting');
$token = get_option('elefgs_token');
$header = admin_url('admin.php?page=gsheetconnector-elementor-config');

if (isset($_GET['code']) && ($ele_manual_setting == 0)) {
    $Code = sanitize_text_field($_GET["code"]);
    $header = admin_url('admin.php?page=gsheetconnector-elementor-config&fetchsheetDataEle=1');
}else{
    $Code = "";
}

?>
<input type="hidden" name="redirect_auth_wc" id="redirect_auth_wc" value="<?php echo (isset($header)) ?$header:''; ?>">
<div class="wrap elem-gs-form">
  <div class="card" id="elem-googlesheet">
    <h2 class="title"><?php echo esc_html(__('Elementor - Google Sheet Integration', 'gsheetconnector-for-elementor-forms')); ?> </h2>
    <br class="clear">
    <div class="card-wp dropdownoption">
      <label for="ele_dro_option" class="ele_gapi"><?php echo esc_html(__('Choose Google API Setting :', 'gsheetconnector-for-elementor-forms')); ?></label>
      <select id="ele_dro_option" name="ele_dro_option">
            <option value="cf7gs_existing" selected><?php echo esc_html__('Use Existing Client/Secret Key (Auto Google API Configuration)', 'gsconnector'); ?></option>
            <option value="cf7gs_manual" disabled=""><?php echo esc_html__('Use Manual Client/Secret Key (Use Your Google API Configuration) (Upgrade To PRO)', 'gsconnector'); ?></option>
      </select>
      <p class="int-meth-btn-ele">
        <a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank">
            <input type="button" name="save-method-api-element" id="save-method-api-element"
                   value="<?php _e('Upgrade to PRO', 'gsheetconnector-for-elementor-forms'); ?>" class="button button-primary" />
        </a>

        <span class="loading-sign-method-api">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </p>
    </div>
    <input type="hidden" name="fetchsheetDataEle" id="fetchsheetDataEle"
            value="<?php echo isset($_GET['fetchsheetDataEle']) ? esc_attr($_GET['fetchsheetDataEle']) : 0 ?>">
    <input type="hidden" name="redirect_auth_eleforms" id="redirect_auth_eleforms"
            value="<?php echo (isset($header)) ? esc_url($header):''; ?>">
    <input type="hidden" name="get_code" id="get_code"
            value="<?php echo (isset($_GET['code']) && sanitize_text_field($_GET['code']) != "") ? '1' : '0'; ?>">
    <input type="hidden" name="ele_manual_setting" id="ele_manual_setting"
            value="<?php echo esc_attr($ele_manual_setting); ?>">
    <input type="hidden" name="gs-ajax-nonce-ele" id="gs-ajax-nonce-ele"
            value="<?php echo wp_create_nonce('gs-ajax-nonce-ele'); ?>" />
    <?php if($ele_manual_setting == 0){ ?>
    <div class="card-wp ele_api_existing_setting">
      <div class="elem-inside">
        <?php if (empty($token) && $token == "") { ?>
        <?php if (empty($Code)) { ?>
        <div class="wpform-gs-alert-kk" id="google-drive-msg">
          <p class="wpform-gs-alert-heading"><?php echo esc_html__('To authenticate with your Google account, follow these steps:', 'gsheetconnector-for-elementor-forms'); ?></p>
          <ol class="wpform-gs-alert-steps">
            <li><?php echo esc_html__('Click on the "Sign In With Google" button.', 'gsheetconnector-for-elementor-forms'); ?></li>
            <li><?php echo esc_html__('Grant permissions for the following:', 'gsheetconnector-for-elementor-forms'); ?>
              <ul class="wpform-gs-alert-permissions">
                <li><?php echo esc_html__('Google Drive', 'gsheetconnector-for-elementor-forms'); ?></li>
                <li><?php echo esc_html__('Google Sheets', 'gsheetconnector-for-elementor-forms'); ?> <span><?php echo esc_html__('* Ensure that you enable the checkbox for each of these services.', 'gsheetconnector-for-elementor-forms'); ?></span></li>
              </ul>
            </li>
            <li><?php echo esc_html__('This will allow the integration to access your Google Drive and Google Sheets.', 'gsheetconnector-for-elementor-forms'); ?></li>
          </ol>
        </div>
        <?php } ?>
        <?php } ?>
        <p class="gs-integration-box">
          <label><?php echo esc_html(__('Google Access Code : ', 'gsheetconnector-for-elementor-forms')); ?></label>
          <?php
                
                if (!empty($token) && $token !== "") {
                    ?>
          <input type="text" name="ele-code" id="ele-code" value="" disabled
                        placeholder="<?php echo esc_html(__('Currently Active', 'gsheetconnector-for-elementor-forms')); ?>" />
          <input type="button" name="deactivate-log-ele" id="deactivate-log-ele"
                        value="<?php _e('Deactivate', 'gsheetconnector-for-elementor-forms'); ?>" class="button button-primary" />
          <span class="tooltip"> <img src="<?php echo GS_CONN_ELE_URL; ?>assets/img/help.png"
                            class="help-icon"> <span class="tooltiptext tooltip-right">On deactivation, all your data
          saved with authentication will be removed and you need to reauthenticate with your google
          account.</span></span> <span class="loading-sign-deactive">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
          <?php } else { 
                    $redirct_uri = admin_url( 'admin.php?page=gsheetconnector-elementor-config' );
    ?>
          <input type="text" name="ele-code" id="ele-code" value="<?php echo esc_attr($Code); ?>"
                        placeholder="<?php echo esc_html(__('Click Sign in with Google ->', 'gsheetconnector-for-elementor-forms')); ?>"disabled />
          <?php if (empty($Code)) { ?>
          <a href="https://oauth.gsheetconnector.com/index.php?client_admin_url=<?php echo $redirct_uri;  ?>&plugin=woocommercegsheetconnector"
                               style="position:relative; top:3px;"><img
                                    src="<?php echo GS_CONN_ELE_URL ?>/assets/img/btn_google_signin_dark_pressed_web.gif"></a>
          <?php } ?>
          <?php } ?>
          <?php if (!empty($_GET['code'])) { ?>
          <button type="button" name="save-ele-code" id="save-ele-code"><?php echo esc_html(__('Save & Authenticate', 'gsheetconnector-for-elementor-forms')); ?></button>
          <?php } ?>
          <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </p>
        <?php
        //resolved - google sheet permission issues - START
       if (!empty(get_option('elefgs_verify')) && (get_option('elefgs_verify') == "invalid-auth")) {
                        ?>
        <p style="color:#c80d0d; font-size: 14px; border: 1px solid;padding: 8px;">
         <?php echo  esc_html(__('Something went wrong! It looks you have not given the permission of Google Drive and Google Sheets from your google account.Please Deactivate Auth and Re-Authenticate again with the permissions.', 'gsheetconnector-for-elementor-forms')); ?>
        </p>
        <p style="color:#c80d0d;border: 1px solid;padding: 8px;"><img width="350px"
                        src="<?php echo GS_CONN_ELE_URL; ?>assets/img/permission_screen.png"></p>
        <p style="color:#c80d0d; font-size: 14px; border: 1px solid;padding: 8px;"> <?php echo esc_html(__('Also,', 'gsheetconnector-for-elementor-forms')); ?><a href="https://myaccount.google.com/permissions"
                        target="_blank"> 
       <?php echo esc_html(__('Click Here ', 'gsheetconnector-for-elementor-forms')); ?></a> 
       <?php echo esc_html(__('and if it displays "GSheetConnector for WordPress Contact Forms" under Third-party apps with account access then remove it.', 'gsheetconnector-for-elementor-forms')); ?> 
       </p>
        <?php
                }
       //resolved - google sheet permission issues - END
                else{
         // connected-email-account
                            $token = get_option('elefgs_token');
                            if (!empty($token) && $token !== "") {
                                $google_sheet = new GSC_Elementor_Free();
                                $email_account = $google_sheet->gsheet_print_google_account_email();

                            if ($email_account) {
                                    ?>
        <p class="connected-account"> <?php printf(__('Connected email account: %s', 'gsheetconnector-for-elementor-forms'), $email_account); ?>
        <p>
          <?php } else { ?>
        <p style="color:red"> <?php echo esc_html(__('Something wrong ! Your Auth Code may be wrong or expired. Please deactivate and do Re-Authentication again. ', 'gsheetconnector-for-elementor-forms')); ?> </p>
        <?php
                                }
                            }
                        }
                        ?>
        <?php 
          if(!empty(get_option('elefgs_verify')) && (get_option('elefgs_verify') =="valid")){ ?>
        <p class="ele-sync-row"> <?php echo __('<a id="ele-sync" data-init="yes">Click here </a> to fetch Sheet details to be set at Elementor Forms Google Sheet settings.', 'gsheetconnector-for-elementor-forms'); ?> <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
        <?php } 
              
                ?>
        <div id="elemnt-gsc-cta" class="elemnt-gsc-privacy-box">
          <div class="elemnt-gsc-table">
            <div class="elemnt-gsc-less-free">
              <p><i class="dashicons dashicons-lock"></i> <?php echo esc_html(__('We do not store any of the data from your Google account on our servers, everything is processed & stored on your server. We take your privacy extremely seriously and ensure it is never misused.', 'gsheetconnector-for-elementor-forms')); ?> <br />
                <a href="https://gsheetconnector.com/usage-tracking/" target="_blank" rel="noopener noreferrer">Learn more.</a> </p>
            </div>
          </div>
        </div>
        <p>
          <label><?php echo __('Debug Log ->', 'gsheetconnector-for-elementor-forms'); ?></label>
          <button class="elemnt-logs"><?php echo __('View', 'gsheetconnector-for-elementor-forms'); ?></button>
          <!-- <label><a href="<?php echo GS_CONN_ELE_URL . 'logs/log.txt'; ?>" target="_blank"
                            class="debug-view"><?php echo __('View', 'gsheetconnector-for-elementor-forms'); ?></a></label> -->
          <label><a class="debug-clear-elementor"><?php echo __('Clear', 'gsheetconnector-for-elementor-forms'); ?></a></label>
          <span class="clear-loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </p>
        <p id="gs-validation-message"></p>
        <span id="deactivate-message"></span> </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
          var googleDriveMsg = document.getElementById('google-drive-msg');
          if (googleDriveMsg) {
            // Check if the 'gfgs_token' option is not empty
            if ('<?php echo get_option('elefgs_token'); ?>' !== '') {
              googleDriveMsg.style.display = 'none';
            }
          }
        });
        </script>
    <?php } ?>
    
    <!-- Manual Settings START -->
    <?php if($ele_manual_setting == 1){ ?>
    <div class="card-wp ele_api_manual_setting">
      <div class="ele-in-fields elem-inside">
        <h2><span class="title1"><?php echo __(' Google - ', 'gsheetconnector-for-elementor-forms'); ?></span><span
                        class="title"><?php echo __('  API Settings', 'gsheetconnector-for-elementor-forms'); ?></span></h2>
        <hr>
        <p class="ele-gs-alert-kk eleform-alert"> <?php echo __('Create new google APIs with Client ID and Client Secret keys to get an access for the google drive and google sheets. ', 'gsheetconnector-for-elementor-forms'); ?> </p>
        <p>
        <div class="ele_api_set">
          <div class="ele_api_option">
            <div class="ele_api_label">
              <label><?php echo __('Client Id', 'gsheetconnector-for-elementor-forms'); ?></label>
            </div>
            <div class="ele_api_input">
              <input type="text" name="ele-client-id" id="ele-client-id"
                                value="<?php echo esc_attr($ele_client_id); ?>" placeholder="" /
                                <?php echo (!empty(get_option('elefgs_token_manual')) && get_option('elefgs_token_manual') !== "") ? "disabled": "" ?>>
              <br>
            </div>
          </div>
          <div class="ele_api_option">
            <div class="ele_api_label">
              <label><?php echo __('Client Secret', 'gsheetconnector-for-elementor-forms'); ?></label>
            </div>
            <div class="ele_api_input">
              <input type="text" name="ele-secret-id" id="ele-secret-id"
                                value="<?php echo esc_attr($ele_secret_id); ?>" placeholder=""
                                <?php echo (!empty(get_option('elefgs_token_manual')) && get_option('elefgs_token_manual') !== "") ? "disabled": "" ?> />
            </div>
          </div>
          <?php 
          //resolved - google sheet permission issues - START
          if (!empty(get_option('elefgs_verify')) && (get_option('elefgs_verify') == "invalid-auth")) {
                        ?>
          <p style="color:#c80d0d; font-size: 14px; border: 1px solid;padding: 8px;"> 
            <?php echo esc_html(__('Something went wrong! It looks you have not given the permission of Google Drive and Google Sheets from your google account.Please Deactivate Auth and Re-Authenticate again with the permissions.', 'gsheetconnector-for-elementor-forms'));
             ?>
            </p>
          <p style="color:#c80d0d;border: 1px solid;padding: 8px;"><img width="350px"
                        src="<?php echo GS_CONN_ELE_URL; ?>assets/img/permission_screen.png"></p>
          <p style="color:#c80d0d; font-size: 14px; border: 1px solid;padding: 8px;"> <?php echo esc_html(__('Also,', 'gsheetconnector-for-elementor-forms')); ?><a href="https://myaccount.google.com/permissions"
                        target="_blank"> <?php echo esc_html(__('Click Here ', 'gsheetconnector-for-elementor-forms')); ?></a> 
                        <?php echo esc_html(__('and if it displays "GSheetConnector for WordPress Contact Forms" under Third-party apps with account access then remove it.', 'gsheetconnector-for-elementor-forms')); ?> 
          </p>
          <?php
                }
                //resolved - google sheet permission issues - END
                // connected email account
                else{
                if (!empty(get_option('elefgs_token_manual')) && get_option('elefgs_token_manual') !== "") {
                include_once( GS_CONN_ELE_ROOT . "/lib/google-sheets.php" );
                $google_sheet = new GSC_Elementor_Free();
                $email_account = $google_sheet->gsheet_print_google_account_email_manual(); 
                if( $email_account ) { ?>
          <div class="wg_api_option-ele">
            <div class="wg_api_label-ele">
              <label><?php echo __('Connected Email Account:', 'gsheetconnector-for-elementor-forms'); ?></label>
            </div>
            <div class="wg_api_input-ele">
              <p class="connected-account-manual-ele"> <?php printf( __( '%s', 'gsheetconnector-for-elementor-forms' ), $email_account ); ?>
              <p> 
            </div>
          </div>
          <?php }else{?>
          <p style="color:red"> <?php echo esc_html(__('Something wrong ! Your Auth code may be wrong or expired Please Deactivate and Do Re-Auth Code ', 'gsheetconnector-for-elementor-forms')); ?> </p>
          <?php 
                  }
                }      
                }   
               ?>
          <?php
            if (isset($_GET['code']))
                $ele_code = sanitize_text_field($_GET['code']);
            else
                $ele_code = "";
            ?>
          <?php
            if ($ele_client_id != "" || $ele_secret_id != "") {
                if (!(empty($ele_manual_code_db))) {
                    $auth_butt_display = "none";
                    $auth_input_display = "block";
                } elseif (!empty($ele_code)) {
                    $auth_butt_display = "none";
                    $auth_input_display = "block";
                } else {
                    $auth_butt_display = "block";
                    $auth_input_display = "none";
                }
                ?>
          <div class="ele_api_option">
            <div class="ele_api_label">
              <label><?php echo __('Client Token', 'gsheetconnector-for-elementor-forms'); ?></label>
            </div>
            <div class="ele_api_input">
            
              <input type="text"
                                value="<?php echo (!isset($ele_code) || $ele_code == "") && (isset($ele_manual_code_db) || $ele_manual_code_db != "") ? esc_attr($ele_manual_code_db) : esc_attr($ele_code) ?>"
                                name="ele-client-token" id="ele-client-token" placeholder=""
                                style="display: <?php echo esc_attr($auth_input_display); ?>" disabled/>
           
              <?php
                        if (get_option('elefgs_token_manual') !== '') {
                            include_once( GS_CONN_ELE_ROOT . "/lib/google-sheets.php" );
                            $ele_auth_url = GSC_Elementor_Free::getClient_auth(0, $ele_client_id, $ele_secret_id);
                            ?>
              <div class="ele_api_option_auth_url"
                                style="display: <?php echo esc_attr($auth_butt_display); ?>"> <a href="<?php echo esc_url($ele_auth_url); ?>" id="authlink_ele" target="_blank">
                <div class="ele-button-auth ele-button-secondary"> <?php echo esc_html__("Click here to generate an Authentication Token", 'gsheetconnector-for-elementor-forms'); ?> </div>
                </a> 
              </div>
              <?php } ?>
            </div>
          </div>
        
          <?php } ?>
          <div class="ele_api_option">
            <input type="button" class="ele-save" name="save-ele-manual" id="save-ele-manual" value="Save"
                            <?php echo (!empty(get_option('elefgs_token_manual')) && get_option('elefgs_token_manual') !== "") ? "disabled": "" ?>>
                            <?php  if ($ele_client_id != "" || $ele_secret_id != "") { ?>
  <input type="button" class="ele-deactivate-auth" name="ele-deactivate-auth" id="ele-deactivate-auth"
                        value="Deactivate"
                        style="display:<?php echo ($ele_manual_code_db != "") ? "block" : "none"; ?>">
     <?php } ?>
                            <?php  if ((empty($ele_manual_code_db))) { ?>
            <input type="reset" class="ele-reset" name="save-ele-reset" id="save-ele-reset" value="Reset">
             <?php } ?>
          </div>
          <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
         <span id="ele-validation-message"></span>
        <p id="deactivate-message"></p>
           </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>
<div class="elemnt-system-Error-logs" >
	<button id="copy-logs-btn" class="copy-log" onclick="copyLogs()"><?php echo esc_html__("Copy Logs", 'gsheetconnector-for-elementor-forms'); ?></button>	
  <div class="elemntdisplayLogs">
    <?php
            $existDebugFile = get_option('ele_gs_debug_log_file');
            // check if debug unique log file exist or not
            if (!empty($existDebugFile) && file_exists($existDebugFile)) {
              $displayelemntfreeLogs =  nl2br(file_get_contents($existDebugFile));
            if(!empty($displayelemntfreeLogs)){
               echo __($displayelemntfreeLogs, 'gsheetconnector-for-elementor-forms'); 
           
            }
            else{
              echo __('No errors found.', 'gsheetconnector-for-elementor-forms'); 
              
             }
        }
       else{
            // check if debug unique log file not exist
           echo __('No log file exists as no errors are generated.', 'gsheetconnector-for-elementor-forms'); 
          }
          ?>
  </div>
</div>

<script>
     function copyLogs() {
        // Get the log content from the element
        var logContentElement = document.getElementById('log-content');
        if (logContentElement) {
            var logContent = logContentElement.innerText || logContentElement.textContent;

            // Use the clipboard API to copy the log content
            navigator.clipboard.writeText(logContent).then(function() {
                alert('Logs copied to clipboard!');
            }).catch(function(err) {
                alert('Failed to copy logs: ' + err);
            });
        } else {
            alert('No logs to copy!');
        }
    }
</script>

<div class="two-col gsc-elemnt-box-help12">
  <div class="col gsc-elemnt-box12">
    <header>
      <h3><?php echo __('Next steps…', 'gsheetconnector-for-elementor-forms'); ?></h3>
    </header>
    <div class="gsc-elemnt-box-content12">
      <ul class="gsc-elemnt-list-icon12">
        <li> <a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank">
          <div>
            <button class="icon-button"> <span class="dashicons dashicons-star-filled"></span> </button>
            <strong><?php echo __('Upgrade to PRO', 'gsheetconnector-for-elementor-forms'); ?></strong>
            <p> <?php echo __('Capabilities/ Role Management ..', 'gsheetconnector-for-elementor-forms'); ?></p>
          </div>
          </a> </li>
        <li> <a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank">
          <div>
            <button class="icon-button"> <span class="dashicons dashicons-download"></span> </button>
            <strong><?php echo __('Compatibility', 'gsheetconnector-for-elementor-forms'); ?></strong>
            <p><?php echo __('Compatibility with Elementor-Forms Third-Party Plugins', 'gsheetconnector-for-elementor-forms'); ?></p>
          </div>
          </a> </li>
        <li> <a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank">
          <div>
            <button class="icon-button"> <span class="dashicons dashicons-chart-bar"></span> </button>
            <strong><?php echo __('Multi Languages', 'gsheetconnector-for-elementor-forms'); ?></strong>
            <p><?php echo __('This plugin supports multi-languages as well!', 'gsheetconnector-for-elementor-forms'); ?></p>
          </div>
          </a> </li>
        <li> <a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank">
          <div>
            <button class="icon-button"> <span class="dashicons dashicons-download"></span> </button>
            <strong><?php echo __('Support Wordpress multisites', 'gsheetconnector-for-elementor-forms'); ?></strong>
            <p><?php echo __('With the use of a Multisite, you’ll also have a new level of user-available: the Super Admin.', 'gsheetconnector-for-elementor-forms'); ?></p>
          </div>
          </a> </li>
      </ul>
    </div>
  </div>
  
  <!-- 2nd div -->
  <div class="col gsc-elemnt-box13">
    <header>
      <h3><?php echo __('Support Wordpress multisites', 'gsheetconnector-for-elementor-forms'); ?>Product Support</h3>
    </header>
    <div class="gsc-elemnt-box-content13">
      <ul class="gsc-elemnt-list-icon13">
        <li> <a href="https://www.gsheetconnector.com/elementor-forms-google-sheet-connector-pro" target="_blank"> <span class="dashicons dashicons-book"></span>
          <div> <strong><?php echo __('Online Documentation', 'gsheetconnector-for-elementor-forms'); ?></strong>
            <p><?php echo __('Understand all the capabilities of ELementor-Forms GSheetConnector', 'gsheetconnector-for-elementor-forms'); ?></p>
          </div>
          </a> </li>
        <li> <a href="https://www.gsheetconnector.com/support" target="_blank"> <span class="dashicons dashicons-sos"></span>
          <div> <strong><?php echo __('Ticket Support', 'gsheetconnector-for-elementor-forms'); ?></strong>
            <p><?php echo __('Direct help from our qualified support team', 'gsheetconnector-for-elementor-forms'); ?></p>
          </div>
          </a> </li>
        <li> <a href="https://www.gsheetconnector.com/affiliate-area" target="_blank"> <span class="dashicons dashicons-admin-links"></span>
          <div> <strong><?php echo __('Affiliate Program', 'gsheetconnector-for-elementor-forms'); ?></strong>
            <p><?php echo __('Earn flat 30% on every sale!', 'gsheetconnector-for-elementor-forms'); ?></p>
          </div>
          </a> </li>
      </ul>
    </div>
  </div>
</div>
<script>
		
 /*document.addEventListener("DOMContentLoaded", function() {
  var closeButton = document.querySelector('.close-link');
  var promotionBox = document.querySelector('.main-promotion-box');

  closeButton.addEventListener('click', function(event) {
    event.preventDefault();
    promotionBox.classList.add('hidden');
    // Store the state of hiding
    localStorage.setItem('isHidden', 'true');
  });

  // Check if the item is hidden in local storage
  var isHidden = localStorage.getItem('isHidden');
  if (isHidden === 'true') {
    promotionBox.classList.add('hidden');
  }

  // Listen for page refresh events
  window.addEventListener('beforeunload', function() {
    // Check if the box is hidden
    var isHiddenNow = promotionBox.classList.contains('hidden');
    // Store the state of hiding
    localStorage.setItem('isHidden', isHiddenNow ? 'true' : 'false');
  });

  // Reset hiding state on page refresh
  window.addEventListener('load', function() {
    localStorage.removeItem('isHidden');
    promotionBox.classList.remove('hidden');
  });
});*/


document.addEventListener("DOMContentLoaded", function() {
  var closeButton = document.querySelector('.close-link');
  var promotionBox = document.querySelector('.main-promotion-box');

  closeButton.addEventListener('click', function(event) {
    event.preventDefault();
    // Add URL to open in a new window
    var url = 'https://www.gsheetconnector.com/'; // Replace 'https://example.com' with your desired URL
    window.open(url, '_blank');
    
    // Hide the promotion box
    promotionBox.classList.add('hidden');
    
    // Store the state of hiding
    localStorage.setItem('isHidden', 'true');
  });

  // Check if the item is hidden in local storage
  var isHidden = localStorage.getItem('isHidden');
  if (isHidden === 'true') {
    promotionBox.classList.add('hidden');
  }

  // Listen for page refresh events
  window.addEventListener('beforeunload', function() {
    // Check if the box is hidden
    var isHiddenNow = promotionBox.classList.contains('hidden');
    // Store the state of hiding
    localStorage.setItem('isHidden', isHiddenNow ? 'true' : 'false');
  });

  // Reset hiding state on page refresh
  window.addEventListener('load', function() {
    localStorage.removeItem('isHidden');
    promotionBox.classList.remove('hidden');
  });
});



</script>
<?php  include( GS_CONN_ELE_ROOT . "/includes/pages/admin-footer.php" ) ;?>
