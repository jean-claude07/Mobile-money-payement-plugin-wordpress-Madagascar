<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class MobileMoneySecurityHandler {
    private static $encryption_key;
    private static $encryption_method = 'AES-256-CBC';
    private static $max_attempts = 3;
    private static $lockout_time = 1800; // 30 minutes in seconds

    public function __construct() {
        self::init();
        add_action('init', array($this, 'security_checks'));
        add_filter('mobile_money_validate_request', array($this, 'validate_request'), 10, 2);
    }

    public static function init() {
        // Utiliser une clé définie ou en générer une
        self::$encryption_key = defined('MOBILE_MONEY_ENCRYPTION_KEY') 
            ? MOBILE_MONEY_ENCRYPTION_KEY 
            : wp_salt('auth');
    }

    /**
     * Encrypt sensitive data
     */
    public static function encrypt_data($data) {
        if (empty($data)) return '';
        
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$encryption_method));
        $encrypted = openssl_encrypt(
            $data, 
            self::$encryption_method, 
            self::$encryption_key, 
            0, 
            $iv
        );
        
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt sensitive data
     */
    public static function decrypt_data($encrypted_data) {
        if (empty($encrypted_data)) return '';
        
        $decoded = base64_decode($encrypted_data);
        $iv_length = openssl_cipher_iv_length(self::$encryption_method);
        $iv = substr($decoded, 0, $iv_length);
        $encrypted = substr($decoded, $iv_length);
        
        return openssl_decrypt(
            $encrypted, 
            self::$encryption_method, 
            self::$encryption_key, 
            0, 
            $iv
        );
    }

    /**
     * Validate request data
     */
    public static function validate_request($request, $type = 'payment') {
        // Vérifier le nonce
        if (!wp_verify_nonce($request['_wpnonce'], 'mobile_money_action')) {
            self::log_security_event('invalid_nonce', array(
                'request' => $request,
                'ip' => self::get_client_ip()
            ));
            return new WP_Error('security_error', 'Invalid security token');
        }

        // Vérifier le nombre de tentatives
        if (self::is_ip_blocked()) {
            return new WP_Error('security_error', 'Too many attempts. Please try again later.');
        }

        // Validation spécifique au type de requête
        switch ($type) {
            case 'payment':
                return self::validate_payment_request($request);
            case 'refund':
                return self::validate_refund_request($request);
            default:
                return new WP_Error('invalid_type', 'Invalid request type');
        }
    }

    /**
     * Validate payment specific data
     */
    private static function validate_payment_request($request) {
        // Valider le numéro de téléphone
        if (!preg_match('/^03[2-4][0-9]{7}$/', $request['phone_number'])) {
            return new WP_Error('invalid_phone', 'Invalid phone number format');
        }

        // Valider le montant
        if (!is_numeric($request['amount']) || $request['amount'] <= 0) {
            return new WP_Error('invalid_amount', 'Invalid amount');
        }

        return true;
    }

    /**
     * Check if IP is blocked
     */
    private static function is_ip_blocked() {
        $ip = self::get_client_ip();
        $attempts = get_transient('mm_failed_attempts_' . $ip);
        
        if ($attempts && $attempts >= self::$max_attempts) {
            return true;
        }
        
        return false;
    }

    /**
     * Log failed attempt
     */
    public static function log_failed_attempt() {
        $ip = self::get_client_ip();
        $attempts = get_transient('mm_failed_attempts_' . $ip);
        
        if (!$attempts) {
            $attempts = 1;
        } else {
            $attempts++;
        }
        
        set_transient('mm_failed_attempts_' . $ip, $attempts, self::$lockout_time);
    }

    /**
     * Log security events
     */
    public static function log_security_event($type, $data = array()) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'mobile_money_security_logs',
            array(
                'event_type' => $type,
                'event_data' => json_encode($data),
                'ip_address' => self::get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_at' => current_time('mysql')
            )
        );
    }

    /**
     * Get client IP
     */
    private static function get_client_ip() {
        $ip = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Create security tables
     */
    public static function create_security_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mobile_money_security_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data longtext NOT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent text NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY event_type (event_type),
            KEY ip_address (ip_address)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Clean old security logs
     */
    public static function cleanup_old_logs($days = 30) {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}mobile_money_security_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days
            )
        );
    }
}