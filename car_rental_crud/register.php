<?php
session_start();
include 'db.php';
include 'functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = "❌ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "❌ Passwords do not match.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $error = "❌ Email is already registered.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->execute([
                    ':username' => $username,
                    ':email'    => $email,
                    ':password' => $hashed
                ]);

                header("Location: login.php?registered=1");
                exit();
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Car Rental System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f0e8;
            color: #2d2d2d;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .wrapper {
            width: 100%;
            max-width: 480px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            color: #2d2d2d;
            margin-bottom: 24px;
            letter-spacing: 0.5px;
        }

        .card {
            background-color: #fdf8f0;
            border: 1px solid #e0d6c2;
            border-radius: 10px;
            padding: 32px 28px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border: 1px solid #c9bfa8;
            border-radius: 6px;
            background-color: #fffdf7;
            color: #2d2d2d;
            font-size: 15px;
            transition: border 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #7a9e7e;
            background-color: #f5faf5;
        }

        input[type="submit"] {
            width: 100%;
            padding: 11px;
            background-color: #7a9e7e;
            color: #fdf8f0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s;
            margin-top: 4px;
        }

        input[type="submit"]:hover {
            background-color: #608a64;
        }

        .alert {
            padding: 11px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        .alert-error {
            background-color: #f5e0e0;
            color: #7a3030;
            border: 1px solid #d9aaaa;
        }

        .nav {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .nav a {
            text-decoration: none;
            color: #5a7d9a;
            font-weight: bold;
            transition: color 0.2s;
        }

        .nav a:hover {
            color: #3a5f7a;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="wrapper">

    <h2>🚗 Car Rental System</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card">
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username"
                   placeholder="e.g. carllouise" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email"
                   placeholder="e.g. carl@email.com" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password"
                   placeholder="Minimum 6 characters" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password"
                   placeholder="Re-enter your password" required>

            <input type="submit" value="Register">
        </form>
    </div>

    <div class="nav">
        Already have an account? <a href="login.php">Login here</a>
    </div>

</div>
</body>
</html>