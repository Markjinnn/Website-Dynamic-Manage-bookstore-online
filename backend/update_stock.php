<?php
include('../config.php');
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$book_id = $_POST['book_id'];
$stock = $_POST['stock'];

$stmt = $conn->prepare("UPDATE books SET stock = ? WHERE id = ?");
$stmt->bind_param('ii', $stock, $book_id);
$stmt->execute();
$stmt->close();

header('Location: edit_book.php');
exit();
?>
