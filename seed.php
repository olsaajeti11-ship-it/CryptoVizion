<?php
require_once __DIR__ . '/db.php';

$pdo = db();

$adminEmail = 'admin@cryptovizion.test';
$adminPass = 'admin123';

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$adminEmail]);
if (!$stmt->fetch()) {
    $hash = password_hash($adminPass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute(['Admin', $adminEmail, $hash, 'admin']);
}

$existing = (int)$pdo->query('SELECT COUNT(*) FROM cryptos')->fetchColumn();
if ($existing === 0) {
    $seed = [
        ['Bitcoin', 'BTC', 43850.45, 1.25, 25000000000, 'up', 1],
        ['Ethereum', 'ETH', 2350.80, -0.85, 12000000000, 'down', 2],
        ['Solana', 'SOL', 98.10, 2.15, 3500000000, 'up', 3],
        ['Cardano', 'ADA', 0.55, -0.40, 1800000000, 'down', 4],
        ['Ripple', 'XRP', 0.62, 0.10, 2200000000, 'flat', 5],
        ['Polkadot', 'DOT', 7.85, 0.75, 900000000, 'up', 6],
    ];

    $stmt = $pdo->prepare('INSERT INTO cryptos (name, symbol, price_usd, change_24h, volume_24h, trend, market_rank) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($seed as $row) {
        $stmt->execute($row);
    }
}

echo "Seed completed. Admin: $adminEmail / $adminPass";
