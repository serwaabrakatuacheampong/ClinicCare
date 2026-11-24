<?php
session_start();
include 'config/database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$name = $_SESSION['name'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - ClinicQueue</title>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
body {
    background-color: #f4f2fa;
    font-family: Arial, sans-serif;
}
.navbar {
    background-color: #514399;
}
.dashboard-title {
    color: #514399;
    margin-top: 35px;
}
.dashboard-card {
    background-color: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    height: 100%;
}
.dashboard-card h4 {
    color: #514399;
}
.btn-primary {
    background-color: #514399;
    border: none;
}
</style>
</head>
<body>
<nav class="navbar navbar-dark">
<div class="container">
<a class="navbar-brand fw-bold" href="index.php">
ClinicQueue
</a>
<a href="logout.php" class="btn btn-light">
Logout
</a>
</div>
</nav>
<div class="container">
<h2 class="dashboard-title">

Welcome, !

</h2>
<p class="text-muted">

Your role:

<?php echo htmlspecialchars(ucfirst($role)); ?>
</p>
<div class="row g-4 mt-3">
<?php if ($role == "patient"): ?>
<div class="col-md-6">
<div class="dashboard-card">
<h4>Book Appointment</h4>
<p>
Schedule an appointment with the clinic.
</p>
<a href="book.php" class="btn btn-primary">
Book Now
</a>
</div>
</div>
<div class="col-md-6">
<div class="dashboard-card">
<h4>My Appointments</h4>
<p>
View your appointments and queue position.
</p>
<a href="my_appointments.php" class="btn btn-primary">
View Appointments
</a>
</div>
</div>
<?php elseif ($role == "receptionist" || $role == "admin"): ?>
<div class="col-md-6">
<div class="dashboard-card">
<h4>Manage Patient Queue</h4>
<p>
View registered appointments and update queue status.
</p>
<a href="staff_queue.php" class="btn btn-primary">
Manage Queue
</a>
</div>
</div>
<?php elseif ($role == "provider"): ?>
<div class="col-md-6">
<div class="dashboard-card">
<h4>Patient Queue</h4>
<p>
View patients waiting for consultation.
</p>
<a href="staff_queue.php" class="btn btn-primary">
View Queue
</a>
</div>
</div>
<?php endif; ?>
</div>
</div>
</body>
</html>