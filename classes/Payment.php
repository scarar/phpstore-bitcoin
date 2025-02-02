<?php

namespace PhpStore;

use PDO;
use PhpStore\Bitcoin\BitcoinPaymentManager;

class Payment {
    private $db;
    private $bitcoinManager;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $config = require __DIR__ . '/../config/bitcoin.php';
        $this->bitcoinManager = new BitcoinPaymentManager($config);
    }

    public function createPayment($orderId, $amount) {
        try {
            $address = $this->bitcoinManager->generateNewAddress();
            
            $stmt = $this->db->prepare("
                INSERT INTO payments (order_id, bitcoin_address, amount_btc, status, created_at)
                VALUES (?, ?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([$orderId, $address, $amount]);
            return [
                'payment_id' => $this->db->lastInsertId(),
                'bitcoin_address' => $address,
                'amount_btc' => $amount
            ];
        } catch (\Exception $e) {
            throw new \Exception("Failed to create payment: " . $e->getMessage());
        }
    }

    public function checkPaymentStatus($paymentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM payments WHERE id = ?
            ");
            $stmt->execute([$paymentId]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                throw new \Exception("Payment not found");
            }

            $receivedAmount = $this->bitcoinManager->getReceivedByAddress($payment['bitcoin_address']);
            $isValid = $this->bitcoinManager->validatePayment($payment['bitcoin_address'], $payment['amount_btc']);
            
            if ($isValid && $payment['status'] === 'pending') {
                $this->updatePaymentStatus($paymentId, 'completed');
                $payment['status'] = 'completed';
            }

            return [
                'status' => $payment['status'],
                'received_amount' => $receivedAmount,
                'expected_amount' => $payment['amount_btc'],
                'address' => $payment['bitcoin_address']
            ];
        } catch (\Exception $e) {
            throw new \Exception("Failed to check payment status: " . $e->getMessage());
        }
    }

    private function updatePaymentStatus($paymentId, $status) {
        $stmt = $this->db->prepare("
            UPDATE payments 
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$status, $paymentId]);
    }

    public function getPaymentsByOrder($orderId) {
        $stmt = $this->db->prepare("
            SELECT * FROM payments WHERE order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}