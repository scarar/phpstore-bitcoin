# PHP Store with Bitcoin Integration

This repository is a complete e-commerce solution with Bitcoin payment integration. It combines the original PHP Store functionality with Bitcoin Core RPC payment processing.

## Features

- Complete e-commerce platform
- Product catalog and shopping cart
- User registration and authentication with CAPTCHA
- Multiple payment methods:
  - Cash on delivery
  - PayPal
  - Bitcoin (using Bitcoin Core RPC)
- Real-time Bitcoin payment tracking
- Live BTC/USD price updates
- Order management system

## Requirements

- PHP 7.4 or higher
- MySQL/MariaDB
- Bitcoin Core node running with RPC enabled
- Composer for dependency management

## Installation

1. Clone this repository

2. Install Composer dependencies:
```bash
composer require denpa/php-bitcoinrpc
composer require guzzlehttp/guzzle
```

Required dependencies in composer.json:
```json
{
    "require": {
        "php": ">=7.4",
        "denpa/php-bitcoinrpc": "^2.1",
        "guzzlehttp/guzzle": "^7.0"
    }
}
```

3. Import the SQL file:
```bash
mysql -u your_user -p your_database < sql\ file/phpstore_bitcoin.sql
```

## Configuration Files

Several files need to be configured with your credentials:

### 1. Database Configuration (Two Files)

#### a. Main Database Config
File: `config/database.php`
```php
return [
    'host' => 'localhost',      // Your database host
    'dbname' => 'phpstore',     // Your database name
    'username' => 'root',       // Your database username
    'password' => '',           // Your database password
    'charset' => 'utf8mb4'
];
```

#### b. Legacy Database Connection
File: `partials/connect.php`
```php
<?php
$host = "localhost";    // Your database host
$user = "root";        // Your database username
$password = "";        // Your database password
$dbname = "phpstore";  // Your database name

$connect = mysqli_connect($host, $user, $password, $dbname);
?>
```

### 2. Bitcoin Core RPC Configuration
File: `config/bitcoin.php`
```php
return [
    'scheme' => 'http',
    'host' => '127.0.0.1',              // Your Bitcoin Core RPC host
    'port' => 8332,                     // Your Bitcoin Core RPC port
    'user' => 'your-rpc-username',      // Your Bitcoin Core RPC username
    'password' => 'your-rpc-password',  // Your Bitcoin Core RPC password
    'confirmations_required' => 3
];
```

### 3. Bitcoin Price API Configuration
File: `js/bitcoin-price.js`
```javascript
// Configure API endpoints and update intervals
const CONFIG = {
    UPDATE_INTERVAL: 60000,  // Price update interval in milliseconds
    CACHE_DURATION: 3600000, // Cache duration in milliseconds
    APIS: {
        BINANCE_US: 'https://api.binance.us/api/v3/ticker/price?symbol=BTCUSDT',
        BINANCE_GLOBAL: 'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT',
        BLOCKCHAIN_INFO: 'https://blockchain.info/ticker'
    }
};
```

### 4. PayPal Configuration (if using PayPal)
File: `config/paypal.php`
```php
return [
    'client_id' => 'your-paypal-client-id',
    'client_secret' => 'your-paypal-client-secret',
    'mode' => 'sandbox' // or 'live' for production
];
```

### 5. Admin Account
Default admin credentials in SQL file:
- Username: admin
- Email: admin@phpstore.com
- Password: admin123

**IMPORTANT:** Change these credentials after first login!

## Directory Structure

- `/admin` - Admin panel and management interface
- `/classes` - Core PHP classes
- `/config` - Configuration files
- `/css` - Stylesheets
  - `bitcoin.css` - Bitcoin-specific styles
  - `main.css` - Main application styles
- `/handler` - Request handlers
- `/includes` - PHP includes and helpers
- `/js` - JavaScript files
  - `bitcoin-price.js` - Bitcoin price updates
  - `main.js` - Main application scripts
- `/partials` - Reusable page components
  - `connect.php` - Legacy database connection
  - `header.php` - Site header with Bitcoin price
  - `footer.php` - Site footer
- `/securimage-master` - CAPTCHA implementation
- `/sql file` - Database schema
- `/src` - Bitcoin integration classes
- `/vendor` - Composer dependencies

## Security Considerations

1. File Permissions:
   - Set 644 for files
   - Set 755 for directories
   - Protect config files

2. Configuration Security:
   - Move config files outside web root
   - Use strong passwords
   - Change default credentials
   - Use different database users for admin/customer access

3. Bitcoin Security:
   - Use SSL/TLS for RPC
   - Limit RPC access
   - Monitor transactions
   - Set appropriate confirmations

4. Database Security:
   - Use prepared statements
   - Limit database user privileges
   - Regular backups
   - Secure credentials

## Troubleshooting

1. Bitcoin Price Not Updating:
   - Check API endpoints in bitcoin-price.js
   - Verify network connectivity
   - Check browser console for errors

2. Bitcoin Payments Not Processing:
   - Verify Bitcoin Core is running
   - Check RPC credentials
   - Verify network connectivity
   - Check PHP error logs

3. Database Connection Issues:
   - Verify MySQL is running
   - Check credentials in both database.php and connect.php
   - Verify database exists
   - Check PHP error logs

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.