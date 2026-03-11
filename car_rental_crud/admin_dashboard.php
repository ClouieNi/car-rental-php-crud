<?php
session_start();
include 'db.php';
include 'functions.php';

requireLogin();
requireAdmin();

try {
    $stmt = $pdo->prepare("
        SELECT t.*, u.username 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.id DESC
    ");
    $stmt->execute();
    $records = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching records: " . $e->getMessage());
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
    $stmt->execute();
    $total_users = $stmt->fetch()['total_users'];
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Car Rental System</title>
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

        .nav-link {
            color: #fdf8f0;
            text-decoration: none;
            background-color: #7a5a9a;
            padding: 7px 14px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .nav-link:hover {
            background-color: #5a3e7a;
        }

        .nav-logout {
            color: #fdf8f0;
            text-decoration: none;
            background-color: #b85c5c;
            padding: 7px 14px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .nav-logout:hover {
            background-color: #8f3a3a;
        }

        .container {
            max-width: 1100px;
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
            color: #5a3e6b;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d2d2d;
            margin-bottom: 14px;
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

        .alert-deleted {
            background-color: #f5e0e0;
            color: #7a3030;
            border: 1px solid #d9aaaa;
        }

        .table-wrapper {
            background-color: #fdf8f0;
            border: 1px solid #e0d6c2;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.07);
            margin-bottom: 36px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #5a3e6b;
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

        .btn {
            display: inline-block;
            padding: 6px 13px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            transition: background-color 0.2s;
            margin-right: 4px;
        }

        .btn-edit {
            background-color: #5a7d9a;
            color: #fdf8f0;
        }

        .btn-edit:hover {
            background-color: #3a5f7a;
        }

        .btn-delete {
            background-color: #b85c5c;
            color: #fdf8f0;
        }

        .btn-delete:hover {
            background-color: #8f3a3a;
        }

        .empty {
            text-align: center;
            padding: 28px;
            color: #888;
            font-size: 15px;
        }

        .user-tag {
            background-color: #eef4fb;
            color: #3a5f7a;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">🚗 Car Rental System</div>
    <div class="nav-right">
        <span class="badge-admin">Admin</span>
        <a href="admin_users.php" class="nav-link">👥 Manage Users</a>
        <a href="logout.php" class="nav-logout">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="welcome">
        <h3>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>! 👑</h3>
        <?php if (isset($_COOKIE['last_login'])): ?>
            <span>🕐 Last Login: <?php echo htmlspecialchars($_COOKIE['last_login']); ?></span>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">✅ Record updated successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-deleted">🗑️ Record deleted successfully!</div>
    <?php endif; ?>

    <div class="summary">
        <div class="summary-card">
            <h4>Total Records</h4>
            <p><?php echo count($records); ?></p>
        </div>
        <div class="summary-card">
            <h4>Total Revenue</h4>
            <p>₱<?php echo number_format(array_sum(array_column($records, 'total')), 2); ?></p>
        </div>
        <div class="summary-card">
            <h4>Total Rental Days</h4>
            <p><?php echo array_sum(array_column($records, 'rental_days')); ?></p>
        </div>
        <div class="summary-card">
            <h4>Total Users</h4>
            <p><?php echo $total_users; ?></p>
        </div>
    </div>

    <div class="section-title">📋 All Rental Records</div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Car Name</th>
                    <th>Price Per Day (₱)</th>
                    <th>Rental Days</th>
                    <th>Total (₱)</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($records) > 0): ?>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><span class="user-tag">👤 <?php echo htmlspecialchars($row['username']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['car_name']); ?></td>
                        <td>₱<?php echo number_format($row['price_per_day'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['rental_days']); ?></td>
                        <td>₱<?php echo number_format($row['total'], 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">✏️ Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-delete"
                               onclick="return confirm('Are you sure you want to delete this record?')">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty">No records found in the system.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>