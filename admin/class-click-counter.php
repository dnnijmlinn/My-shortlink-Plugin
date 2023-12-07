<?php

namespace MyShortlinkPlugin\Admin;
require_once __DIR__ . '/singleton.php';

class Click_Counter extends Singleton {

    public function __construct() {
        if (!session_id()) {
            session_start();
        }
        add_action('template_redirect', array($this, 'handle_click'));
    }

    // Constructor to initialize session and set up action hooks.
    public function handle_click() {
        if (!isset($_GET['shortlink_id'])) {
            return;
        }

        $post_id = intval($_GET['shortlink_id']);
        if (get_post_type($post_id) !== 'shortlink') {
            return;
        }

        // Increment the total click count
        $this->increment_total_click_count($post_id);

        // Increment the unique click count if more than 2 minutes have passed
        if ($this->should_count_unique_click($post_id)) {
            $this->increment_unique_click_count($post_id);
        }

        // Redirect to the target URL
        $this->redirect_to_target_url($post_id);
    }

    // Increase the total click count for a shortlink
    private function increment_total_click_count($post_id) {
        $count = (int) get_post_meta($post_id, '_total_opens', true);
        $count++;
        update_post_meta($post_id, '_total_opens', $count);
    }

    // Check if a unique click should be counted
    private function should_count_unique_click($post_id) {
        $last_click_time = $_SESSION['last_click_time_' . $post_id] ?? 0;
        $current_time = time();

        if ($current_time - $last_click_time > 2 * MINUTE_IN_SECONDS) {
            $_SESSION['last_click_time_' . $post_id] = $current_time;
            return true;
        }

        return false;
    }

    // Increase the unique click count for a shortlink
    private function increment_unique_click_count($post_id) {
        $count = (int) get_post_meta($post_id, '_unique_opens', true);
        $count++;
        update_post_meta($post_id, '_unique_opens', $count);
    }

    // Redirect to the target URL specified in the shortlink
    private function redirect_to_target_url($post_id) {
        $target_url = get_post_meta($post_id, '_redirect_url', true);
        if (!empty($target_url)) {
            wp_redirect($target_url);
            exit;
        }
    }

    // Methods to retrieve click counts
    public function get_total_click_count($post_id) {
        return (int) get_post_meta($post_id, '_total_opens', true);
    }

    public function get_unique_click_count($post_id) {
        return (int) get_post_meta($post_id, '_unique_opens', true);
    }
}
