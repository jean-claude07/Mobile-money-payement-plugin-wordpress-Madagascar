<?php
class TestGateway {
    public function test_process_payment() {
        $response = MobileMoneyAPI::process_payment('mvola', '0321234567', 1000);
        $this->assertTrue($response['success']);
    }
}