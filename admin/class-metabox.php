<?php

namespace MyShortlinkPlugin\Admin;
require_once __DIR__ . '/singleton.php';

class Metabox extends Singleton {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_shortlink_metabox'));
        add_action('save_post', array($this, 'save_shortlink'), 10, 2);
        add_action('admin_menu', array($this, 'remove_unwanted_meta_boxes'));
    }

    // Add a custom metabox for 'shortlink' post type
    public function add_shortlink_metabox() {
        add_meta_box(
            'shortlink_info',        
            __('Link Info', 'my-shortlink-plugin'),
            array($this, 'render_metabox'), 
            'shortlink',            
            'normal',              
            'high'          
        );
    }

    // Remove unwanted meta boxes for the 'shortlink' post type
    public function remove_unwanted_meta_boxes() {
        remove_meta_box('postexcerpt', 'shortlink', 'normal');
        remove_meta_box('slugdiv', 'shortlink', 'normal');
        remove_meta_box('authordiv', 'shortlink', 'normal');
        remove_meta_box('commentstatusdiv', 'shortlink', 'normal');
        remove_meta_box('commentsdiv', 'shortlink', 'normal');
    
    }
    
    // Render the contents of the custom metabox
    public function render_metabox($post) {
        // Get the saved value (if it exists)
        $redirect_url = get_post_meta($post->ID, '_redirect_url', true);

        // Output the HTML for the metabox
        echo '<label for="shortlink_redirect_url">' . __('Redirect to:', 'my-shortlink-plugin') . '</label>';
        echo '<input type="url" id="shortlink_redirect_url" name="shortlink_redirect_url" value="' . esc_attr($redirect_url) . '" size="180" />';
    }

    // Save the custom field value for the 'shortlink' post type
    public function save_shortlink($post_id, $post) {
    
        if (isset($_POST['shortlink_redirect_url'])) {
            // Save data
            update_post_meta(
                $post_id,
                '_redirect_url',
                sanitize_text_field($_POST['shortlink_redirect_url'])
            );
        }
    
        // Save the value of the 'Link Info' field
        if (isset($_POST['_redirect_url'])) {
            update_post_meta(
                $post_id,
                '_redirect_url',
                sanitize_text_field($_POST['_redirect_url'])
            );
        }
    }
}
