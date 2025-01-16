<?php
use Elementor\Plugin;
use Elementor\Settings_Page;
use Elementor\Settings;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class gsc_elementor_sidemenu extends Settings_Page {

    const PAGE_ID = 'gsheetconnector-elementor-config';

    public function __construct() {
        if (!Plugin::$instance->experiments->is_feature_active('admin_menu_rearrangement')) {
            add_action('admin_menu', [$this, 'register_admin_menu'], 100);
        }
    }

    protected function get_page_title() {
        return __('Google Sheet', 'gsheetconnector-for-elementor-forms');
    }

    public function register_admin_menu() {
        $sanitized_page_title = esc_html($this->get_page_title());

        add_submenu_page(
                Settings::PAGE_ID,
                $sanitized_page_title,
                $sanitized_page_title,
                'manage_options',
                self::PAGE_ID,
                [$this, 'display_settings_page']
        );
    }

    public function display_settings_page() {
        ?>
        <div class="wrap"><h1 class="wp-heading-inline"><?php echo esc_html($this->get_page_title()); ?></h1></div>       
        <?php
        $active_tab = (isset($_GET['tab']) && sanitize_text_field($_GET["tab"])) ? sanitize_text_field($_GET['tab']) : 'integration';

        $active_tab_name = '';
        if($active_tab ==  'integration'){
        $active_tab_name = 'Integration';
        }
       elseif($active_tab ==  'met_form_settings'){
       $active_tab_name = 'MetForm Settings';
       }
       elseif ($active_tab == 'form_feed_settings') {
            $active_tab_name = 'Form Feeds';
        }
      elseif($active_tab ==  'System_Status'){
     $active_tab_name = 'System Status';
       }

         $active_plugins = get_option('active_plugins');
         $parent_plugins_free1 = 'metform/metform.php';
         $met_active_plugins = "false";
         if (in_array($parent_plugins_free1, $active_plugins)){
            $met_active_plugins = "true";
       
         }
      $plugin_version = defined('GS_CONN_ELE_VERSION') ? GS_CONN_ELE_VERSION : 'N/A'; ?>


      <div class="gsheet-header">
			<div class="gsheet-logo">
				<a href="https://www.gsheetconnector.com/"><i></i></a></div>
			<h1 class="gsheet-logo-text"><span><?php echo esc_html( __('Elementor Forms GSheetConnector', 'gsheetconnector-for-elementor-forms' ) ); ?></span> <small><?php echo esc_html( __('Version :', 'gsheetconnector-for-elementor-forms' ) ); ?> <?php   echo esc_html($plugin_version, 'gsheetconnector-for-elementor-forms'); ?> </small></h1>
			<a href="https://support.gsheetconnector.com/kb" title="gsheet Knowledge Base" target="_blank" class="button gsheet-help"><i class="dashicons dashicons-editor-help"></i></a>
		</div>
        <p>
             <span class="dashboard-gsc"><?php echo esc_html( __('DASHBOARD', 'gsheetconnector-for-elementor-forms' ) ); ?></span>

    <span class="divider-gsc"> / </span>

    <span class="modules-gsc"> <?php echo esc_html( __($active_tab_name, 'gsheetconnector-for-elementor-forms' ) ); ?></span>
        </p>
       <div class="wrap">
            <?php
            $tabs = array(
                'integration' => __('Integration', 'gsheetconnector-for-elementor-forms'),
                'met_form_settings' => __('MetForm Settings', 'gsheetconnector-for-elementor-forms'),
                'form_feed_settings' => __('Form Feeds', 'gsheetconnector-for-elementor-forms-pro'),
                'System_Status' => __('System Status', 'gsheetconnector-for-elementor-forms'),
              
            );

            echo '<div id="icon-themes" class="icon32"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';
            foreach ($tabs as $tab => $name) {
             if($met_active_plugins == "false" && $name == "MetForm Settings"){
                   continue;
               }
                $class = ($tab == $active_tab) ? ' nav-tab-active' : '';
                echo "<a class='nav-tab$class' href='?page=gsheetconnector-elementor-config&tab=$tab'>$name</a>";
            }
            echo '</h2>';
			
			echo ' ';
			
            switch ($active_tab) {
                case 'integration':
                    include(GS_CONN_ELE_PATH . "includes/pages/gsc-integration.php");
                    break;
                case 'System_Status':
                    include(GS_CONN_ELE_PATH . "includes/pages/gsc-system_status.php");
                    break;
                case 'met_form_settings':
                    include(GS_CONN_ELE_PATH . "includes/pages/gsc-met-form-settings.php");
                    break;
                case 'form_feed_settings':
                    if (isset($_GET['form_id']) && isset($_GET['feed_id'])) {
                        $form_id = intval($_GET['form_id']);
                        $feed_id = intval($_GET['feed_id']);
                        include(GS_CONN_ELE_PATH . "includes/pages/edit-sheet.php");
                    } else {
                        include(GS_CONN_ELE_PATH . "includes/pages/gsc-feed-google-sheet.php");
                    }
                    break;
                 
            }
            ?>
        </div>
  <?php
    }

    protected function create_tabs() {
        
    }

    public function get_elements_form_data( $form_data, $keyToFind ) {
        foreach ($form_data as $key => $value) {
            // If the key matches, return the value
            if ($key === $keyToFind) {
                return $value;
            }

            // If the value is an array, recurse into it
            if (is_array($value)) {
                $result = $this->get_elements_form_data($value, $keyToFind);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        // If the key is not found, return null
        return null;
    }

}

// Initialize the google sheet connector class
$gsc_elementor_sidemenu = new gsc_elementor_sidemenu();