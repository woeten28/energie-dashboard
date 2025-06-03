<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header(header: 'Location: dashboard.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check = $pdo->prepare("SELECT COUNT(*) FROM energy_data WHERE username = ? AND date = ?");
    $check->execute([$_SESSION['username'], $_POST['date']]);
    $exists = $check->fetchColumn();

    if ($exists) {
        $message = "Je hebt voor deze datum al gegevens ingevoerd.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO energy_data 
            (username, date, solar_kw, grid_import_kw, grid_export_kw, battery_charged_kw, battery_discharged_kw, opmerking)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_SESSION['username'],
            $_POST['date'],
            $_POST['solar_kw'],
            $_POST['grid_import_kw'],
            $_POST['grid_export_kw'],
            $_POST['battery_charged_kw'],
            $_POST['battery_discharged_kw'],
            $_POST['opmerking']
        ]);

        $message = "Energiegegevens succesvol toegevoegd.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Energiegegevens invoeren</h2>
    <?php if ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="add_energy.php">
        <label>Datum:</label>
        <input type="date" name="date" required><br><br>

        <label>Opgewekte energie (kW):</label>
        <input type="number" step="0.01" name="solar_kw" required><br><br>

        <label>Netafname (kW):</label>
        <input type="number" step="0.01" name="grid_import_kw" required><br><br>

        <label>Netinjectie (kW):</label>
        <input type="number" step="0.01" name="grid_export_kw" required><br><br>

        <label>Batterij geladen (kW):</label>
        <input type="number" step="0.01" name="battery_charged_kw" required><br><br>

        <label>Batterij ontladen (kW):</label>
        <input type="number" step="0.01" name="battery_discharged_kw" required><br><br>

        <label><strong>Effectief verbruik (kW):</strong> <span id="calculatedUsage">-</span></label><br><br>

        <label>Opmerking:</label>
        <input type="text" name="opmerking"><br><br>
        <input type="submit" value="Opslaan">
    </form>
</div>
<script>
    function calculateUsage() {
        const solar = parseFloat(document.querySelector('[name="solar_kw"]').value) || 0;
        const import_kw = parseFloat(document.querySelector('[name="grid_import_kw"]').value) || 0;
        const export_kw = parseFloat(document.querySelector('[name="grid_export_kw"]').value) || 0;
        const batt_charge = parseFloat(document.querySelector('[name="battery_charged_kw"]').value) || 0;
        const batt_discharge = parseFloat(document.querySelector('[name="battery_discharged_kw"]').value) || 0;

        const verbruik = (solar + import_kw + batt_discharge) - (export_kw + batt_charge);

        document.getElementById('calculatedUsage').textContent = verbruik.toFixed(2) + ' kW';
    }

    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', calculateUsage);
    });

    window.addEventListener('DOMContentLoaded', calculateUsage);
</script>
<?php include 'includes/footer.php'; ?>
