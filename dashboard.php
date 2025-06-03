<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>
   
<div class="dashboard-container">
  <h2>Admin Dashboard</h2>
    <ul class="dashboard-menu">
      <li><a href="admin_manage.php">Beheer van Energiegegevens</a></li>
      <li><a href="register.php">Registreer nieuwe gebruiker</a></li>
      <li><a href="change_password.php">Wijzig wachtwoorden</a></li>
    </ul>
</div>
<?php include 'includes/footer.php'; ?>