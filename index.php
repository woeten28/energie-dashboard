<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: view_energy.php');
    }
    exit();
}

$login_error = $_SESSION['login_error'] ?? '';

unset($_SESSION['login_error'], $_SESSION['login_username']);
?>

<?php include 'includes/header.php'; ?>
    <div><img class="logo" src="img/logo.png"></div>
    <div id="login">
        <h2>Inloggen</h2>
        <p>Geen account:<a href="register.php"> Registreer</a></p>
        
        <?php if ($login_error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Wachtwoord:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Inloggen</button>
        </form>
    </div> 
<?php include 'includes/footer.php'; ?>