<?php
session_start();
require_once 'config/db.php';

$username = $_SESSION['username'] ?? '';

$stmt = $pdo->prepare("SELECT date, solar_kw, grid_import_kw, grid_export_kw, battery_charged_kw, battery_discharged_kw FROM energy_data WHERE username = ? ORDER BY date ASC");
$stmt->execute([$username]);
$energyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$solar = [];
$gridImport = [];
$gridExport = [];
$batteryCharged = [];
$batteryDischarged = [];
$effectiveUsage = [];

foreach ($energyData as $row) {
    $labels[] = date('d-m', strtotime($row['date']));
    $solar[] = isset($row['solar_kw']) ? (float)$row['solar_kw'] : 0;
    $gridImport[] = isset($row['grid_import_kw']) ? (float)$row['grid_import_kw'] : 0;
    $gridExport[] = isset($row['grid_export_kw']) ? (float)$row['grid_export_kw'] : 0;
    $batteryCharged[] = isset($row['battery_charged_kw']) ? (float)$row['battery_charged_kw'] : 0;
    $batteryDischarged[] = isset($row['battery_discharged_kw']) ? (float)$row['battery_discharged_kw'] : 0;

    $effectief = $row['solar_kw'] +
                 $row['grid_import_kw'] +
                 $row['battery_discharged_kw'] -
                 $row['battery_charged_kw'] -
                 $row['grid_export_kw'];
    $effectiveUsage[] = round($effectief, 2);
}

?>

<?php include 'includes/header.php' ?>
    
<div class="chart-container">
    <canvas id="energyChart"></canvas>
</div>

<script>
const labels = <?= json_encode($labels) ?>;
const solarData = <?= json_encode($solar) ?>;
const gridImportData = <?= json_encode($gridImport) ?>;
const gridExportData = <?= json_encode($gridExport) ?>;
const batteryChargedData = <?= json_encode($batteryCharged) ?>;
const batteryDischargedData = <?= json_encode($batteryDischarged) ?>;
const effectiveUsageData = <?= json_encode($effectiveUsage) ?>;

const ctx = document.getElementById('energyChart').getContext('2d');
const energyChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Opgewekte energie (kW)',
                data: solarData,
                borderColor: 'orange',
                fill: false,
                tension: 0.2
            },
            {
                label: 'Netafname (kW)',
                data: gridImportData,
                borderColor: 'blue',
                fill: false,
                tension: 0.2
            },
            {
                label: 'Netinjectie (kW)',
                data: gridExportData,
                borderColor: 'green',
                fill: false,
                tension: 0.2
            },
            {
                label: 'Batterij geladen (kW)',
                data: batteryChargedData,
                borderColor: 'purple',
                fill: false,
                tension: 0.2
            },
            {
                label: 'Batterij ontladen (kW)',
                data: batteryDischargedData,
                borderColor: 'red',
                fill: false,
                tension: 0.2
            },
            {
                label: 'Effectief verbruik (kW)',
                data: effectiveUsageData,
                borderColor: 'black',
                fill: false,
                borderWidth: 2,
                tension: 0.2
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        stacked: false,
        plugins: {
            title: {
                display: true,
                text: 'Energie gegevens per dag'
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'kW'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Datum'
                }
            }
        }
    }
});
</script>
<?php include 'includes/footer.php'; ?>