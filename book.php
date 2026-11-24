<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/sms.php';
$message = "";
$messageType = "";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$patient_id = $_SESSION['user_id'];
// Get the patient's details
$userQuery = $conn->prepare(
    "SELECT name, phone FROM users WHERE id = ?"
);
$userQuery->bind_param("i", $patient_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$patient = $userResult->fetch_assoc();
$userQuery->close();
if (!$patient) {
    die("Patient account could not be found.");
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $reason = trim($_POST['reason'] ?? '');
    // Validate the submitted information
    if (
        empty($appointment_date) ||
        empty($appointment_time) ||
        empty($reason)
    ) {
        $message = "Please complete all the fields.";
        $messageType = "danger";
    } elseif ($appointment_date < date("Y-m-d")) {
        $message = "Please select a valid appointment date.";
        $messageType = "danger";
    } else {
        // Generate the next queue number for the selected date
        $queueQuery = $conn->prepare(
            "SELECT COALESCE(MAX(queue_number), 0) + 1 AS next_number
             FROM appointments
             WHERE appointment_date = ?"
        );
        $queueQuery->bind_param("s", $appointment_date);
        $queueQuery->execute();
        $queueResult = $queueQuery->get_result();
        $queueData = $queueResult->fetch_assoc();
        $queue_number = $queueData['next_number'];
        $queueQuery->close();
        // Save the appointment
        $insertQuery = $conn->prepare(
            "INSERT INTO appointments
            (patient_id, appointment_date, appointment_time, reason, queue_number)
            VALUES (?, ?, ?, ?, ?)"
        );
        $insertQuery->bind_param(
            "isssi",
            $patient_id,
            $appointment_date,
            $appointment_time,
            $reason,
            $queue_number
        );
        if ($insertQuery->execute()) {
            $insertQuery->close();
            // Prepare the SMS notification
            $formattedDate = date(
                "d F Y",
                strtotime($appointment_date)
            );
            $formattedTime = date(
                "h:i A",
                strtotime($appointment_time)
            );
            $smsMessage =
                "Hello " . $patient['name'] .
                ", your ClinicCare appointment is confirmed. " .
                "Date: " . $formattedDate .
                ". Time: " . $formattedTime .
                ". Queue Number: " . $queue_number .
                ". Thank you.";
            // Send SMS through Arkesel
            $smsResult = send_clinic_sms(
                $patient['phone'],
                $smsMessage
            );
            if ($smsResult['success']) {
                $message =
                    "Appointment booked successfully! " .
                    "Your queue number is " . $queue_number .
                    ". An SMS confirmation has been sent.";
                $messageType = "success";
            } else {
                $message =
                    "Your appointment was booked successfully! " .
                    "Your queue number is " . $queue_number .
                    ". However, the SMS could not be sent yet. " .
                    "Please check your phone number, API settings " .
                    "and Sender ID approval.";
                $messageType = "warning";
            }
        } else {
            $message = "Unable to book your appointment. Please try again.";
            $messageType = "danger";
            $insertQuery->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Appointment | ClinicCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body {
        background: #f1edfa;
        font-family: Arial, sans-serif;
    }
    .booking-card {
        max-width: 650px;
        margin: 60px auto;
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .heading {
        color: #51418f;
        font-weight: bold;
    }
    .btn-book {
        background: #168b80;
        color: white;
        border: none;
        padding: 12px;
    }
    .btn-book:hover {
        background: #116e65;
        color: white;
    }
    .form-control {
        padding: 11px;
    }
</style>
</head>
<body>
<div class="container">
<div class="booking-card">
    <div class="text-center mb-4">
        <i class="fas fa-calendar-check fa-3x"
           style="color:#51418f;"></i>
        <h2 class="heading mt-3">
            Book an Appointment
        </h2>
        <p class="text-muted">
            Welcome, <?php echo htmlspecialchars($patient['name']); ?>.
            Complete the form below.
        </p>
    </div>
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">
                Appointment Date
            </label>
            <input
                type="date"
                name="appointment_date"
                class="form-control"
                min="<?php echo date('Y-m-d'); ?>"
                value="<?php echo htmlspecialchars($_POST['appointment_date'] ?? ''); ?>"
                required
            >
        </div>
        <div class="mb-3">
            <label class="form-label">
                Appointment Time
            </label>
            <input
                type="time"
                name="appointment_time"
                class="form-control"
                value="<?php echo htmlspecialchars($_POST['appointment_time'] ?? ''); ?>"
                required
            >
        </div>
        <div class="mb-4">
            <label class="form-label">
                Reason for Appointment
            </label>
            <textarea
                name="reason"
                class="form-control"
                rows="4"
                placeholder="Briefly explain the reason for your visit"
                required><?php echo htmlspecialchars($_POST['reason'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-book w-100">
            <i class="fas fa-calendar-plus me-2"></i>
            Book Appointment
        </button>
    </form>
    <div class="text-center mt-3">
        <a href="dashboard.php"
           class="btn btn-outline-secondary w-100">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Dashboard
        </a>
    </div>
</div>
</div>
