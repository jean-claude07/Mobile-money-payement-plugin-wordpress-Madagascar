<?php
/**
 * Plugin Name: Mobile Money Madagascar
 * Plugin URI: https://votre-site.com/mobile-money-plugin
 * Description: Plugin WooCommerce pour les paiements via Mvola, Orange Money et Airtel Money.
 * Version: 1.2.1
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Author: RAKOTONARIVO Jean Claude
 * Author URI: https://www.dizitalizeo.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mobile-money-mg
 * Domain Path: /languages
 * WC requires at least: 4.0
 * WC tested up to: 7.5
 * Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
 * WC requires at least: 6.0
 * WC tested up to: 8.0
 *
 * @package MobileMoneyMadagascar
 */

 if (!defined('ABSPATH')) {
    exit;
}

// Définition des constantes
define('MOBILE_MONEY_VERSION', '1.2');
define('MOBILE_MONEY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MOBILE_MONEY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MOBILE_MONEY_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Vérification des dépendances
function mobile_money_check_dependencies() {
    $errors = array();

    // Vérifier PHP version
    if (version_compare(PHP_VERSION, '7.2', '<')) {
        $errors[] = 'PHP 7.2 ou supérieur est requis';
    }

    // Vérifier WooCommerce
    if (!class_exists('WooCommerce')) {
        $errors[] = 'WooCommerce doit être installé et activé';
    }

    // Vérifier la compatibilité avec HPOS
    if (class_exists('Automattic\WooCommerce\Utilities\OrderUtil') && 
        \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()) {
        // Ajouter le support HPOS
        add_action('before_woocommerce_init', function() {
            if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            }
        });
    }

    return $errors;
}

// Hook d'activation
register_activation_hook(__FILE__, function() {
    $errors = mobile_money_check_dependencies();
    
    if (!empty($errors)) {
        deactivate_plugins(MOBILE_MONEY_PLUGIN_BASENAME);
        wp_die(implode('<br>', $errors));
    }
    
    // Créer les tables
    require_once MOBILE_MONEY_PLUGIN_DIR . 'includes/class-database.php';
    MobileMoneyDatabase::create_tables();
});

// Initialisation du plugin
function mobile_money_init() {
    $errors = mobile_money_check_dependencies();
    if (!empty($errors)) {
        add_action('admin_notices', function() use ($errors) {
            echo '<div class="error"><p>';
            echo implode('<br>', $errors);
            echo '</p></div>';
        });
        return;
    }

    // Chargement des fichiers
    require_once MOBILE_MONEY_PLUGIN_DIR . 'includes/class-gateway.php';
    require_once MOBILE_MONEY_PLUGIN_DIR . 'includes/class-transactions.php';
    require_once MOBILE_MONEY_PLUGIN_DIR . 'includes/class-api.php';
    require_once MOBILE_MONEY_PLUGIN_DIR . 'includes/class-security.php';
    require_once MOBILE_MONEY_PLUGIN_DIR . 'admin/class-admin.php';

    // Initialisation des classes
    new MobileMoneyAdmin();
    new MobileMoneyGateway();
}

add_action('plugins_loaded', 'mobile_money_init');