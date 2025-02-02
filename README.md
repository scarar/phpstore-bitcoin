# PHP Store with Bitcoin Integration

This repository is a complete e-commerce solution with Bitcoin payment integration. It combines the original PHP Store functionality with Bitcoin Core RPC payment processing.

## Features

- Complete e-commerce platform
- Product catalog and shopping cart
- User registration and authentication
- Multiple payment methods:
  - Cash on delivery
  - PayPal
  - Bitcoin (using Bitcoin Core RPC)
- Real-time Bitcoin payment tracking
- Order management system

## Requirements

- PHP 7.4 or higher
- MySQL/MariaDB
- Bitcoin Core node running with RPC enabled
- Composer for dependency management

## Installation

1. Clone this repository
2. Install dependencies:
```bash
composer install
```

3. Import the SQL files:
```bash
mysql -u your_user -p your_database < sql\ file/phpstore.sql
mysql -u your_user -p your_database < sql\ file/bitcoin_payments.sql
```

4. Configure your database connection in `config/database.php`:
```php
return [
    'host' => 'localhost',
    'dbname' => 'your_database',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];
```

5. Configure Bitcoin Core RPC in `config/bitcoin.php`:
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

1. Start your web server and point it to the project directory
2. Access the website through your browser
3. Register an account or log in
4. Browse products and add them to cart
5. During checkout, select Bitcoin as payment method
6. Follow the payment instructions on screen

## Bitcoin Payment Process

1. Customer selects Bitcoin payment method
2. System generates a unique Bitcoin address
3. Customer sends the exact amount to the address
4. System monitors the address for incoming transactions
5. Order is marked as complete when payment is confirmed

## Security Features

- Secure password hashing
- Session management
- SQL injection protection
- XSS prevention
- CSRF protection
- Secure Bitcoin payment handling:
  - Unique addresses for each transaction
  - Confirmation requirement
  - Amount validation
  - Double-spend protection

## Directory Structure

- `/classes` - Core PHP classes
- `/config` - Configuration files
- `/handler` - Request handlers
- `/partials` - Reusable page components
- `/sql file` - Database schema
- `/src` - Bitcoin integration classes
- `/vendor` - Composer dependencies

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## Security Considerations

- Always use HTTPS in production
- Keep Bitcoin Core RPC credentials secure
- Regularly update dependencies
- Monitor for suspicious activities
- Maintain secure backups
- Use strong server-side validation