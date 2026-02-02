<?php
require_once __DIR__ . '/../db.php';

require_admin();
$pdo = db();

$errors = [];
$editing = null;

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM cryptos WHERE id = ?');
    $stmt->execute([$id]);
    redirect('/admin/cryptos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $symbol = strtoupper(trim($_POST['symbol'] ?? ''));
    $price = (float)($_POST['price_usd'] ?? 0);
    $change = (float)($_POST['change_24h'] ?? 0);
    $volume = (float)($_POST['volume_24h'] ?? 0);
    $trend = $_POST['trend'] ?? 'flat';
    $rank = $_POST['market_rank'] !== '' ? (int)$_POST['market_rank'] : null;

    if ($name === '' || $symbol === '') {
        $errors[] = 'Emri dhe simboli janë të detyrueshme.';
    }

    if (!in_array($trend, ['up', 'down', 'flat'], true)) {
        $trend = 'flat';
    }

    if (!$errors) {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE cryptos SET name = ?, symbol = ?, price_usd = ?, change_24h = ?, volume_24h = ?, trend = ?, market_rank = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$name, $symbol, $price, $change, $volume, $trend, $rank, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO cryptos (name, symbol, price_usd, change_24h, volume_24h, trend, market_rank) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $symbol, $price, $change, $volume, $trend, $rank]);
        }
        redirect('/admin/cryptos.php');
    }
}

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM cryptos WHERE id = ?');
    $stmt->execute([$id]);
    $editing = $stmt->fetch();
}

$cryptos = $pdo->query('SELECT * FROM cryptos ORDER BY COALESCE(market_rank, 999999), name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - CryptoVizion</title>
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
    <h2 class="section-title">Menaxho Kriptovalutat</h2>

    <?php if ($errors): ?>
        <div class="notice error">
            <?= e(implode(' ', $errors)) ?>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <h3><?= $editing ? 'Përditëso kriptovalutën' : 'Shto kriptovalutë të re' ?></h3>
        <form method="post" class="admin-form">
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
            <?php endif; ?>
            <div class="form-row">
                <div>
                    <label>Emri</label>
                    <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
                </div>
                <div>
                    <label>Simboli</label>
                    <input type="text" name="symbol" required value="<?= e($editing['symbol'] ?? '') ?>">
                </div>
                <div>
                    <label>Rangu</label>
                    <input type="number" name="market_rank" value="<?= e((string)($editing['market_rank'] ?? '')) ?>">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Çmimi (USD)</label>
                    <input type="number" step="0.01" name="price_usd" required value="<?= e((string)($editing['price_usd'] ?? '0')) ?>">
                </div>
                <div>
                    <label>Ndryshimi 24h (%)</label>
                    <input type="number" step="0.01" name="change_24h" value="<?= e((string)($editing['change_24h'] ?? '0')) ?>">
                </div>
                <div>
                    <label>Vëllimi 24h (USD)</label>
                    <input type="number" step="0.01" name="volume_24h" value="<?= e((string)($editing['volume_24h'] ?? '0')) ?>">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Trendi</label>
                    <select name="trend">
                        <?php
                            $currentTrend = $editing['trend'] ?? 'flat';
                            $trends = ['up' => 'Rritje', 'down' => 'Rënie', 'flat' => 'Neutral'];
                            foreach ($trends as $key => $label):
                        ?>
                            <option value="<?= $key ?>" <?= $currentTrend === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">Ruaj</button>
                    <?php if ($editing): ?>
                        <a class="btn btn-secondary" href="/admin/cryptos.php">Anulo</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <h3>Lista e kriptovalutave</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Rangu</th>
                    <th>Emri</th>
                    <th>Çmimi</th>
                    <th>24h</th>
                    <th>Vëllimi</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cryptos as $crypto): ?>
                    <tr>
                        <td><?= e((string)($crypto['market_rank'] ?? '-')) ?></td>
                        <td><?= e($crypto['name']) ?> (<?= e($crypto['symbol']) ?>)</td>
                        <td><?= number_format((float)$crypto['price_usd'], 2) ?></td>
                        <td><?= number_format((float)$crypto['change_24h'], 2) ?>%</td>
                        <td><?= number_format((float)$crypto['volume_24h'], 2) ?></td>
                        <td>
                            <a class="btn btn-secondary btn-sm" href="/admin/cryptos.php?edit=<?= (int)$crypto['id'] ?>">Edito</a>
                            <a class="btn btn-danger btn-sm" href="/admin/cryptos.php?delete=<?= (int)$crypto['id'] ?>" onclick="return confirm('Sigurt që dëshironi ta fshini këtë kriptovalutë?');">Fshij</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
