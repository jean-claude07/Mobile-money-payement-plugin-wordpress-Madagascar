<?php
if (!defined('ABSPATH')) {
    exit;
}

class MobileMoneyTransactions {
    private static $table_name;
    
    public function __construct() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'mobile_money_transactions';
        
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_get_transaction_details', array($this, 'ajax_get_transaction_details'));
    }

    public function init() {
        // Initialisation des hooks supplémentaires
        add_action('mobile_money_after_payment', array($this, 'update_transaction_status'), 10, 2);
        add_action('mobile_money_daily_cleanup', array($this, 'cleanup_old_transactions'));
    }

    /**
     * Créer une nouvelle transaction
     */
    public static function create_transaction($data) {
        global $wpdb;

        $defaults = array(
            'order_id' => 0,
            'operator' => '',
            'number' => '',
            'amount' => 0,
            'status' => 'pending',
            'reference' => self::generate_reference(),
            'metadata' => array(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );

        $data = wp_parse_args($data, $defaults);
        $data['metadata'] = maybe_serialize($data['metadata']);

        // Validation des données
        if (!self::validate_transaction_data($data)) {
            return new WP_Error('invalid_data', 'Données de transaction invalides');
        }

        // Insertion dans la base de données
        $result = $wpdb->insert(self::$table_name, $data);

        if ($result === false) {
            return new WP_Error('db_error', 'Erreur lors de l\'enregistrement de la transaction');
        }

        $transaction_id = $wpdb->insert_id;
        
        // Hook après création
        do_action('mobile_money_transaction_created', $transaction_id, $data);
        
        return $transaction_id;
    }

    /**
     * Mettre à jour une transaction
     */
    public static function update_transaction($transaction_id, $data) {
        global $wpdb;

        $data['updated_at'] = current_time('mysql');
        if (isset($data['metadata'])) {
            $data['metadata'] = maybe_serialize($data['metadata']);
        }

        $result = $wpdb->update(
            self::$table_name,
            $data,
            array('id' => $transaction_id)
        );

        if ($result === false) {
            return new WP_Error('update_error', 'Erreur lors de la mise à jour');
        }

        do_action('mobile_money_transaction_updated', $transaction_id, $data);
        
        return true;
    }

    /**
     * Récupérer une transaction
     */
    public static function get_transaction($transaction_id) {
        global $wpdb;
        
        $transaction = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . self::$table_name . " WHERE id = %d",
            $transaction_id
        ));

        if ($transaction) {
            $transaction->metadata = maybe_unserialize($transaction->metadata);
        }

        return $transaction;
    }

    /**
     * Récupérer les transactions avec filtres
     */
    public static function get_transactions($args = array()) {
        global $wpdb;

        $defaults = array(
            'number' => 20,
            'offset' => 0,
            'orderby' => 'id',
            'order' => 'DESC',
            'status' => '',
            'operator' => '',
            'start_date' => '',
            'end_date' => ''
        );

        $args = wp_parse_args($args, $defaults);
        $where = array('1=1');
        
        // Construction des conditions WHERE
        if (!empty($args['status'])) {
            $where[] = $wpdb->prepare('status = %s', $args['status']);
        }
        
        if (!empty($args['operator'])) {
            $where[] = $wpdb->prepare('operator = %s', $args['operator']);
        }
        
        if (!empty($args['start_date'])) {
            $where[] = $wpdb->prepare('created_at >= %s', $args['start_date']);
        }
        
        if (!empty($args['end_date'])) {
            $where[] = $wpdb->prepare('created_at <= %s', $args['end_date']);
        }

        $sql = "SELECT * FROM " . self::$table_name . " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY {$args['orderby']} {$args['order']}";
        $sql .= " LIMIT {$args['number']} OFFSET {$args['offset']}";

        $transactions = $wpdb->get_results($sql);

        foreach ($transactions as &$transaction) {
            $transaction->metadata = maybe_unserialize($transaction->metadata);
        }

        return $transactions;
    }

    /**
     * Obtenir les statistiques des transactions
     */
    public static function get_statistics($period = 'today') {
        global $wpdb;

        switch ($period) {
            case 'today':
                $where = "DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $where = "created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $where = "created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
            default:
                $where = "1=1";
        }

        return $wpdb->get_row("
            SELECT 
                COUNT(*) as total_count,
                SUM(amount) as total_amount,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_count,
                AVG(amount) as average_amount
            FROM " . self::$table_name . "
            WHERE $where
        ");
    }

    /**
     * Génération de référence unique
     */
    private static function generate_reference() {
        return 'MM-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }

    /**
     * Validation des données de transaction
     */
    private static function validate_transaction_data($data) {
        if (empty($data['operator']) || empty($data['number']) || empty($data['amount'])) {
            return false;
        }

        if (!in_array($data['operator'], array('mvola', 'orange', 'airtel'))) {
            return false;
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Nettoyage des anciennes transactions
     */
    public function cleanup_old_transactions() {
        global $wpdb;
        
        $retention_days = apply_filters('mobile_money_transaction_retention_days', 90);
        
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . self::$table_name . " 
            WHERE status IN ('completed', 'failed') 
            AND created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $retention_days
        ));
    }

    /**
     * Gestionnaire AJAX pour les détails de transaction
     */
    public function ajax_get_transaction_details() {
        check_ajax_referer('mobile_money_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_die(-1);
        }

        $transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : 0;
        
        if (!$transaction_id) {
            wp_send_json_error();
        }

        $transaction = self::get_transaction($transaction_id);
        
        if (!$transaction) {
            wp_send_json_error();
        }

        wp_send_json_success(array(
            'transaction' => $transaction,
            'html' => $this->get_transaction_details_html($transaction)
        ));
    }

    /**
     * Génère le HTML des détails de transaction
     */
    private function get_transaction_details_html($transaction) {
        ob_start();
        include(MOBILE_MONEY_PLUGIN_DIR . 'admin/views/transaction-details.php');
        return ob_get_clean();
    }
}