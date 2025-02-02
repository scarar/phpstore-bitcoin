<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use PhpStore\Bitcoin\BitcoinPaymentManager;

if (isset($_POST['placeorder']) && isset($_POST['payment']) && $_POST['payment'] === 'bitcoin') {
    try {
        $config = [
            'bitcoin' => require __DIR__ . '/../config/bitcoin.php',
            'database' => require __DIR__ . '/../config/database.php'
        ];
        
        $bitcoinManager = new BitcoinPaymentManager($config);
        
        // Get order data
        $orderData = [
            'customer' => $_SESSION['customerid'],
            'vendor' => $_POST['vendor'],
            'product_id' => $_POST['product_id'],
            'price' => $_POST['price'],
            'quantity' => $_POST['quantity'],
            'total' => $_POST['total'],
            'shipping_address' => $_POST['address'],
            'additional_info' => $_POST['additional_info'] ?? ''
        ];
        
        // Create order with Bitcoin address
        $orderDetails = $bitcoinManager->createOrder($orderData);
        
        // Store order details in session
        $_SESSION['bitcoin_order'] = $orderDetails;
        
        // Redirect to Bitcoin payment page
        header("Location: ../payment_bitcoin.php");
        exit();
    } catch (Exception $e) {
        echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href='../cart.php';
        </script>";
        exit();
    }
}

// Check payment status
if (isset($_GET['check_status']) && isset($_GET['unique_id'])) {
    try {
        $config = [
            'bitcoin' => require __DIR__ . '/../config/bitcoin.php',
            'database' => require __DIR__ . '/../config/database.php'
        ];
        
        $bitcoinManager = new BitcoinPaymentManager($config);
        $status = $bitcoinManager->checkOrderStatus($_GET['unique_id']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $status
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit();
}