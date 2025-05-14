<?php
// Uninstall script for the Mobile Money plugin

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Function to delete plugin options and data
function mobile_money_uninstall() {
    // Delete options from the database
    delete_option('mobile_money_enabled');
    delete_option('mobile_money_title');
    delete_option('mobile_money_mvola_api_url');
    delete_option('mobile_money_orange_api_url');
    delete_option('mobile_money_airtel_api_url');
    delete_option('mobile_money_merchant_number');

    // Optionally, remove transaction records from the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'mobile_money_transactions';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Hook the uninstall function
mobile_money_uninstall();
?>