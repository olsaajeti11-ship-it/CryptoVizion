<?php
require_once __DIR__ . '/db.php';

$pdo = db();
$cryptos = $pdo->query('SELECT * FROM cryptos ORDER BY COALESCE(market_rank, 999999), name')->fetchAll();

$userCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$cryptoCount = (int)$pdo->query('SELECT COUNT(*) FROM cryptos')->fetchColumn();
$volumeTotal = (float)$pdo->query('SELECT COALESCE(SUM(volume_24h), 0) FROM cryptos')->fetchColumn();

$favorites = [];
$favoriteCryptos = [];
if (is_logged_in()) {
    $stmt = $pdo->prepare('SELECT crypto_id FROM favorites WHERE user_id = ?');
    $stmt->execute([$_SESSION['user']['id']]);
    $favorites = array_map('intval', array_column($stmt->fetchAll(), 'crypto_id'));

    $stmt = $pdo->prepare('SELECT c.* FROM cryptos c INNER JOIN favorites f ON f.crypto_id = c.id WHERE f.user_id = ? ORDER BY COALESCE(c.market_rank, 999999), c.name');
    $stmt->execute([$_SESSION['user']['id']]);
    $favoriteCryptos = $stmt->fetchAll();
}

function format_money(float $value): string
{
    return '$' . number_format($value, 2);
}

$notice = null;
$noticeType = 'success';
if (isset($_GET['contact']) && $_GET['contact'] === 'success') {
    $notice = 'Faleminderit për mesazhin tuaj! Do t’ju kontaktojmë së shpejti.';
}
if (isset($_GET['contact']) && $_GET['contact'] === 'error') {
    $notice = 'Ju lutemi plotësoni të gjitha fushat në mënyrë të saktë.';
    $noticeType = 'error';
}
if (isset($_GET['auth']) && $_GET['auth'] === 'login') {
    $notice = 'Ju lutemi kyçuni për të vazhduar.';
}
if (isset($_GET['auth']) && $_GET['auth'] === 'registered') {
    $notice = 'Regjistrimi u krye me sukses. Tani mund të kyçeni.';
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoVizion - Futuristic Crypto Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if ($notice): ?>
    <div class="notice <?= $noticeType === 'error' ? 'error' : '' ?>">
        <?= e($notice) ?>
    </div>
<?php endif; ?>

<header>
    <div class="logo">CryptoVizion</div>
    <nav>
        <a href="#home">Ballina</a>
        <a href="#features">Veçoritë</a>
        <a href="#market">Tregu</a>
        <a href="#why">Përse Ne</a>
        <a href="#contact">Kontakti</a>
        <?php if (!is_logged_in()): ?>
            <a href="#auth">Kyçu</a>
        <?php else: ?>
            <a href="#dashboard">Paneli</a>
            <?php if (is_admin()): ?>
                <a href="admin/cryptos.php">Admin</a>
            <?php endif; ?>
            <a href="auth/logout.php">Dil</a>
        <?php endif; ?>
    </nav>
</header>

<section id="home" class="hero">
    <div class="hero-content">
        <h1>Mirësevini në të Ardhmen e Kriptovalutave</h1>
        <p>Analiza vizuale, grafike dinamike dhe pasqyra reale e tregut në kohë reale</p>

        <div class="cta-buttons">
            <button class="btn btn-primary" id="exploreBtn">Eksploro Tregun ➜</button>
            <button class="btn btn-secondary">Mëso Më Shumë</button>
        </div>
    </div>
</section>

<div class="stats">
    <div class="stat-item">
        <div class="stat-number"><?= number_format($userCount) ?></div>
        <div class="stat-label">Përdorues Aktivë</div>
    </div>

    <div class="stat-item">
        <div class="stat-number"><?= format_money($volumeTotal) ?></div>
        <div class="stat-label">Vëllimi Ditor</div>
    </div>

    <div class="stat-item">
        <div class="stat-number"><?= number_format($cryptoCount) ?></div>
        <div class="stat-label">Kriptovaluta</div>
    </div>

    <div class="stat-item">
        <div class="stat-number">99.9%</div>
        <div class="stat-label">Uptime</div>
    </div>
</div>

<section id="features" class="features">
    <h2 class="section-title">Veçoritë Futuriste</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Analiza në Kohë Reale</h3>
            <p>Monitorim i çmimeve me përditësim të menjëhershëm dhe grafike interaktive për çdo kriptovalutë.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Siguri Maksimale</h3>
            <p>Enkriptim i avancuar dhe arkitekturë moderne për ruajtjen e sigurt të të dhënave tuaja.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Shpejtësi e Lartë</h3>
            <p>Platforma jonë ofron performancë të shkëlqyer dhe kohë reagimi të shpejtë në çdo transaksion.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🌐</div>
            <h3>Akses Global</h3>
            <p>Tregoni kudo në botë, 24/7, me mbështetje për mbi 50 valuta të ndryshme kombëtare.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📈</div>
            <h3>Raporte të Detajuara</h3>
            <p>Statistika të hollësishme dhe analiza të thella për të marrë vendime të informuara.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🎯</div>
            <h3>Sinjale Inteligjente</h3>
            <p>Algoritme të avancuara AI për të identifikuar mundësitë më të mira të investimit.</p>
        </div>
    </div>
</section>

<section id="market" class="market-section">
    <div class="market-container">
        <h2 class="section-title">Tregu i Kriptovalutave</h2>

        <div class="market-tabs">
            <button class="tab-btn active">Të Gjitha</button>
            <button class="tab-btn">Top Gainers</button>
            <button class="tab-btn">Top Losers</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kriptovaluta</th>
                    <th>Çmimi</th>
                    <th>24h Ndryshimi</th>
                    <th>Vëllimi</th>
                    <th>Trendi</th>
                    <?php if (is_logged_in()): ?>
                        <th>Favorit</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cryptos as $crypto): ?>
                    <?php
                        $change = (float)$crypto['change_24h'];
                        $trendIcon = $crypto['trend'] === 'up' ? '📈' : ($crypto['trend'] === 'down' ? '📉' : '➡️');
                        $changeClass = $change > 0 ? 'positive' : ($change < 0 ? 'negative' : '');
                        $isFavorite = in_array((int)$crypto['id'], $favorites, true);
                    ?>
                    <tr>
                        <td>
                            <div class="crypto-name">
                                <strong><?= e($crypto['name']) ?></strong>
                                <span class="crypto-symbol"><?= e($crypto['symbol']) ?></span>
                            </div>
                        </td>
                        <td><?= format_money((float)$crypto['price_usd']) ?></td>
                        <td class="<?= $changeClass ?>" style="font-weight:bold;">
                            <?= number_format($change, 2) ?>%
                        </td>
                        <td><?= format_money((float)$crypto['volume_24h']) ?></td>
                        <td style="font-size: 20px;">
                            <?= $trendIcon ?>
                        </td>
                        <?php if (is_logged_in()): ?>
                            <td>
                                <form method="post" action="favorite_toggle.php" class="favorite-form">
                                    <input type="hidden" name="crypto_id" value="<?= (int)$crypto['id'] ?>">
                                    <input type="hidden" name="redirect" value="/index.php#market">
                                    <button type="submit" class="favorite-btn <?= $isFavorite ? 'active' : '' ?>" title="<?= $isFavorite ? 'Hiq nga favoritët' : 'Shto në favoritë' ?>">
                                        ★
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section id="why" class="why-choose">
    <h2 class="section-title">Përse të Zgjidhni CryptoVizion</h2>
    <div class="why-grid">
        <div class="why-card">
            <h4>✓ Platforma e Besueshme</h4>
            <p>Mbi <?= number_format($userCount) ?> përdorues aktivë na besojnë çdo ditë për investimet e tyre.</p>
        </div>

        <div class="why-card">
            <h4>✓ Tarifat më të Ulëta</h4>
            <p>Komisione transparente dhe konkurruese për të maksimizuar fitimin tuaj.</p>
        </div>

        <div class="why-card">
            <h4>✓ Mbështetje 24/7</h4>
            <p>Ekipi ynë është gjithmonë gati t’ju ndihmojë në çdo moment.</p>
        </div>

        <div class="why-card">
            <h4>✓ Teknologji e Avancuar</h4>
            <p>Infrastrukturë moderne dhe e sigurt për përvojë të shkëlqyer përdorimi.</p>
        </div>

        <div class="why-card">
            <h4>✓ Aplikacion Mobile</h4>
            <p>Tregoni edhe kur jeni larg kompjuterit me aplikacionin tonë mobile.</p>
        </div>

        <div class="why-card">
            <h4>✓ Edukim Falas</h4>
            <p>Burime edukative dhe udhëzues për të filluar investimin tuaj.</p>
        </div>
    </div>
</section>

<section id="auth" class="auth-section">
    <h2 class="section-title">Llogaria juaj</h2>
    <?php if (is_logged_in()): ?>
        <div class="auth-card">
            <h3>Mirë se erdhe, <?= e($_SESSION['user']['name']) ?>!</h3>
            <p>Jeni të kyçur me emailin: <?= e($_SESSION['user']['email']) ?>.</p>
            <?php if (is_admin()): ?>
                <p><a class="btn btn-secondary" href="admin/cryptos.php">Hap Panelin Admin</a></p>
            <?php endif; ?>
            <p><a class="btn btn-primary" href="auth/logout.php">Dil nga llogaria</a></p>
        </div>
    <?php else: ?>
        <div class="auth-grid">
            <form class="auth-card" action="auth/login.php" method="post">
                <h3>Kyçu</h3>
                <label for="loginEmail">Email</label>
                <input type="email" id="loginEmail" name="email" placeholder="email@example.com" required>

                <label for="loginPassword">Fjalëkalimi</label>
                <input type="password" id="loginPassword" name="password" placeholder="********" required>

                <button class="btn btn-primary" type="submit">Kyçu</button>
            </form>

            <form class="auth-card" action="auth/register.php" method="post">
                <h3>Regjistrohu</h3>
                <label for="registerName">Emri i plotë</label>
                <input type="text" id="registerName" name="name" placeholder="Emri juaj" required>

                <label for="registerEmail">Email</label>
                <input type="email" id="registerEmail" name="email" placeholder="email@example.com" required>

                <label for="registerPassword">Fjalëkalimi</label>
                <input type="password" id="registerPassword" name="password" placeholder="Minimum 6 karaktere" required>

                <button class="btn btn-secondary" type="submit">Regjistrohu</button>
            </form>
        </div>
    <?php endif; ?>
</section>

<?php if (is_logged_in()): ?>
    <section id="dashboard" class="user-dashboard">
        <h2 class="section-title">Paneli im</h2>
        <div class="admin-card">
            <h3>Favoritet e mia</h3>
            <?php if (!$favoriteCryptos): ?>
                <p class="muted">Nuk keni favoritë ende. Shtoni kriptovaluta nga tregu duke klikuar yllin.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Kriptovaluta</th>
                            <th>Çmimi</th>
                            <th>24h Ndryshimi</th>
                            <th>Vëllimi</th>
                            <th>Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($favoriteCryptos as $crypto): ?>
                            <?php
                                $change = (float)$crypto['change_24h'];
                                $changeClass = $change > 0 ? 'positive' : ($change < 0 ? 'negative' : '');
                            ?>
                            <tr>
                                <td><?= e($crypto['name']) ?> (<?= e($crypto['symbol']) ?>)</td>
                                <td><?= format_money((float)$crypto['price_usd']) ?></td>
                                <td class="<?= $changeClass ?>" style="font-weight:bold;">
                                    <?= number_format($change, 2) ?>%
                                </td>
                                <td><?= format_money((float)$crypto['volume_24h']) ?></td>
                                <td>
                                    <form method="post" action="favorite_toggle.php" class="favorite-form">
                                        <input type="hidden" name="crypto_id" value="<?= (int)$crypto['id'] ?>">
                                        <input type="hidden" name="redirect" value="/index.php#dashboard">
                                        <button type="submit" class="btn btn-secondary btn-sm">Hiq</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<section id="contact" class="contact">
    <div class="contact-container">
        <h2 class="section-title">Na Kontaktoni</h2>
        <form class="contact-form" id="contactForm" action="contact_submit.php" method="post">
            <div class="form-group">
                <label for="name">Emri</label>
                <input type="text" id="name" name="name" placeholder="Emri juaj" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="email@example.com" required>
            </div>

            <div class="form-group">
                <label for="message">Mesazhi</label>
                <textarea id="message" name="message" placeholder="Si mund t'ju ndihmojmë?" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">Dërgo Mesazhin</button>
        </form>
    </div>
</section>

<footer>
    <div class="footer-content">
        <div class="footer-links">
            <a href="#">Kushtet e Përdorimit</a>
            <a href="#">Politika e Privatësisë</a>
            <a href="#">FAQ</a>
            <a href="#">Rreth Nesh</a>
        </div>
        <p style="color:#cbd5e0;margin-top:20px;">
            © 2026 CryptoVizion. Të gjitha të drejtat e rezervuara.
        </p>
    </div>
</footer>

<button class="scroll-top" id="scrollTop">↑</button>
<script src="script.js"></script>
</body>
</html>
