<?php
session_start();
include 'functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Car Rental System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f0e8;
            color: #2d2d2d;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            background-color: #4a6f4e;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .brand {
            color: #fdf8f0;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        nav .nav-links {
            display: flex;
            gap: 12px;
        }

        nav .nav-links a {
            text-decoration: none;
            padding: 7px 18px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        nav .nav-links .btn-login {
            background-color: transparent;
            color: #fdf8f0;
            border: 2px solid #fdf8f0;
        }

        nav .nav-links .btn-login:hover {
            background-color: #fdf8f0;
            color: #4a6f4e;
        }

        nav .nav-links .btn-register {
            background-color: #fdf8f0;
            color: #4a6f4e;
        }

        nav .nav-links .btn-register:hover {
            background-color: #e8e0d0;
        }

        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
        }

        .hero-icon {
            font-size: 72px;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 36px;
            color: #2d2d2d;
            margin-bottom: 14px;
            letter-spacing: 0.5px;
        }

        .hero p {
            font-size: 16px;
            color: #6a6a5a;
            max-width: 480px;
            line-height: 1.7;
            margin-bottom: 36px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .hero-buttons a {
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 7px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .btn-get-started {
            background-color: #7a9e7e;
            color: #fdf8f0;
        }

        .btn-get-started:hover {
            background-color: #608a64;
        }

        .btn-sign-in {
            background-color: #fdf8f0;
            color: #4a6f4e;
            border: 2px solid #7a9e7e;
        }

        .btn-sign-in:hover {
            background-color: #eee8d8;
        }

        .features {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            padding: 40px 20px 60px;
            max-width: 900px;
            margin: 0 auto;
        }

        .feature-card {
            background-color: #fdf8f0;
            border: 1px solid #e0d6c2;
            border-radius: 10px;
            padding: 24px 28px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.07);
            text-align: center;
            flex: 1;
            min-width: 200px;
            max-width: 240px;
        }

        .feature-card .icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .feature-card h3 {
            font-size: 16px;
            color: #2d2d2d;
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 13px;
            color: #7a7a6a;
            line-height: 1.6;
        }

        footer {
            background-color: #4a6f4e;
            color: #d8ecd8;
            text-align: center;
            padding: 14px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">🚗 Car Rental System</div>
    <div class="nav-links">
        <a href="login.php" class="btn-login">Login</a>
        <a href="register.php" class="btn-register">Register</a>
    </div>
</nav>

<div class="hero">
    <div class="hero-icon">🚗</div>
    <h1>Car Rental Transaction System</h1>
    <p>Manage your car rental transactions easily and efficiently. Track rentals, compute totals, and keep all your records organized in one place.</p>
    <div class="hero-buttons">
        <a href="register.php" class="btn-get-started">Get Started</a>
    </div>
</div>

<div class="features">
    <div class="feature-card">
        <div class="icon">📋</div>
        <h3>Track Rentals</h3>
        <p>Add and manage all your car rental transactions in one place.</p>
    </div>
    <div class="feature-card">
        <div class="icon">💰</div>
        <h3>Auto Compute</h3>
        <p>Total rental cost is automatically calculated for every record.</p>
    </div>
    <div class="feature-card">
        <div class="icon">🔒</div>
        <h3>Secure Login</h3>
        <p>Your data is protected with secure authentication and sessions.</p>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Car Rental Transaction System | Built with PHP & PDO
</footer>

</body>
</html>