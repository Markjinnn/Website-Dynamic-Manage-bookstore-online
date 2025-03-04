<?php
include('config.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['rental_id'])) {
    echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
    exit();
}

$rental_id = $_POST['rental_id'];

$stmt = $conn->prepare("UPDATE books SET stock = stock + 1 WHERE id = (SELECT book_id FROM user_books WHERE id = ?)");
$stmt->bind_param('i', $rental_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM user_books WHERE id = ?");
$stmt->bind_param('i', $rental_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>
