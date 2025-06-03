<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Ongeldige ID.";
    exit();
}

$entry_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM energy_data WHERE entry_id = ?");
$stmt->execute([$entry_id]);
$entry = $stmt->fetch();

if (!$entry) {
    echo "Gegevens niet gevonden.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $solar = $_POST['solar_kw'];
    $import = $_POST['grid_import_kw'];
    $export = $_POST['grid_export_kw'];
    $charged = $_POST['battery_charged_kw'];
    $discharged = $_POST['battery_discharged_kw'];
    $opmerking = $_POST['opmerking'];


    $update = $pdo->prepare("UPDATE energy_data SET date = ?, solar_kw = ?, grid_import_kw = ?, grid_export_kw = ?, battery_charged_kw = ?, battery_discharged_kw = ?, opmerking = ? WHERE entry_id = ?");
    $update->execute([$date, $solar, $import, $export, $charged, $discharged, $opmerking, $entry_id]);

    header('Location: admin_manage.php');
    exit();
}

?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Bewerk energiegegevens (entry_id: <?= htmlspecialchars($entry_id) ?>)</h2>
    <form method="post">
        <label for="date">Datum:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($entry['date']) ?>" required><br><br>

        <label>Opgewekte energie (kW):</label>
        <input type="number" step="0.01" name="solar_kw" value="<?= htmlspecialchars($entry['solar_kw']) ?>" required><br><br>

        <label>Netafname (kW):</label>
        <input type="number" step="0.01" name="grid_import_kw" value="<?= htmlspecialchars($entry['grid_import_kw']) ?>" required><br><br>

        <label>Netinjectie (kW):</label>
        <input type="number" step="0.01" name="grid_export_kw" value="<?= htmlspecialchars($entry['grid_export_kw']) ?>" required><br><br>

        <label>Batterij geladen (kW):</label>
        <input type="number" step="0.01" name="battery_charged_kw" value="<?= htmlspecialchars($entry['battery_charged_kw']) ?>" required><br><br>

        <label>Batterij ontladen (kW):</label>
        <input type="number" step="0.01" name="battery_discharged_kw" value="<?= htmlspecialchars($entry['battery_discharged_kw']) ?>" required><br><br>

        <label>Opmerking:</label>
        <input type="text" name="opmerking" value="<?= htmlspecialchars($entry['opmerking']) ?>"><br><br>

        <input type="submit" value="Opslaan">
    </form>
    <br>
    <a href="admin_manage.php">Terug naar overzicht</a>
</div>
<?php include 'includes/footer.php'; ?>
