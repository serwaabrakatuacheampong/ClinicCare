<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role FROM users WHERE email = ?"
        );

        if (!$stmt) {
            die("Database query failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Incorrect email or password.";

            }

        } else {

            $message = "Incorrect email or password.";

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

    <title>Login - ClinicCare</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        body {
            background: #f1edfa;
            font-family: Arial, sans-serif;
        }

        .login-box {
            max-width: 450px;
            margin: 70px auto;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .hospital-icon {
            font-size: 55px;
            color: #51418f;
            text-align: center;
            margin-bottom: 15px;
        }

        h2 {
            color: #51418f;
            text-align: center;
            margin-bottom: 25px;
        }

        .btn-login {
            background: #168b80;
            color: white;
            border: none;
            padding: 12px;
            font-weight: bold;
        }

        .btn-login:hover {
            background: #106d64;
            color: white;
        }

        .form-control {
            padding: 11px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="login-box">

        <div class="hospital-icon">
            <i class="fas fa-hospital-user"></i>
        </div>

        <h2>Welcome Back</h2>

        <p class="text-center text-muted">
            Login to your ClinicCare account
        </p>

        <?php if (!empty($message)): ?>

            <div class="alert alert-danger">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="mb-3">

                <label class="form-label">Email Address</label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter your email"
                    required
                >

            </div>

            <div class="mb-3">

                <label class="form-label">Password</label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button type="submit" class="btn btn-login w-100">

                <i class="fas fa-sign-in-alt"></i>
                Login

            </button>

        </form>

        <p class="text-center mt-4">

            Don't have an account?

            <a href="register.php">Register here</a>

        </p>

        <a href="index.php" class="btn btn-outline-secondary w-100">

            <i class="fas fa-home"></i>
            Back to Home

        </a>

    </div>

</div>

</body>
</html>