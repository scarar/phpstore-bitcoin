<?php

namespace PhpStore\Bitcoin;

use Denpa\Bitcoin\Client as BitcoinClient;
use PDO;

class BitcoinPaymentManager
{
    private $client;
    private $confirmationsRequired;
    private $db;

    public function __construct(array $config)
    {
        $this->client = new BitcoinClient($config['bitcoin']);
        $this->confirmationsRequired = $config['bitcoin']['confirmations_required'] ?? 3;
        
        // Database connection
        $dbConfig = $config['database'];
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
        $this->db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function generateNewAddress(): string
    {
        try {
            return $this->client->getnewaddress()->get();
        } catch (\Exception $e) {
            throw new \Exception("Failed to generate new Bitcoin address: " . $e->getMessage());
        }
    }

    public function createOrder(array $orderData): array
    {
        try {
            $address = $this->generateNewAddress();
            $uniqueId = $this->generateOrderUniqueId($orderData);
            
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    customer, vendor, order_bitcoin_address, order_unique_id,
                    product_id, price, quantity, order_total,
                    payment_method_id, order_address, order_additional_info,
                    order_status, ordered_at
                ) VALUES (
                    :customer, :vendor, :btc_address, :unique_id,
                    :product_id, :price, :quantity, :total,
                    1, :address, :additional_info,
                    'pending', NOW()
                )
            ");

            $stmt->execute([
                'customer' => $orderData['customer'],
                'vendor' => $orderData['vendor'],
                'btc_address' => $address,
                'unique_id' => $uniqueId,
                'product_id' => $orderData['product_id'],
                'price' => $orderData['price'],
                'quantity' => $orderData['quantity'],
                'total' => $orderData['total'],
                'address' => $orderData['shipping_address'],
                'additional_info' => $orderData['additional_info'] ?? ''
            ]);

            return [
                'order_id' => $this->db->lastInsertId(),
                'bitcoin_address' => $address,
                'unique_id' => $uniqueId,
                'amount_btc' => $orderData['total']
            ];
        } catch (\Exception $e) {
            throw new \Exception("Failed to create order: " . $e->getMessage());
        }
    }

    private function generateOrderUniqueId(array $orderData): string
    {
        return sprintf(
            "%d%sto%s%s",
            $orderData['product_id'],
            $orderData['customer'],
            $orderData['vendor'],
            date('YmdHis')
        );
    }

    public function getReceivedByAddress(string $address): float
    {
        try {
            return (float) $this->client->getreceivedbyaddress($address, $this->confirmationsRequired)->get();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get received amount: " . $e->getMessage());
        }
    }

    public function validatePayment(string $address, float $expectedAmount): bool
    {
        $receivedAmount = $this->getReceivedByAddress($address);
        return $receivedAmount >= $expectedAmount;
    }

    public function checkOrderStatus(string $uniqueId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM orders 
                WHERE order_unique_id = :unique_id
                LIMIT 1
            ");
            $stmt->execute(['unique_id' => $uniqueId]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new \Exception("Order not found");
            }

            $receivedAmount = $this->getReceivedByAddress($order['order_bitcoin_address']);
            $isValid = $this->validatePayment($order['order_bitcoin_address'], $order['order_total']);

            if ($isValid && $order['order_status'] === 'pending') {
                $this->updateOrderStatus($order['id'], 'completed');
                $order['order_status'] = 'completed';
            }

            return [
                'status' => $order['order_status'],
                'received_amount' => $receivedAmount,
                'expected_amount' => $order['order_total'],
                'address' => $order['order_bitcoin_address']
            ];
        } catch (\Exception $e) {
            throw new \Exception("Failed to check order status: " . $e->getMessage());
        }
    }

    private function updateOrderStatus(int $orderId, string $status): void
    {
        $stmt = $this->db->prepare("
            UPDATE orders 
            SET order_status = :status
            WHERE id = :id
        ");
        $stmt->execute([
            'status' => $status,
            'id' => $orderId
        ]);
    }

    public function getTransactionsByAddress(string $address): array
    {
        try {
            $transactions = [];
            $receivedTxs = $this->client->listreceivedbyaddress(0, true, true, $address)->get();
            
            foreach ($receivedTxs as $tx) {
                if ($tx['address'] === $address) {
                    $transactions = $tx['txids'];
                    break;
                }
            }

            return $transactions;
        } catch (\Exception $e) {
            throw new \Exception("Failed to get transactions: " . $e->getMessage());
        }
    }

    public function getTransactionDetails(string $txid): array
    {
        try {
            return $this->client->gettransaction($txid)->get();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get transaction details: " . $e->getMessage());
        }
    }
}