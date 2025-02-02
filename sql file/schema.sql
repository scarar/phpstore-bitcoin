-- Update orders table to include Bitcoin fields
ALTER TABLE orders ADD COLUMN order_bitcoin_address text NOT NULL AFTER customer_id;
ALTER TABLE orders ADD COLUMN order_unique_id varchar(500) NOT NULL AFTER order_bitcoin_address;

-- Update users table to include Bitcoin address
ALTER TABLE users ADD COLUMN btc_address varchar(255) DEFAULT NULL;