<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: user_manage.php');
    exit();
}

$entry_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM energy_data WHERE entry_id = :id AND username = :username");
$stmt->execute(['id' => $entry_id, 'username' => $username]);
$entry = $stmt->fetch();

if (!$entry) {
    echo "Gegevens niet gevonden.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $solar_kw = $_POST['solar_kw'];
    $grid_import_kw = $_POST['grid_import_kw'];
    $grid_export_kw = $_POST['grid_export_kw'];
    $battery_charged_kw = $_POST['battery_charged_kw'];
    $battery_discharged_kw = $_POST['battery_discharged_kw'];
    $opmerking = $_POST['opmerking'];


    $update = $pdo->prepare("UPDATE energy_data SET date = :date, solar_kw = :solar_kw, grid_import_kw = :grid_import_kw, grid_export_kw = :grid_export_kw, battery_charged_kw = :battery_charged_kw, battery_discharged_kw = :battery_discharged_kw, opmerking = :opmerking WHERE entry_id = :id AND username = :username");

    $update->execute([
        'date' => $date,
        'solar_kw' => $solar_kw,
        'grid_import_kw' => $grid_import_kw,
        'grid_export_kw' => $grid_export_kw,
        'battery_charged_kw' => $battery_charged_kw,
        'battery_discharged_kw' => $battery_discharged_kw,
        'id' => $entry_id,
        'username' => $username,
        'opmerking' => $opmerking
    ]);

    header('Location: user_manage.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<div class="form-container">
    <h2>Bewerk energiegegevens (entry_id: <?= htmlspecialchars($entry_id) ?>)</h2>
    <form method="POST">
        <label for="date">Datum:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($entry['date']) ?>" required><br><br>

        <label for="solar_kw">Opgewekte energie (kW):</label>
        <input type="number" step="0.01" name="solar_kw" value="<?= htmlspecialchars($entry['solar_kw']) ?>" required><br><br>

        <label for="grid_import_kw">Netafname (kW):</label>
        <input type="number" step="0.01" name="grid_import_kw" value="<?= htmlspecialchars($entry['grid_import_kw']) ?>" required><br><br>

        <label for="grid_export_kw">Netinjectie (kW):</label>
        <input type="number" step="0.01" name="grid_export_kw" value="<?= htmlspecialchars($entry['grid_export_kw']) ?>" required><br><br>

        <label for="battery_charged_kw">Batterij geladen (kW):</label>
        <input type="number" step="0.01" name="battery_charged_kw" value="<?= htmlspecialchars($entry['battery_charged_kw']) ?>" required><br><br>

        <label for="battery_discharged_kw">Batterij ontladen (kW):</label>
        <input type="number" step="0.01" name="battery_discharged_kw" value="<?= htmlspecialchars($entry['battery_discharged_kw']) ?>" required><br><br>

        <label>Opmerking:</label>
        <input type="text" name="opmerking" value="<?= htmlspecialchars($entry['opmerking']) ?>"><br><br>

        <button type="submit">Opslaan</button>
    </form>
    <br>
    <a href="user_manage.php">Terug naar overzicht</a>
</div>
<?php include 'includes/footer.php'; ?>