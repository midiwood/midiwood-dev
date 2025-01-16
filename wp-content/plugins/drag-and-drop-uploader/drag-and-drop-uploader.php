<?php
/**
 * Plugin Name: Drag and Drop File Uploader
 * Description: A simple drag and drop file uploader for WordPress, with Dropbox integration.
 * Version: 1.8
 * Author: Midiwood
 */

// Enqueue necessary scripts and styles
function ddu_enqueue_scripts() {
    wp_enqueue_style('ddu-style', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0', 'all');
    wp_enqueue_script('ddu-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.0', true);

    // Localize script to pass ajax_url and nonce
    wp_localize_script('ddu-script', 'ddu_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ddu_nonce' => wp_create_nonce('ddu_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'ddu_enqueue_scripts');

// Shortcode to display the uploader
function ddu_display_uploader() {
    ob_start();
    ?>
    <div id="ddu-dropbox">
        <p>Drag and drop files here</p>
    </div>
    <div id="ddu-upload-status"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('drag_and_drop_uploader', 'ddu_display_uploader');

// Function to get the Dropbox access token
function get_dropbox_access_token() {
    // Update with your Dropbox access token
    $accessToken = 'sl.B2k3m88Y7fT9wjEpPkZsVuzFZZ4Ag3SHxICnkbNOdPYqFVEnuJoLAW-V-z-KHxOL5ppFVgkrn9LSkWLtNMikrz4nimgKmm0aeOOTu0iOwSKfRQXzJRn--WM4KMSD31-0pffkJjJO5Zmz';
    return $accessToken;
}

// Handle the file upload via AJAX
function ddu_handle_file_upload() {
    // Check nonce for security
    check_ajax_referer('ddu_nonce', 'security');

    if (!empty($_FILES['files'])) {
        $uploaded_files = $_FILES['files'];

        // Obtain Dropbox access token
        $accessToken = get_dropbox_access_token();

        $uploaded_urls = array();

        foreach ($uploaded_files['tmp_name'] as $index => $tmp_name) {
            $file_name = $uploaded_files['name'][$index];
            
            // Log file info for debugging
            error_log('File info: ' . $file_name);

            // Handle file upload to Dropbox
            $dropboxPath = '/' . basename($file_name);

            $fp = fopen($tmp_name, 'rb');
            $size = filesize($tmp_name);

            $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/octet-stream',
                'Dropbox-API-Arg: ' . json_encode(array(
                    "path" => $dropboxPath,
                    "mode" => "add",
                    "autorename" => true,
                    "mute" => false
                ))
            ));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fp, $size));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            fclose($fp);

            // Log the response for debugging
            error_log('Dropbox response: ' . $response);

            $response_data = json_decode($response, true);

            if (!isset($response_data['error_summary'])) {
                $uploaded_urls[] = 'https://www.dropbox.com/home' . $dropboxPath;
            } else {
                // Log the error summary for debugging
                error_log('Dropbox error: ' . $response_data['error_summary']);
            }

            curl_close($ch);
        }

        if (!empty($uploaded_urls)) {
            echo json_encode(array('message' => 'Files uploaded successfully'));
        } else {
            echo json_encode(array('error' => 'No files uploaded.'));
        }
    } else {
        echo json_encode(array('error' => 'No files uploaded.'));
    }

    wp_die(); // Required to terminate immediately and return a proper response
}
add_action('wp_ajax_ddu_file_upload', 'ddu_handle_file_upload');
add_action('wp_ajax_nopriv_ddu_file_upload', 'ddu_handle_file_upload');
