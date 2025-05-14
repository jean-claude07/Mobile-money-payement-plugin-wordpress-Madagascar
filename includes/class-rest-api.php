<?php
class MobileMoneyRestAPI {
    public static function register_routes() {
        register_rest_route('mobile-money/v1', '/process-payment', [
            'methods' => 'POST',
            'callback' => [self::class, 'process_payment'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function process_payment($request) {
        $params = $request->get_json_params();
        $operator = sanitize_text_field($params['operator']);
        $number = sanitize_text_field($params['number']);
        $amount = floatval($params['amount']);

        return MobileMoneyAPI::process_payment($operator, $number, $amount);
    }
}

// Hook to register the REST API routes
add_action('rest_api_init', ['MobileMoneyRestAPI', 'register_routes']);