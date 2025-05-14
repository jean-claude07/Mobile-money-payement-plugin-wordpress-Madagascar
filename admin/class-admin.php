<?php
if (!defined('ABSPATH')) {
    exit;
}

class MobileMoneyAdmin {
    private $encryption_key;
    private $encryption_iv;

    public function __construct() {
        // Initialiser les clés de chiffrement
        $this->init_encryption();
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    private function init_encryption() {
        // Générer ou récupérer la clé de chiffrement
        $this->encryption_key = get_option('mobile_money_encryption_key');
        if (empty($this->encryption_key)) {
            $this->encryption_key = wp_generate_password(32, true, true);
            update_option('mobile_money_encryption_key', $this->encryption_key);
        }

        // Générer ou récupérer le vecteur d'initialisation
        $this->encryption_iv = get_option('mobile_money_encryption_iv');
        if (empty($this->encryption_iv)) {
            $this->encryption_iv = wp_generate_password(16, true, true);
            update_option('mobile_money_encryption_iv', $this->encryption_iv);
        }
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'mobile-money') !== false) {
            wp_enqueue_style('mobile-money-admin', MOBILE_MONEY_PLUGIN_URL . 'admin/css/style.css', array(), MOBILE_MONEY_VERSION);
            wp_enqueue_script('mobile-money-admin', MOBILE_MONEY_PLUGIN_URL . 'admin/js/script.js', array('jquery'), MOBILE_MONEY_VERSION, true);
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            'Mobile Money Settings',
            'Mobile Money',
            'manage_options',
            'mobile-money-settings',
            array($this, 'settings_page'),
            'dashicons-money',
            100
        );

        add_submenu_page(
            'mobile-money-settings',
            'Transactions',
            'Transactions',
            'manage_options',
            'mobile-money-transactions',
            array($this, 'transactions_page')
        );
    }

    public function register_settings() {
        register_setting('mobile_money_options_group', 'mobile_money_options', array($this, 'sanitize_settings'));
    }

    public function sanitize_settings($input) {
        $sanitized = array();

        if (isset($input['mvola_api_key'])) {
            $sanitized['mvola_api_key'] = $this->encrypt_data($input['mvola_api_key']);
        }

        if (isset($input['orange_api_key'])) {
            $sanitized['orange_api_key'] = $this->encrypt_data($input['orange_api_key']);
        }

        if (isset($input['airtel_api_key'])) {
            $sanitized['airtel_api_key'] = $this->encrypt_data($input['airtel_api_key']);
        }

        // Autres champs qui ne nécessitent pas de chiffrement
        $sanitized['mvola_api_url'] = esc_url_raw($input['mvola_api_url']);
        $sanitized['orange_api_url'] = esc_url_raw($input['orange_api_url']);
        $sanitized['airtel_api_url'] = esc_url_raw($input['airtel_api_url']);
        $sanitized['merchant_number'] = sanitize_text_field($input['merchant_number']);

        return $sanitized;
    }

    private function encrypt_data($data) {
        if (empty($data)) return '';
        
        return openssl_encrypt(
            $data,
            'AES-256-CBC',
            $this->encryption_key,
            0,
            $this->encryption_iv
        );
    }

    private function decrypt_data($encrypted_data) {
        if (empty($encrypted_data)) return '';
        
        return openssl_decrypt(
            $encrypted_data,
            'AES-256-CBC',
            $this->encryption_key,
            0,
            $this->encryption_iv
        );
    }

    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $options = get_option('mobile_money_options', array());
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php
            if (isset($_GET['settings-updated'])) {
                add_settings_error(
                    'mobile_money_messages',
                    'mobile_money_message',
                    __('Paramètres sauvegardés avec succès.', 'mobile-money-mg'),
                    'updated'
                );
            }
            settings_errors('mobile_money_messages');
            ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('mobile_money_options_group');
                ?>
                <table class="form-table">
                    <tr>
                        <th colspan="2"><h2>MVola</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Mvola API URL</th>
                        <td>
                            <input type="url" name="mobile_money_options[mvola_api_url]" 
                                   value="<?php echo esc_attr($options['mvola_api_url'] ?? ''); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Mvola API Key</th>
                        <td>
                            <input type="password" name="mobile_money_options[mvola_api_key]" 
                                   value="<?php echo esc_attr($this->decrypt_data($options['mvola_api_key'] ?? '')); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Mvola Merchant Number</th>
                        <td>
                            <input type="text" name="mobile_money_options[mvola_merchant_number]" 
                                   value="<?php echo esc_attr($options['mvola_merchant_number'] ?? ''); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>

                    <!-- Orange Money Section -->
                    <tr>
                        <th colspan="2"><h2>Orange Money</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Orange Money API URL</th>
                        <td>
                            <input type="url" name="mobile_money_options[orange_api_url]" 
                                   value="<?php echo esc_attr($options['orange_api_url'] ?? ''); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Orange Money API Key</th>
                        <td>
                            <input type="password" name="mobile_money_options[orange_api_key]" 
                                   value="<?php echo esc_attr($this->decrypt_data($options['orange_api_key'] ?? '')); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Orange Money Merchant Number</th>
                        <td>
                            <input type="text" name="mobile_money_options[orange_merchant_number]" 
                                   value="<?php echo esc_attr($options['orange_merchant_number'] ?? ''); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>

                    <!-- Airtel Money Section -->
                    <tr>
                        <th colspan="2"><h2>Airtel Money</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">Airtel Money API URL</th>
                        <td>
                            <input type="url" name="mobile_money_options[airtel_api_url]" 
                                   value="<?php echo esc_attr($options['airtel_api_url'] ?? ''); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Airtel Money API Key</th>
                        <td>
                            <input type="password" name="mobile_money_options[airtel_api_key]" 
                                   value="<?php echo esc_attr($this->decrypt_data($options['airtel_api_key'] ?? '')); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Airtel Money Merchant Number</th>
                        <td>
                            <input type="text" name="mobile_money_options[airtel_merchant_number]" 
                                   value="<?php echo esc_attr($options['airtel_merchant_number'] ?? ''); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                </table>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function transactions_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
    
        // Récupérer les transactions depuis la base de données
        global $wpdb;
        $table_name = $wpdb->prefix . 'mobile_money_transactions';
        $transactions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    
        // Inclure la vue avec les transactions
        require_once MOBILE_MONEY_PLUGIN_DIR . 'admin/views/transactions.php';
    }
}