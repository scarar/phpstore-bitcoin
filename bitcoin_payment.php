<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use PhpStore\Payment;

$payment = new Payment();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create new payment
        $orderId = $_POST['order_id'];
        $amountUSD = $_POST['amount'];
        
        // TODO: Implement real BTC/USD conversion
        $btcPrice = 45000; // Example fixed price, should be fetched from an API
        $amountBTC = $amountUSD / $btcPrice;
        
        $paymentDetails = $payment->createPayment($orderId, $amountBTC);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $paymentDetails
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

if (isset($_GET['check_status'])) {
    try {
        $paymentId = $_GET['payment_id'];
        $status = $payment->checkPaymentStatus($paymentId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $status
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}