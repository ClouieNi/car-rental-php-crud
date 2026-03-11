<?php
session_start();
include 'db.php';
include 'functions.php';

requireLogin();

if (!isAdmin()) {
    redirect('dashboard.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('admin_dashboard.php');
}

$id = $_GET['id'];
$error = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch();

    if (!$record) {
        redirect('admin_dashboard.php');
    }
} catch (PDOException $e) {
    die("Error fetching record: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_name      = trim($_POST['car_name']);
    $price_per_day = $_POST['price_per_day'];
    $rental_days   = $_POST['rental_days'];

    if (empty($car_name) || $price_per_day === '' || $rental_days === '') {
        $error = "❌ All fields are required.";
    } elseif (!validateNumber($price_per_day)) {
        $error = "❌ Price per day cannot be negative.";
    } elseif (!validateNumber($rental_days)) {
        $error = "❌ Rental days cannot be negative.";
    } else {
        $total = computeTotal($price_per_day, $rental_days);

        try {
            $sql = "UPDATE transactions
                    SET car_name = :car_name,
                        price_per_day = :price_per_day,
                        rental_days = :rental_days,
                        total = :total
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':car_name'      => $car_name,
                ':price_per_day' => $price_per_day,
                ':rental_days'   => $rental_days,
                ':total'         => $total,
                ':id'            => $id
            ]);

            redirect('admin_dashboard.php?updated=1');

        } catch (PDOException $e) {
            die("Error updating record: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Record | Car Rental System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f0e8;
            color: #2d2d2d;
            min-height: 100vh;
        }

        nav {
            background-color: #5a3e6b;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .brand {
            color: #fdf8f0;
            font-size: 18px;
            font-weight: bold;
        }

        nav .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        nav .nav-right span {
            color: #e0d0f0;
            font-size: 14px;
        }

        .badge-admin {
            background-color: #c4a000;
            color: #2d2d2d;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        nav .nav-right a {
            color: #fdf8f0;
            text-decoration: none;
            background-color: #b85c5c;
            padding: 7px 14px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        nav .nav-right a:hover {
            background-color: #8f3a3a;
        }

        .container {
            max-width: 620px;
            margin: 36px auto;
            padding: 0 20px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            color: #2d2d2d;
            margin-bottom: 24px;
        }

        .card {
            background-color: #fdf8f0;
            border: 1px solid #e0d6c2;
            border-radius: 10px;
            padding: 28px;
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
        input[type="number"] {
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
        input[type="number"]:focus {
            outline: none;
            border-color: #5a7d9a;
            background-color: #f0f5fa;
        }

        input[type="submit"] {
            width: 100%;
            padding: 11px;
            background-color: #5a7d9a;
            color: #fdf8f0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        input[type="submit"]:hover {
            background-color: #3a5f7a;
        }

        .alert-error {
            background-color: #f5e0e0;
            color: #7a3030;
            border: 1px solid #d9aaaa;
            padding: 11px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        .nav {
            text-align: center;
            margin-top: 18px;
        }

        .nav a {
            text-decoration: none;
            color: #5a7d9a;
            font-weight: bold;
            font-size: 15px;
        }

        .nav a:hover {
            color: #3a5f7a;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">🚗 Car Rental System</div>
    <div class="nav-right">
        <span>👑 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <span class="badge-admin">Admin</span>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <h2>✏️ Update Rental Record</h2>

    <?php if ($error): ?>
        <div class="alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card">
        <form action="update.php?id=<?php echo $record['id']; ?>" method="POST">
            <label for="car_name">Car Name:</label>
            <input type="text" name="car_name" id="car_name"
                   value="<?php echo htmlspecialchars($record['car_name']); ?>" required>

            <label for="price_per_day">Price Per Day (₱):</label>
            <input type="number" name="price_per_day" id="price_per_day"
                   step="0.01" min="0"
                   value="<?php echo htmlspecialchars($record['price_per_day']); ?>" required>

            <label for="rental_days">Rental Days:</label>
            <input type="number" name="rental_days" id="rental_days"
                   min="0"
                   value="<?php echo htmlspecialchars($record['rental_days']); ?>" required>

            <input type="submit" value="Update Record">
        </form>
    </div>

    <div class="nav">
        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

</div>
</body>
</html>