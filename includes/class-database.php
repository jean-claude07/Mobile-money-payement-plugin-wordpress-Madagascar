<?php
if (!defined('ABSPATH')) {
    exit;
}

class MobileMoneyDatabase {
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table des transactions
        $table_transactions = $wpdb->prefix . 'mobile_money_transactions';
        $sql_transactions = "CREATE TABLE IF NOT EXISTS $table_transactions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT(20) UNSIGNED NOT NULL,
            operator VARCHAR(50) NOT NULL,
            number VARCHAR(20) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            metadata TEXT,
            response_data TEXT,
            refund_status VARCHAR(20),
            refund_amount DECIMAL(10,2),
            refund_date DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY order_id (order_id),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_transactions);
    }
}