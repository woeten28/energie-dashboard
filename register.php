<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = 'user'; 

    if (empty($username) || empty($password)) {
        $errors[] = "Gebruikersnaam en wachtwoord zijn verplicht.";
    } elseif (strlen($username) > 50) {
        $errors[] = "Gebruikersnaam is te lang (max. 50 tekens).";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                $errors[] = "Gebruikersnaam bestaat al.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $role]);

                $success = "Gebruiker succesvol geregistreerd. <a href='index.php'>Klik hier om in te loggen</a>.";
            }
        } catch (PDOException $e) {
            $errors[] = "Fout bij registreren: " . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
    <div><img class="logo" src="img/logo.png"></div>
    <div id="login">
    <h2>Registreren</h2>
    <p>Heb je al een account:<a href="index.php"> Inloggen</a></p>

    <?php
    if (!empty($errors)) {
        echo "<ul style='color: red;'>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }

    if (!empty($success)) {
        echo "<p style='color: green;'>$success</p>";
    }
    ?>
    
    <form action="register.php" method="post">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Wachtwoord:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Registreren</button>
    </form>
    </div>
<?php include 'includes/footer.php'; ?>