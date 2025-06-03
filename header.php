<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Energie Dashboard</title>
    <link rel="icon" type="image/x-icon" href="img/mini_logo.png">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header>
    <nav class="nav-bar">
        <ul class="nav-menu-left">
            <li><a href="index.php"><img class="logo-klein" src="img/mini_logo_white.png" alt="logo"></a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="admin_manage.php">Beheer Energiegegevens</a></li>
                <?php else: ?>
                    <li><a href="view_energy.php">Grafiek</a></li>
                    <li><a href="user_manage.php">Beheer Energiegegevens</a></li>
                    <li><a href="add_energy.php">Gegevens toevoegen</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <?php if (isset($_SESSION['username'])): ?>
            <ul class="nav-menu-right">
                <li><a href="logout.php">Afmelden (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
            </ul>
        <?php endif; ?>
    </nav>
</header>
<main>
