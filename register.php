<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$message = "";
require_once __DIR__ . '/config/database.php';

if (!isset($conn)) {
    die("ERROR: Database connection variable is missing.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || $phone === '' || $password === '') {

        $message = "Please complete all fields.";

    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, phone, password)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $phone,
            $hashedPassword
        );

        if ($stmt->execute()) {
            $message = "Registration successful! You can now log in.";
        } else {
            $message = "Registration failed. This email may already exist.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f4f1fa;
        }

        .register-box {
            max-width: 450px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .btn-purple {
            background: #5145a5;
            color: white;
        }

        .btn-purple:hover {
            background: #403580;
            color: white;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="register-box">

        <h2 class="text-center mb-4">Patient Registration</h2>

        <?php if ($message !== ''): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name"
                       class="form-control"
                       placeholder="Enter your full name"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email"
                       class="form-control"
                       placeholder="Enter your email"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone"
                       class="form-control"
                       placeholder="Enter your phone number"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password"
                       class="form-control"
                       placeholder="Create a password"
                       required>
            </div>

            <button type="submit"
                    class="btn btn-purple w-100">
                Register
            </button>

        </form>

        <p class="text-center mt-3">
            Already registered?
            <a href="login.php">Login here</a>
        </p>

    </div>

</div>

</body>
</html>