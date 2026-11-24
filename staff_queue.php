<?php
session_start();
include 'config/database.php';
// Check whether the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Allow only clinic staff
if (!in_array($_SESSION['role'], ['receptionist', 'provider', 'admin'])) {
    header("Location: dashboard.php");
    exit();
}
$message = "";
// Update patient status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = (int)$_POST['appointment_id'];
    $status = $_POST['status'];
    $allowed_statuses = [
        'Waiting',
        'In Consultation',
        'Completed',
        'Cancelled'
    ];
    if ($appointment_id > 0 && in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare(
            "UPDATE appointments SET status = ? WHERE id = ?"
        );
        $stmt->bind_param("si", $status, $appointment_id);
        if ($stmt->execute()) {
            $message = "Patient status updated successfully.";
        } else {
            $message = "Unable to update patient status.";
        }
    } else {
        $message = "Invalid information provided.";
    }
}
// Get today's appointments
$today = date('Y-m-d');
$sql = "SELECT
            appointments.id,
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.queue_number,
            appointments.reason,
            appointments.status,
            users.name,
            users.phone
        FROM appointments
        INNER JOIN users
        ON appointments.patient_id = users.id
        WHERE appointments.appointment_date = ?
        ORDER BY appointments.queue_number ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Queue Management</title>
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
h2 {
    color: #514399;
}
.queue-box {
    background-color: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
<a class="navbar-brand fw-bold" href="dashboard.php">
ClinicQueue
</a>
<a href="logout.php" class="btn btn-light">
Logout
</a>
</div>
</nav>
<div class="container mt-5">
<h2>Patient Queue Management</h2>
<p class="text-muted">

Today’s Date: 

</p>
<?php if (!empty($message)): ?>
<div class="alert alert-info">
<?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>
<div class="queue-box">
<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
<th>Queue Number</th>
<th>Patient Name</th>
<th>Phone Number</th>
<th>Appointment Time</th>
<th>Reason</th>
<th>Status</th>
<th>Update Status</th>
</tr>
</thead>
<tbody>
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td>
<strong>
Q<?php echo str_pad($row['queue_number'], 3, "0", STR_PAD_LEFT); ?>
</strong>
</td>
<td>
<?php echo htmlspecialchars($row['name']); ?>
</td>
<td>
<?php echo htmlspecialchars($row['phone']); ?>
</td>
<td>
<?php echo htmlspecialchars($row['appointment_time']); ?>
</td>
<td>
<?php echo htmlspecialchars($row['reason']); ?>
</td>
<td>
<span class="badge bg-primary">
<?php echo htmlspecialchars($row['status']); ?>
</span>
</td>
<td>
<form method="POST">
<select name="status" class="form-select mb-2">
<option value="Waiting"
<?php if ($row['status'] == 'Waiting') echo 'selected'; ?>>
Waiting
</option>
<option value="In Consultation"
<?php if ($row['status'] == 'In Consultation') echo 'selected'; ?>>
In Consultation
</option>
<option value="Completed"
<?php if ($row['status'] == 'Completed') echo 'selected'; ?>>
Completed
</option>
<option value="Cancelled"
<?php if ($row['status'] == 'Cancelled') echo 'selected'; ?>>
Cancelled
</option>
</select>
<button type="submit" class="btn btn-primary btn-sm">

Update

</button>
</form>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="7" class="text-center">

No appointments have been booked for today.

</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<div class="mt-4">
<a href="dashboard.php" class="btn btn-secondary">

Back to Dashboard

</a>
</div>
</div>
</body>
</html>
