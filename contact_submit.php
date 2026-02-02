<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php#contact');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    redirect('/index.php?contact=error#contact');
}

$pdo = db();
$stmt = $pdo->prepare('INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $message]);

redirect('/index.php?contact=success#contact');
