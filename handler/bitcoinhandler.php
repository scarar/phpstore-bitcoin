<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use PhpStore\Payment;

if (isset($_POST['placeorder']) && isset($_POST['payment']) && $_POST['payment'] === 'bitcoin') {
    $payment = new Payment();
    
    try {
        // Create order first
        include_once "orderhandler.php";
        
        if (isset($_SESSION['order_id'])) {
            $orderId = $_SESSION['order_id'];
            $total = $_POST['total'];
            
            // Create Bitcoin payment
            $paymentDetails = $payment->createPayment($orderId, $total);
            
            // Store payment details in session
            $_SESSION['bitcoin_payment'] = $paymentDetails;
            
            // Redirect to Bitcoin payment page
            header("Location: ../payment_bitcoin.php");
            exit();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}