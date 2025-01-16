<?php
/*
 * Google Sheet configuration and settings page
 * @since 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit();
}

$args = array(
    'post_type' => array('post', 'page', 'elementor_library'), // Include post, page, and elementor_library post types
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_elementor_data',
            'compare' => 'EXISTS'
        )
    )
);
global $wpdb;

$forms_query = new WP_Query($args);
$all_elementor_forms = $forms_query->posts;

$feed = $wpdb->prefix . 'postmeta';
$feedList = $wpdb->get_results("SELECT * FROM $feed WHERE `meta_value`='gscele_form_feeds'");

// Check if the user is authenticated
$authenticated = get_option('elefgs_token');
$authenticatedManual = get_option('elefgs_token_manual');
$elegs_manual_setting = get_option('elefgs_manual_setting');
$ele_per = get_option('elefgs_verify');
$show_setting = 0;
$selected_method ='';    
if($elegs_manual_setting == 0){
$selected_method = __('Use Existing Client/Secret Key (Auto Google API Configuration)', 'gsheetconnector-for-elementor-forms-pro');
      }
elseif($elegs_manual_setting == 1){
    $selected_method = __('Use Manual Client/Secret Key (Use Your Google API Configuration)', 'gsheetconnector-for-elementor-forms-pro');
     }
// check user is authenticated when save existing api method
 if ((!empty($authenticated) && $ele_per == 'valid' && $elegs_manual_setting == 0) ) {
        $show_setting = 1;
    }
    //check user is authenticated when save manual api method
   elseif(!empty($authenticatedManual) && $ele_per == 'valid' && $elegs_manual_setting == 1){
       $show_setting = 1;
      } 
else {
    ?>
    
        <?php
         echo "<p class='elementor-gs-display-note'>".__('<strong>Make Sure, your selected Method is:</strong>  '.$selected_method.'<br><br> <strong>Authentication Required:</strong>
                  You must have to <a href="admin.php?page=gsheetconnector-elementor-config&tab=integration" target="_blank">Authenticate using your Google Account</a> along with Google Drive and Google Sheets Permissions in order to enable the settings for configuration.</p>', 'gsheetconnector-for-elementor-forms-pro')."</h3>";

      
        ?>
  
    <?php
}


if ($show_setting == 1) {
    ?>
   
    <!-- feed message display block -->
    <div class="feed-error-message" style="display:none;"></div>
    <div class="feed-success-message" style="display:none;"></div>

    <div class="elementor-main">
        <div class="elementor-row">
            <div>
                <button class="elementor-btn" id="add-new-feed">
                   <?php echo __('Add Feeds', 'gsheetconnector-for-elementor-forms-pro'); ?>
                </button>
                <button class="elementor-close-btn" id="close-feed" style="display:none">
                    <?php echo __('Close Feeds', 'gsheetconnector-for-elementor-forms-pro'); ?> 
                </button>
            </div>

            <div class="add-feed-form">
                <form method="post">
                    <label for="feed_name"><?php echo __('Feed Name', 'gsheetconnector-for-elementor-forms-pro'); ?></label>
                    <input type="text" id="feed_name" class="feedName" name="feed_name" />
                    
                    <label for="elementor_form_select"><?php echo __('Select Form:', 'gsheetconnector-for-elementor-forms-pro'); ?></label>
                    <select id="elementor_form_select" name="elementorforms" class="elementorForms">
                        <option value=""><?php echo __('Select Form', 'gsheetconnector-for-elementor-forms-pro'); ?></option>

                        <?php
                        // Function to recursively extract form data from Elementor structure
                        function extract_elementor_forms($data) {
                            $forms = [];
                            foreach ($data as $element) {
                                if (isset($element['widgetType']) && $element['widgetType'] === 'form') {
                                    $forms[] = [
                                        'form_name' => $element['settings']['form_name'] ?? __('Unnamed Form', 'gsheetconnector-for-elementor-forms-pro'),
                                        'element_id' => $element['id'] ?? __('Unknown Element ID', 'gsheetconnector-for-elementor-forms-pro')
                                    ];
                                }
                                if (isset($element['elements']) && is_array($element['elements'])) {
                                    $forms = array_merge($forms, extract_elementor_forms($element['elements']));
                                }
                            }
                            return $forms;
                        }

                        // Loop through all Elementor posts (pages, posts, templates)
                        foreach ($all_elementor_forms as $f) {
                            $form_id = $f->ID;
                            $elementor_data = get_post_meta($form_id, '_elementor_data', true);
                            if ($elementor_data && ($data = json_decode($elementor_data, true)) && is_array($data)) {
                                $forms = extract_elementor_forms($data);
                                
                                // Loop through each form found in this post
                                foreach ($forms as $form) {
                                    $form_name = $form['form_name'];
                                    $element_id = $form['element_id'];

                                    // Determine the source of the form
                                    $form_source = $f->post_type == 'elementor_library' && get_post_meta($f->ID, '_elementor_template_type', true) == 'popup' 
                                        ? 'Popup: ' 
                                        : 'Page/Post: ';

                                    // Output the form in the dropdown
                                    ?>
                        <option value="<?php echo esc_attr($form_id); ?>">
                            <?php echo esc_html($form_source . $form_name . ' (Element ID: ' . $element_id . ')'); ?>
                        </option>
                        <?php } } } ?>

                        <?php 
                        // Fetch MetForm Forms (unchanged)
                        $metforms = get_posts(array(
                            'post_type' => 'metform-form',
                            'numberposts' => -1
                        ));
                        
                        if (!empty($metforms)) {
                            foreach ($metforms as $metform) { ?>
                                <option value="<?php echo $metform->ID; ?>"><?php echo __('MetForm: ', 'gsheetconnector-for-elementor-forms-pro') . $metform->post_title; ?></option>
                            <?php }
                        }
                        ?>
                    </select>
                    
                    <input type="hidden" name="elementorform-ajax-nonce" id="elementorform-ajax-nonce" value="<?php echo wp_create_nonce('elementorform-ajax-nonce'); ?>" />
                    <input type="button" name="execute-submit-feed-elementor" class="elementor-gs-sub-btn" value="<?php echo __('Submit', 'gsheetconnector-for-elementor-forms-pro'); ?>">
                    <span class="fld-fetch-load">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                </form>
            </div>
        </div>

        <div class="elementor-feeds-list">
            <table border="1" id="elementorformtable">
                <?php
                $feeds_per_page = 10; // Limit feeds per page
                $current_page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
                $total_feeds = count($feedList); // Total number of feeds
                $total_pages = ceil($total_feeds / $feeds_per_page);

                // Slice the feed list for the current page
                $start_index = ($current_page - 1) * $feeds_per_page;
                $current_feeds = array_slice($feedList, $start_index, $feeds_per_page);

                // Define total items for pagination
                $total_items = $total_feeds; // This should be the total number of feeds

                if (!empty($current_feeds)) {
                    ?>
                    <tr>
                        <th><?php echo __('Sr No', 'gsheetconnector-for-elementor-forms-pro'); ?></th>
                        <th><?php echo __('Page ID', 'gsheetconnector-for-elementor-forms-pro'); ?></th>
                        <th><?php echo __('Feed Name', 'gsheetconnector-for-elementor-forms-pro'); ?></th>
                        <th><?php echo __('Form Name', 'gsheetconnector-for-elementor-forms-pro'); ?></th>                        
                        <th><?php echo __('Page Name', 'gsheetconnector-for-elementor-forms-pro'); ?></th>
                    </tr>
                    <?php
                    foreach ($current_feeds as $key => $value) {
                        $sr_no = $start_index + $key + 1; // Serial number for current page
                        $post_title = get_the_title($value->post_id);
                        $form_id = $value->post_id;

                        // Fetch the form name
                        $elementor_data = get_post_meta($form_id, '_elementor_data', true);
                        $form_name = '';
                        if ($elementor_data) {
                            $data = json_decode($elementor_data, true);
                            if (is_array($data)) {
                                $form_name_result = get_form_name($data);
                                if (is_array($form_name_result)) {
                                    $form_name = implode(', ', $form_name_result);
                                } else {
                                    $form_name = $form_name_result;
                                }
                            }
                        }
                        $form_title = $form_name ? $form_name : 'Unnamed Form';

                        ?>
                        <tr id="feed-<?php echo $value->meta_id; ?>">
                            <td><?php echo $sr_no; ?></td>
                            <td><?php echo esc_html($value->post_id); ?></td>
                            <td>
                                <div class="feed-info">
                                    <div class="feed-title"><?php echo esc_html($value->meta_key); ?></div>
                                    <div class="feed-edit-option">
                                        <a href="?page=gsheetconnector-elementor-config&tab=form_feed_settings&form_id=<?php echo $value->post_id; ?>&feed_id=<?php echo $value->meta_id; ?>">Edit</a>
                                        <a href="#" class="delete elementor-gs-btn delete-feed" data-form-id="<?php echo $value->post_id; ?>" data-feed-id="<?php echo $value->meta_id; ?>">Delete</a>
                                        <a href="<?php echo get_permalink($value->post_id); ?>" target="_blank">View</a>
                                    </div>
                                    <span style="overflow: hidden;" class="loading-sign-delete-feed-elegs<?php echo $value->meta_id ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </div>
                            </td>
                            <td><?php echo esc_html($form_title); ?></td>
                            <td><?php echo esc_html($post_title); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="5"><h3><?php echo __('No feeds found', 'gsheetconnector-for-elementor-forms-pro'); ?></h3></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <!-- Pagination Links -->
            <div class="pagination">
                <span class="total-items"><?php echo sprintf(__('Total %d items', 'gsheetconnector-for-elementor-forms-pro'), $total_items); ?></span>

                <?php if ($current_page > 1) { ?>
                    <a href="?page=gsheetconnector-elementor-config&tab=form_feed_settings&paged=1" title="First Page" class="first-page"><?php echo __('« First', 'gsheetconnector-for-elementor-forms-pro'); ?></a>
                    <a href="?page=gsheetconnector-elementor-config&tab=form_feed_settings&paged=<?php echo $current_page - 1; ?>" title="Previous Page" class="prev-page"><?php echo __('‹', 'gsheetconnector-for-elementor-forms-pro'); ?></a>
                <?php } ?>

                <span><?php echo sprintf(__('Current Page %d of %d', 'gsheetconnector-for-elementor-forms-pro'), $current_page, $total_pages); ?></span>

                <?php if ($current_page < $total_pages) { ?>
                    <a href="?page=gsheetconnector-elementor-config&tab=form_feed_settings&paged=<?php echo $current_page + 1; ?>" class="next-page" title="Next Page"><?php echo __('›', 'gsheetconnector-for-elementor-forms-pro'); ?></a>
                    <a href="?page=gsheetconnector-elementor-config&tab=form_feed_settings&paged=<?php echo $total_pages; ?>" class="last-page" title="Last Page"><?php echo __('Last ›', 'gsheetconnector-for-elementor-forms-pro'); ?></a>
                <?php } ?>
            </div>

        </div>

    </div>
    <?php
}

function get_form_name($data) {
    foreach ($data as $widget) {
        if (is_array($widget) || is_object($widget)) {
            if (isset($widget['widgetType']) && $widget['widgetType'] === 'form') {
                $form_info = array();

                // Check for form name
                $form_info['form_name'] = isset($widget['settings']['form_name']) 
                    ? $widget['settings']['form_name'] 
                    : '';

                // Get element_id for the form
                $form_info['element_id'] = isset($widget['id']) 
                    ? $widget['id'] 
                    : '';

                return $form_info;
            }
            
            // If the widget has child elements, search within them
            if (isset($widget['elements']) && is_array($widget['elements'])) {
                $form_info = get_form_name($widget['elements']);
                if ($form_info) {
                    return $form_info;
                }
            }
        }
    }
    return null;
}

?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Ensure $ alias is used within this function scope

    // Hide the close-feed button initially
    $("#close-feed").hide();

    // Show the add-feed form and toggle buttons
    $(document).on('click', '#add-new-feed', function() {
        $(".add-feed-form").show();
        $("#add-new-feed").hide();
        $("#close-feed").show();
    });

    // Hide the add-feed form and toggle buttons
    $(document).on('click', '#close-feed', function(event) {
        event.preventDefault(); // Prevent default link behavior
        $("#add-new-feed").show();
        $("#close-feed").hide();
        $(".add-feed-form").hide();
    });
});
</script>
