# PHP Store Bitcoin Integration

This repository contains the Bitcoin Core RPC integration for the PHP Store application. It provides a simple way to handle Bitcoin payments in your PHP Store installation.

## Requirements

- PHP 7.4 or higher
- Bitcoin Core node running with RPC enabled
- Composer for dependency management

## Installation

1. Clone this repository
2. Install dependencies:
```bash
composer install
```

3. Copy the config/bitcoin.php file and update it with your Bitcoin Core RPC credentials:
```php
return [
    'scheme' => 'http',
    'host' => '127.0.0.1',
    'port' => 8332,
    'user' => 'your-rpc-username',
    'password' => 'your-rpc-password',
    'confirmations_required' => 3
];
```

## Usage

The `BitcoinPaymentManager` class provides several methods for handling Bitcoin payments:

```php
use PhpStore\Bitcoin\BitcoinPaymentManager;

// Initialize with configuration
$config = require 'config/bitcoin.php';
$bitcoinManager = new BitcoinPaymentManager($config);

// Generate new address for payment
$address = $bitcoinManager->generateNewAddress();

// Check received amount
$amount = $bitcoinManager->getReceivedByAddress($address);

// Validate payment
$isValid = $bitcoinManager->validatePayment($address, 0.001); // 0.001 BTC

// Get transactions
$transactions = $bitcoinManager->getTransactionsByAddress($address);
```

See the `examples/payment.php` file for more detailed usage examples.

## Integration with PHP Store

To integrate this with your PHP Store installation:

1. Add this package as a dependency
2. Configure your Bitcoin Core RPC settings
3. Use the BitcoinPaymentManager class to handle payments in your checkout process

## Security Considerations

- Always validate payment amounts server-side
- Use HTTPS for all API communications
- Keep your Bitcoin Core RPC credentials secure
- Monitor for double-spend attempts
- Wait for sufficient confirmations before finalizing orders