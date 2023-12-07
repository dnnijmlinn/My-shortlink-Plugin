<?php

namespace MyShortlinkPlugin\Admin;
require_once __DIR__ . '/singleton.php';

class Admin_Page extends Singleton {
    protected function __construct() {
        add_action('init', array($this, 'register_shortlink_post_type'));
        add_action('init', array($this, 'initialize_meta_for_all_shortlinks'));
        add_filter('manage_shortlink_posts_columns', array($this, 'set_custom_edit_shortlink_columns'));
        add_action('manage_shortlink_posts_custom_column', array($this, 'custom_shortlink_column'), 10, 2);
        add_filter('manage_edit-shortlink_sortable_columns', array($this, 'set_custom_sortable_columns'));
        add_action('pre_get_posts', array($this, 'custom_orderby'));
        add_action('add_meta_boxes', array($this, 'add_custom_meta_boxes'));
        add_filter('post_type_labels_shortlink', array($this, 'change_add_new_button_label'));
        add_filter('gettext', array($this, 'change_add_new_post_text'), 10, 3);
        add_filter('gettext', array($this, 'change_edit_post_text'), 10, 3);
    }

/**
 * Change the label for the "Add New" button in the WordPress admin menu.
 *
 * @param object $labels The labels object containing various labels.
 * @return object The modified labels object.
 */
public function change_add_new_button_label($labels) {
    $labels->add_new = __('Add Short Link', 'my-shortlink-plugin'); 
    return $labels;
}

/**
 * Change the text for the "Add New Post" link in the WordPress admin menu.
 *
 * @param string $translated_text The translated text to be displayed.
 * @param string $text The original text to be translated.
 * @param string $domain The text domain.
 * @return string The modified translated text.
 */
public function change_add_new_post_text($translated_text, $text, $domain) {
    if ($text === 'Add New Post' && $domain === 'default') {
        return __('Add Short Link', 'my-shortlink-plugin'); 
    }
    return $translated_text;
}

/**
 * Change the text for the "Edit Post" link in the WordPress admin menu.
 *
 * @param string $translated_text The translated text to be displayed.
 * @param string $text The original text to be translated.
 * @param string $domain The text domain.
 * @return string The modified translated text.
 */
public function change_edit_post_text($translated_text, $text, $domain) {
    if ($text === 'Edit Post' and $domain === 'default') {
        return __('Edit Link', 'my-shortlink-plugin'); 
    }
    return $translated_text;
}
    
    // Render the admin page content
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Short Links</h1>
        </div>
        <?php
    }

    // Register a custom post type
    public function register_shortlink_post_type() {
        $args = array(
            'public' => true,
            'label'  => __('Short Links', 'my-shortlink-plugin'),
            'menu_name' => __('Short Links', 'my-shortlink-plugin'), 
            'supports' => array('title'), 
            'show_ui' => true, 
            'show_in_menu' => true,
        );
        register_post_type('shortlink', $args);
    }

    // Add new columns to the post table
    public function set_custom_edit_shortlink_columns($columns) {
        unset($columns['author']);
        unset($columns['comments']);
        unset($columns['date']); 

        $columns['shortlink'] = __('Page URL', 'my-shortlink-plugin');
        $columns['full_link'] = __('Full Link', 'my-shortlink-plugin');
        $columns['number_page'] = __('Number of page opens', 'my-shortlink-plugin');
        $columns['total_number_page'] = __('Total number of page opens', 'my-shortlink-plugin');

        $columns['date'] = __('Date', 'my-shortlink-plugin'); 

        return $columns;
    }

    // Fill new columns with data
    public function custom_shortlink_column($column, $post_id) {
        switch ($column) {
            case 'shortlink':
                // Display the short link
                $permalink = esc_url(get_permalink($post_id));
                echo '<a href="' . $permalink . '">' . $permalink . '</a>';
                break;
            case 'full_link':
                // Display the full link
                $link_info = get_post_meta($post_id, '_redirect_url', true);
                $click_url = site_url() . '?shortlink_id=' . $post_id; 
                echo '<a href="' . esc_url($click_url) . '">' . esc_url($link_info) . '</a>';
                break;
            case 'number_page':
                // Display the number of unique page opens
                $click_counter = new Click_Counter();
                $unique_opens = $click_counter->get_unique_click_count($post_id);
                echo esc_html($unique_opens);
                break;
            
            case 'total_number_page':
                // Display the total number of page opens
                $full_click_counter = new Click_Counter();
                $total_opens = $full_click_counter->get_total_click_count($post_id);
                echo esc_html($total_opens);
                break;
        }
    }

    // Add new sortable columns
    public function set_custom_sortable_columns($columns) {
        $columns['number_page'] = 'number_page';
        $columns['total_number_page'] = 'total_number_page';
        return $columns;
    }

    // Configure the query for sorting
    public function custom_orderby($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $orderby = $query->get('orderby');
        if ('number_page' === $orderby) {
            $query->set('meta_key', '_unique_opens');
            $query->set('orderby', 'meta_value_num');
        } elseif ('total_number_page' === $orderby) {
            $query->set('meta_key', '_total_opens');
            $query->set('orderby', 'meta_value_num');
        }
    }

    // Initialize metadata for all 'shortlink' posts
    public function initialize_meta_for_all_shortlinks() {
        $args = array(
            'post_type' => 'shortlink',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        $shortlinks = get_posts($args);

        foreach ($shortlinks as $id) {
            if (!metadata_exists('post', $id, '_unique_opens')) {
                update_post_meta($id, '_unique_opens', 0);
            }
            if (!metadata_exists('post', $id, '_total_opens')) {
                update_post_meta($id, '_total_opens', 0);
            }
        }
    }

    // Add custom meta boxes
    public function add_custom_meta_boxes() {
        add_meta_box(
            'shortlink_clicks_info',               
            __('Click Information', 'my-shortlink-plugin'),                  
            array($this, 'display_clicks_info'),   
            'shortlink',                           
            'side',                                
            'default'                             
        );
    }

    // Display click information in the meta box
    public function display_clicks_info($post) {
        $unique_opens = get_post_meta($post->ID, '_unique_opens', true);
        $total_opens = get_post_meta($post->ID, '_total_opens', true);

        echo '<p>' . sprintf(__('Number of Page Opens: %s', 'my-shortlink-plugin'), esc_html($unique_opens)) . '</p>';
        echo '<p>' . sprintf(__('Total Number of Page Opens: %s', 'my-shortlink-plugin'), esc_html($total_opens)) . '</p>';
    }
}
