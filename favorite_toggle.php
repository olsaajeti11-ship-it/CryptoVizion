<?php
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    redirect('/index.php?auth=login#auth');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php#market');
}

$cryptoId = isset($_POST['crypto_id']) ? (int)$_POST['crypto_id'] : 0;
if ($cryptoId <= 0) {
    redirect('/index.php#market');
}

$redirect = $_POST['redirect'] ?? '/index.php#market';
if (!is_string($redirect) || $redirect === '' || $redirect[0] !== '/') {
    $redirect = '/index.php#market';
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id FROM cryptos WHERE id = ?');
$stmt->execute([$cryptoId]);
if (!$stmt->fetch()) {
    redirect($redirect);
}

$stmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND crypto_id = ?');
$stmt->execute([$_SESSION['user']['id'], $cryptoId]);
$existing = $stmt->fetchColumn();

if ($existing) {
    $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND crypto_id = ?');
    $stmt->execute([$_SESSION['user']['id'], $cryptoId]);
} else {
    $stmt = $pdo->prepare('INSERT INTO favorites (user_id, crypto_id) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user']['id'], $cryptoId]);
}

redirect($redirect);
