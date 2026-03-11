<?php
function validateNumber($value) {
    if (!is_numeric($value) || $value < 0) {
        return false;
    }
    return true;
}

function computeTotal($price_per_day, $rental_days) {
    return $price_per_day * $rental_days;
}

function redirect($page) {
    header("Location: $page");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('dashboard.php');
    }
}
?>