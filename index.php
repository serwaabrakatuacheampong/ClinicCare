<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clinic Appointment and Queue Management System</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f1edfa;
        color: #333;
    }
    .navbar {
        background: #51418f;
        padding: 18px 40px;
    }
    .navbar-brand {
        color: white;
        font-size: 22px;
        font-weight: bold;
        text-decoration: none;
    }
    .navbar-brand:hover {
        color: white;
    }
    .hero {
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px 20px;
        text-align: center;
    }
    .hero-box {
        max-width: 850px;
        width: 100%;
        background: white;
        padding: 55px 35px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .hospital-icon {
        font-size: 75px;
        color: #51418f;
        margin-bottom: 20px;
    }
    .hero h1 {
        color: #51418f;
        font-size: 35px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .hero p {
        font-size: 17px;
        line-height: 1.7;
        color: #666;
    }
    /* Login button */
    .btn-login {
        background: #168b80;
        color: white;
        padding: 13px 35px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        margin: 10px;
    }
    .btn-login:hover {
        background: #106d64;
        color: white;
    }
    /* Register button */
    .btn-register {
        background: #e6a23c;
        color: white;
        padding: 13px 35px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        margin: 10px;
    }
    .btn-register:hover {
        background: #c8892e;
        color: white;
    }
    .features {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 25px;
        margin-top: 35px;
    }
    .feature {
        color: #51418f;
        font-size: 15px;
    }
    .feature i {
        margin-right: 7px;
    }
    footer {
        background: #51418f;
        color: white;
        text-align: center;
        padding: 15px;
    }
    @media(max-width: 600px) {
        .hero h1 {
            font-size: 27px;
        }
        .hero-box {
            padding: 35px 20px;
        }
        .navbar {
            padding: 15px 20px;
        }
    }
</style>
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar">
    <a class="navbar-brand" href="/Clinic_appointment_queue/index.php">
        <i class="fas fa-hospital"></i>
        ClinicCare
    </a>
</nav>
<!-- Main Homepage Section -->
<section class="hero">
<div class="hero-box">
    <div class="hospital-icon">
        <i class="fas fa-hospital-user"></i>
    </div>
    <h1>Clinic Appointment and Queue Management System</h1>
    <p>
        Welcome to ClinicCare! Our system makes it easier for patients
        to book appointments, receive queue numbers and manage their
        clinic visits conveniently.
    </p>
    <p>
        Save time, reduce waiting and enjoy a more organised
        healthcare experience.
    </p>
    <!-- Login and Register Buttons -->
    <div class="mt-4">
        <a href="/Clinic_appointment_queue/login.php"
           class="btn-login">
            <i class="fas fa-sign-in-alt"></i>
            Login
        </a>
        <a href="/Clinic_appointment_queue/register.php"
           class="btn-register">
            <i class="fas fa-user-plus"></i>
            Register
        </a>
    </div>
    <!-- System Features -->
    <div class="features">
        <div class="feature">
            <i class="fas fa-calendar-check"></i>
            Appointment Booking
        </div>
        <div class="feature">
            <i class="fas fa-users"></i>
            Queue Management
        </div>
        <div class="feature">
            <i class="fas fa-bell"></i>
            SMS Notifications
        </div>
    </div>
</div>
</section>
<!-- Footer -->
<footer>
    &copy; 2026 Clinic Appointment and Queue Management System.
    All Rights Reserved.
</footer>
</body>
</html>