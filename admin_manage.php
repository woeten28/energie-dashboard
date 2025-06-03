<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$stmt = $pdo->query("SELECT * FROM energy_data ORDER BY date DESC");
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <h2 class="center">Beheer van Energiegegevens</h2>
    <table class="overzicht">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Datum</th>
                <th>Zonne-Energie</th>
                <th>Netafname</th>
                <th>Netinjectie</th>
                <th>Batterij Geladen</th>
                <th>Batterij Ontladen</th>
                <th>Effectief Verbruik</th>
                <th>Opmerking</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $stmt->fetch()): ?>
                <?php $verbruik = $row['solar_kw'] + $row['grid_import_kw'] + $row['battery_discharged_kw'] 
                                - $row['grid_export_kw'] - $row['battery_charged_kw']; 
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['entry_id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['date']))) ?></td>
                    <td><?= htmlspecialchars($row['solar_kw']) ?></td>
                    <td><?= htmlspecialchars($row['grid_import_kw']) ?></td>
                    <td><?= htmlspecialchars($row['grid_export_kw']) ?></td>
                    <td><?= htmlspecialchars($row['battery_charged_kw']) ?></td>
                    <td><?= htmlspecialchars($row['battery_discharged_kw']) ?></td>
                    <td><strong><?= number_format($verbruik, 2) ?> kW</strong></td>
                    <td><?= htmlspecialchars($row['opmerking']) ?></td>
                    <td>
                        <a href="edit_energy.php?id=<?= $row['entry_id'] ?>">Bewerken</a> |
                        <a href="delete_energy.php?id=<?= $row['entry_id'] ?>">Verwijderen</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include 'includes/footer.php'; ?>