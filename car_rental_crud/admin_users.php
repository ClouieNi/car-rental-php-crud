<?php
session_start();
include 'db.php';
include 'functions.php';

requireLogin();
requireAdmin();

try {
    $stmt = $pdo->prepare("
        SELECT u.*, COUNT(t.id) as total_records 
        FROM users u 
        LEFT JOIN transactions t ON u.id = t.user_id 
        WHERE u.role = 'user'
        GROUP BY u.id 
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Car Rental System</title>
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
            max-width: 980px;
            margin: 36px auto;
            padding: 0 20px;
        }

        h2 {
            font-size: 22px;
            color: #2d2d2d;
            margin-bottom: 24px;
        }

        .alert {
            padding: 11px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
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

        .record-count {
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
        <span>👑 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <span class="badge-admin">Admin</span>
        <a href="admin_dashboard.php" class="nav-link">📋 Dashboard</a>
        <a href="logout.php" class="nav-logout">Logout</a>
    </div>
</nav>

<div class="container">

    <h2>👥 Manage Users</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-deleted">🗑️ User deleted successfully!</div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Total Records</th>
                    <th>Date Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td>👤 <?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="record-count"><?php echo $user['total_records']; ?> records</span></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                               class="btn btn-delete"
                               onclick="return confirm('Delete this user and all their records?')">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>