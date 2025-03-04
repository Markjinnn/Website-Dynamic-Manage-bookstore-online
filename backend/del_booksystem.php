<?php
include('../config.php');
session_start();

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // ลบรายการ user_books
    $stmt = $conn->prepare("DELETE FROM user_books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->close();

    // ลบหนังสือจาก books 
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('ลบหนังสือสำเร็จ');
            window.location.href = 'edit_book.php';
        </script>";
    } else {
        echo "<script>
            alert('เกิดข้อผิดพลาดในการลบหนังสือ');
            window.location.href = 'edit_book.php';
        </script>";
    }

    $stmt->close();
} else {
    header("Location: edit_book.php");
    exit();
}
?>
