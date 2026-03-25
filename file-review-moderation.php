<?php
/*
Plugin Name: file-review-moderation for https://10min-english.nz/
Description: When a user uploads a file, the backend automatically reviews it to ensure the content is safe, legal, and compliant with platform rules.
Version:     1.0.0
Author:      Q Hao
License:     GPL2
*/


require_once plugin_dir_path( __FILE__ ) . 'config/db.php';
require_once plugin_dir_path( __FILE__ ) . 'frontend/frm-desk.php';
require_once plugin_dir_path( __FILE__ ) . 'methods/utilities.php';
require_once plugin_dir_path( __FILE__ ) . 'backend/application_review_page.php';




function frm_enqueue_styles() {
    if ( ! is_admin() ) {
        wp_enqueue_style(
            'frm-frontend',
            plugins_url('frontend/assets/css/style.css', __FILE__),
            array(),
            '1.1.0'
        );
    }
    if ( is_admin() ) {
        wp_enqueue_style(
            'frm-backend',
            plugins_url('backend/assets/css/style.css', __FILE__),
            array(),
            '1.1.0'
        );
    }
}

add_action('wp_enqueue_scripts', 'frm_enqueue_styles');
add_action('admin_enqueue_scripts', 'frm_enqueue_styles');
