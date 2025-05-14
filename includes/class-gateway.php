<?php
class MobileMoneyGateway {
    public function __construct() {
        add_filter('woocommerce_payment_gateways', [$this, 'add_gateway']);
    }

    public function add_gateway($gateways) {
        $gateways[] = 'WC_MobileMoney_Gateway';
        return $gateways;
    }
}

add_action('plugins_loaded', function() {
    class WC_MobileMoney_Gateway extends WC_Payment_Gateway {
        public function __construct() {
            $this->id = 'mobile_money';
            $this->method_title = 'Mobile Money';
            $this->method_description = 'Paiements via Mvola, Orange Money et Airtel Money.';
            $this->supports = ['products'];

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        }

        public function init_form_fields() {
            $this->form_fields = [
                'enabled' => [
                    'title' => 'Activer/Désactiver',
                    'type' => 'checkbox',
                    'label' => 'Activer Mobile Money',
                    'default' => 'yes',
                ],
                'title' => [
                    'title' => 'Titre',
                    'type' => 'text',
                    'default' => 'Mobile Money',
                ],
                'description' => [
                    'title' => 'Description',
                    'type' => 'textarea',
                    'default' => 'Payez via Mobile Money.',
                ],
            ];
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            // Simulez un paiement réussi
            $order->payment_complete();
            return [
                'result' => 'success',
                'redirect' => $this->get_return_url($order),
            ];
        }
    }
});