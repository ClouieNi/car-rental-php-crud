<?php
session_start();
include 'db.php';
include 'functions.php';

requireLogin();
requireAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('admin_users.php');
}

$id = $_GET['id'];

// Prevent admin from deleting own account
if ($id == $_SESSION['user_id']) {
    redirect('admin_users.php?error=self_delete');
}

try {
    // Delete user's transactions first
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE user_id = :id");
    $stmt->execute([':id' => $id]);

    // Then delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'user'");
    $stmt->execute([':id' => $id]);

    redirect('admin_users.php?deleted=1');

} catch (PDOException $e) {
    die("Error deleting user: " . $e->getMessage());
}
?>