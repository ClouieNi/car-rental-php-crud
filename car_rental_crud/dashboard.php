<?php
session_start();
include 'db.php';
include 'functions.php';

requireLogin();

if (isAdmin()) {
    redirect('admin_dashboard.php');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $records = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching records: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Car Rental System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f0e8;
            color: #2d2d2d;
            min-height: 100vh;
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
        }

        nav .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        nav .nav-right span {
            color: #d8ecd8;
            font-size: 14px;
        }

        .badge {
            background-color: #5a7d9a;
            color: #fdf8f0;
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
            max-width: 980px;
            margin: 36px auto;
            padding: 0 20px;
        }

        .welcome {
            background-color: #fdf8f0;
            border: 1px solid #e0d6c2;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.07);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome h3 {
            font-size: 18px;
            color: #2d2d2d;
        }

        .welcome span {
            font-size: 13px;
            color: #7a7a6a;
        }

        .summary {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .summary-card {
            flex: 1;
            min-width: 160px;
            background-color: #fdf8f0;
            border: 1px solid #e0d6c2;
            border-radius: 10px;
            padding: 18px 22px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.07);
            text-align: center;
        }

        .summary-card h4 {
            font-size: 12px;
            color: #7a7a6a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card p {
            font-size: 24px;
            font-weight: bold;
            color: #4a6f4e;
        }

        .btn-add {
            display: inline-block;
            padding: 9px 18px;
            background-color: #7a9e7e;
            color: #fdf8f0;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 18px;
            transition: background-color 0.2s;
        }

        .btn-add:hover {
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

        .alert-success {
            background-color: #dff0df;
            color: #3a6b3a;
            border: 1px solid #b2d1b2;
        }

        .table-wrapper {
            background-color: #fdf8f0;
            border: 1px solid #e0d6c2;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.07);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #7a9e7e;
            color: #fdf8f0;
            padding: 13px 15px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 11px 15px;
            border-bottom: 1px solid #ece5d5;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f5edd8;
        }

        .no-action {
            color: #aaa;
            font-size: 13px;
            font-style: italic;
        }

        .empty {
            text-align: center;
            padding: 28px;
            color: #888;
            font-size: 15px;
        }

        .empty a {
            color: #5a7d9a;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">🚗 Car Rental System</div>
    <div class="nav-right">
        <span>👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <span class="badge">User</span>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="welcome">
        <h3>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h3>
        <?php if (isset($_COOKIE['last_login'])): ?>
            <span>🕐 Last Login: <?php echo htmlspecialchars($_COOKIE['last_login']); ?></span>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Record added successfully!</div>
    <?php endif; ?>

    <div class="summary">
        <div class="summary-card">
            <h4>My Total Records</h4>
            <p><?php echo count($records); ?></p>
        </div>
        <div class="summary-card">
            <h4>My Total Revenue</h4>
            <p>₱<?php echo number_format(array_sum(array_column($records, 'total')), 2); ?></p>
        </div>
        <div class="summary-card">
            <h4>My Total Rental Days</h4>
            <p><?php echo array_sum(array_column($records, 'rental_days')); ?></p>
        </div>
    </div>

    <a href="create.php" class="btn-add">➕ Add New Record</a>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Car Name</th>
                    <th>Price Per Day (₱)</th>
                    <th>Rental Days</th>
                    <th>Total (₱)</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($records) > 0): ?>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['car_name']); ?></td>
                        <td>₱<?php echo number_format($row['price_per_day'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['rental_days']); ?></td>
                        <td>₱<?php echo number_format($row['total'], 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty">No records found. <a href="create.php">Add one now!</a></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>