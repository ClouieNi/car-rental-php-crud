<?php
session_start();
include 'db.php';
include 'functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin_dashboard.php' : 'dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = "❌ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                if ($remember) {
                    setcookie('last_login', date('Y-m-d H:i:s'), time() + (86400 * 30), '/');
                } else {
                    setcookie('last_login', date('Y-m-d H:i:s'), time() + 3600, '/');
                }

                redirect($user['role'] === 'admin' ? 'admin_dashboard.php' : 'dashboard.php');
            } else {
                $error = "❌ Invalid email or password.";
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
    <title>Login | Car Rental System</title>
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
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 440px;
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

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #7a9e7e;
            background-color: #f5faf5;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            color: #555;
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #7a9e7e;
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

        .alert-success {
            background-color: #dff0df;
            color: #3a6b3a;
            border: 1px solid #b2d1b2;
        }

        .cookie-info {
            background-color: #eef4fb;
            border: 1px solid #b8d0e8;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13px;
            color: #3a5f7a;
            margin-bottom: 18px;
            text-align: center;
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
<div class="container">

    <h2>🚗 Car Rental System</h2>

    <?php if (isset($_COOKIE['last_login'])): ?>
        <div class="cookie-info">
            🕐 Last Login: <?php echo htmlspecialchars($_COOKIE['last_login']); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">✅ Registered successfully! Please login.</div>
    <?php endif; ?>

    <div class="card">
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email"
                   placeholder="e.g. carl@email.com" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password"
                   placeholder="Enter your password" required>

            <div class="remember">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin:0; font-weight:normal;">Remember Me (30 days)</label>
            </div>

            <input type="submit" value="Login">
        </form>
    </div>

    <div class="nav">
        Don't have an account? <a href="register.php">Register here</a>
    </div>

</div>
</body>
</html>