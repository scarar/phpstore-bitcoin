-- Create the bitcoin_payments table
CREATE TABLE IF NOT EXISTS bitcoin_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    bitcoin_address VARCHAR(255) NOT NULL,
    amount_btc DECIMAL(16,8) NOT NULL,
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    UNIQUE KEY unique_address (bitcoin_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add bitcoin payment method to orders table if not exists
ALTER TABLE orders MODIFY COLUMN pay_method ENUM('cash', 'paypal', 'bitcoin') NOT NULL;