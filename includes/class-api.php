<?php
class MobileMoneyAPI {
    public static function process_payment($operator, $number, $amount) {
        switch ($operator) {
            case 'mvola':
                return self::process_mvola_payment($number, $amount);
            case 'orange':
                return self::process_orange_payment($number, $amount);
            case 'airtel':
                return self::process_airtel_payment($number, $amount);
            default:
                return ['success' => false, 'message' => 'Opérateur non supporté.'];
        }
    }

    private static function process_mvola_payment($number, $amount) {
        // Exemple d'appel API pour Mvola
        $response = wp_remote_post('https://api.mvola.mg/payment', [
            'body' => json_encode([
                'number' => $number,
                'amount' => $amount,
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer YOUR_API_KEY',
            ],
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body['success'] ? ['success' => true, 'message' => 'Paiement réussi.'] : ['success' => false, 'message' => $body['message']];
    }

    private static function process_orange_payment($number, $amount) {
        $options = get_option('mobile_money_options');
        $response = wp_remote_post($options['orange_api_url'], [
            'body' => json_encode([
                'number' => $number,
                'amount' => $amount,
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer YOUR_ORANGE_API_KEY',
            ],
        ]);
    
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
    
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body['success'] ? ['success' => true, 'message' => 'Paiement réussi.'] : ['success' => false, 'message' => $body['message']];
    }
    
    private static function process_airtel_payment($number, $amount) {
        $options = get_option('mobile_money_options');
        $response = wp_remote_post($options['airtel_api_url'], [
            'body' => json_encode([
                'number' => $number,
                'amount' => $amount,
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer YOUR_AIRTEL_API_KEY',
            ],
        ]);
    
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
    
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body['success'] ? ['success' => true, 'message' => 'Paiement réussi.'] : ['success' => false, 'message' => $body['message']];
    }
}