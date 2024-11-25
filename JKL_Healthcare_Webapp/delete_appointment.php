<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle appointment deletion
if (isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

// Redirect to dashboard after deletion
header("Location: admin_dashboard.php");
exit();
?>
