-- Database schema for CryptoVizion
CREATE DATABASE IF NOT EXISTS cryptovizion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cryptovizion;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cryptos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    symbol VARCHAR(10) NOT NULL UNIQUE,
    price_usd DECIMAL(14,2) NOT NULL DEFAULT 0,
    change_24h DECIMAL(6,2) NOT NULL DEFAULT 0,
    volume_24h DECIMAL(18,2) NOT NULL DEFAULT 0,
    trend ENUM('up', 'down', 'flat') NOT NULL DEFAULT 'flat',
    market_rank INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    crypto_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_favorite (user_id, crypto_id),
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_crypto FOREIGN KEY (crypto_id) REFERENCES cryptos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed admin user
INSERT INTO users (name, email, password_hash, role)
VALUES ('Admin', 'admin@cryptovizion.test', '$2y$10$vvdWAmtPASKa1H4ldtIStuare81yXaaV1HeYXr0WJ0LVgAubqnzOK', 'admin')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password_hash = VALUES(password_hash),
    role = VALUES(role);

-- Seed crypto data
INSERT INTO cryptos (name, symbol, price_usd, change_24h, volume_24h, trend, market_rank)
VALUES
    ('Bitcoin', 'BTC', 43850.45, 1.25, 25000000000, 'up', 1),
    ('Ethereum', 'ETH', 2350.80, -0.85, 12000000000, 'down', 2),
    ('Solana', 'SOL', 98.10, 2.15, 3500000000, 'up', 3),
    ('Cardano', 'ADA', 0.55, -0.40, 1800000000, 'down', 4),
    ('Ripple', 'XRP', 0.62, 0.10, 2200000000, 'flat', 5),
    ('Polkadot', 'DOT', 7.85, 0.75, 900000000, 'up', 6)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    price_usd = VALUES(price_usd),
    change_24h = VALUES(change_24h),
    volume_24h = VALUES(volume_24h),
    trend = VALUES(trend),
    market_rank = VALUES(market_rank),
    updated_at = NOW();
