<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Ongeldige ID.";
    exit();
}

$entry_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM energy_data WHERE entry_id = ?");
$stmt->execute([$entry_id]);
$entry = $stmt->fetch();

if (!$entry) {
    echo "Gegevens niet gevonden.";
    exit();
}

if ($role !== 'admin' && $entry['username'] !== $username) {
    echo "Geen toestemming om dit item te verwijderen.";
    exit();
}

$delete = $pdo->prepare("DELETE FROM energy_data WHERE entry_id = ?");
$delete->execute([$entry_id]);


if ($role === 'admin') {
    header('Location: admin_manage.php');
} else {
    header('Location: user_manage.php');
}
exit();
