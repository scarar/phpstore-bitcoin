<?php

namespace PhpStore\Bitcoin;

use Denpa\Bitcoin\Client as BitcoinClient;

class BitcoinPaymentManager
{
    private $client;
    private $confirmationsRequired;

    public function __construct(array $config)
    {
        $this->client = new BitcoinClient($config);
        $this->confirmationsRequired = $config['confirmations_required'] ?? 3;
    }

    public function generateNewAddress(): string
    {
        try {
            return $this->client->getnewaddress()->get();
        } catch (\Exception $e) {
            throw new \Exception("Failed to generate new Bitcoin address: " . $e->getMessage());
        }
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