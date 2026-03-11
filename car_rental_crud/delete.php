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

try {
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id");
    $stmt->execute([':id' => $id]);

    redirect('admin_dashboard.php?deleted=1');

} catch (PDOException $e) {
    die("Error deleting record: " . $e->getMessage());
}
?>