<?php
require_once __DIR__ . '/../db.php';

require_admin();
$pdo = db();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
    redirect('/admin/messages.php');
}

$messages = $pdo->query('SELECT * FROM contacts ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesazhet - CryptoVizion</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <div class="logo">CryptoVizion</div>
    <nav>
        <a href="/index.php">Ballina</a>
        <a href="/admin/cryptos.php">Kriptot</a>
        <a href="/admin/messages.php">Mesazhet</a>
        <a href="/auth/logout.php">Dil</a>
    </nav>
</header>

<main class="admin-page">
    <h2 class="section-title">Mesazhet e Kontaktit</h2>

    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Emri</th>
                    <th>Email</th>
                    <th>Mesazhi</th>
                    <th>Data</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= e($message['name']) ?></td>
                        <td><?= e($message['email']) ?></td>
                        <td><?= e($message['message']) ?></td>
                        <td><?= e($message['created_at']) ?></td>
                        <td>
                            <a class="btn btn-danger btn-sm" href="/admin/messages.php?delete=<?= (int)$message['id'] ?>" onclick="return confirm('Sigurt që dëshironi ta fshini këtë mesazh?');">Fshij</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
