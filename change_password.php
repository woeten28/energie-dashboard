<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "Gebruiker niet gevonden.";
        $Error = true;
    } elseif (!password_verify($current_password, $user['password'])) {
        $message = "Huidig wachtwoord is onjuist.";
        $Error = true;
    } elseif ($new_password !== $confirm_password) {
        $message = "Nieuw wachtwoord en bevestiging komen niet overeen.";
        $Error = true;
    } elseif ($current_password === $new_password) {
        $message = "Huidig en nieuw wachtwoord zijn gelijk.";
        $Error = true;
    }    else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        $message = "Wachtwoord succesvol gewijzigd.";
        $Error = false;
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Wachtwoord wijzigen</h2>
    <?php if (!empty($message)): ?>
        <p style="color: <?= $Error ? 'red' : 'green' ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
    <form method="post">
    <label>Gebruikersnaam:</label>
    <input type="text" name="username" required>
    <br><br>

    <label>Huidig wachtwoord:</label>
    <input type="password" name="current_password" required>
    <br><br>

    <label>Nieuw wachtwoord:</label>
    <input type="password" name="new_password" required>
    <br><br>

    <label>Bevestig nieuw wachtwoord:</label>
    <input type="password" name="confirm_password" required>
    <br><br>

    <button type="submit">Wijzigen</button>
</form>
</div>
<?php include 'includes/footer.php'; ?>
