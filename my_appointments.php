<?php
session_start();
require_once __DIR__ . '/config/database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$patient_id = $_SESSION['user_id'];
$message = "";
// Handle appointment deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_appointment'])) {
    $appointment_id = (int) $_POST['appointment_id'];
    $deleteQuery = $conn->prepare(
        "DELETE FROM appointments
         WHERE id = ? AND patient_id = ?
         AND status IN ('Booked', 'Waiting')"
    );
    $deleteQuery->bind_param("ii", $appointment_id, $patient_id);
    if ($deleteQuery->execute() && $deleteQuery->affected_rows > 0) {
        $deleteQuery->close();
        header("Location: my_appointments.php?deleted=1");
        exit();
    } else {
        $message = "Appointment could not be deleted. It may already be in consultation or completed.";
        $deleteQuery->close();
    }
}
// Display confirmation after deletion
if (isset($_GET['deleted'])) {
    $message = "Appointment deleted successfully!";
}
// Get patient's appointments
$query = $conn->prepare(
    "SELECT id, appointment_date, appointment_time,
            reason, queue_number, status
     FROM appointments
     WHERE patient_id = ?
     ORDER BY appointment_date DESC, appointment_time DESC"
);
$query->bind_param("i", $patient_id);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Appointments | ClinicCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body {
        background: #f1edfa;
        font-family: Arial, sans-serif;
    }
    .main-card {
        background: white;
        padding: 30px;
        margin-top: 50px;
        margin-bottom: 50px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .heading {
        color: #51418f;
        font-weight: bold;
    }
    .queue-number {
        background: #51418f;
        color: white;
        padding: 7px 13px;
        border-radius: 7px;
        font-weight: bold;
    }
    .btn-dashboard {
        background: #51418f;
        color: white;
    }
    .btn-dashboard:hover {
        background: #3f326f;
        color: white;
    }
</style>
</head>
<body>
<div class="container">
<div class="main-card">
    <div class="text-center mb-4">
        <i class="fas fa-calendar-check fa-3x"
           style="color:#51418f;"></i>
        <h2 class="heading mt-3">My Appointments</h2>
        <p class="text-muted">
            View and manage your clinic appointments.
        </p>
    </div>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Queue Number</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($appointment = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="queue-number">
                                <?php
                                echo htmlspecialchars(
                                    str_pad(
                                        (string)$appointment['queue_number'],
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    )
                                );
                                ?>
                            </span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(
                                date("d M Y", strtotime($appointment['appointment_date']))
                            ); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(
                                date("h:i A", strtotime($appointment['appointment_time']))
                            ); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($appointment['reason']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($appointment['status']); ?>
                        </td>
                        <td>
                            <?php if (in_array($appointment['status'], ['Booked', 'Waiting'])): ?>
                                <form method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                    <input type="hidden"
                                           name="appointment_id"
                                           value="<?php echo (int)$appointment['id']; ?>">
                                    <button type="submit"
                                            name="delete_appointment"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Not available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            You have no appointments yet.
        </div>
    <?php endif; ?>
    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-dashboard">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Dashboard
        </a>
        <a href="book.php" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>
            Book Appointment
        </a>
    </div>
</div>
</div>
</body>
</html>
<?php
$query->close();
?>