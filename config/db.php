<?php
if ( ! defined( 'ABSPATH' ) ) exit;

register_activation_hook( dirname(__DIR__) . '/file-review-moderation.php', 'frm_create_db_table_if_not_exists' );

function frm_create_db_table_if_not_exists() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'file_reviews_10min';

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            wp_user_id BIGINT(20) UNSIGNED NOT NULL,
            reviewer_id bigint(20) UNSIGNED DEFAULT 0,
            file_url VARCHAR(255) NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            email VARCHAR(100) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_notes VARCHAR(200) DEFAULT '',
            created_user_id BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
