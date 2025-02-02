<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpStore\Bitcoin\BitcoinPaymentManager;

// Load configuration
$config = require __DIR__ . '/../config/bitcoin.php';

try {
    // Initialize Bitcoin payment manager
    $bitcoinManager = new BitcoinPaymentManager($config);

    // Example: Generate a new address for payment
    $paymentAddress = $bitcoinManager->generateNewAddress();
    echo "New payment address: " . $paymentAddress . "\n";

    // Example: Check received amount for an address
    $receivedAmount = $bitcoinManager->getReceivedByAddress($paymentAddress);
    echo "Received amount: " . $receivedAmount . " BTC\n";

    // Example: Validate if payment is received
    $expectedAmount = 0.001; // 0.001 BTC
    $isPaymentReceived = $bitcoinManager->validatePayment($paymentAddress, $expectedAmount);
    echo "Payment received: " . ($isPaymentReceived ? "Yes" : "No") . "\n";

    // Example: Get transactions for an address
    $transactions = $bitcoinManager->getTransactionsByAddress($paymentAddress);
    echo "Transactions:\n";
    foreach ($transactions as $txid) {
        $txDetails = $bitcoinManager->getTransactionDetails($txid);
        echo "Transaction ID: " . $txid . "\n";
        echo "Amount: " . $txDetails['amount'] . " BTC\n";
        echo "Confirmations: " . $txDetails['confirmations'] . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}