<?php
include('../config.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM user_books WHERE status = 'Pending'");
$stmt->execute();
$stmt->bind_result($pending_count);
$stmt->fetch();
$stmt->close();

echo json_encode(['status' => 'success', 'pending_count' => $pending_count]);
?>
