<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'], $_POST['book_id'], $_POST['rating'])) {
        $user_id = $_SESSION['user_id'];
        $book_id = $_POST['book_id'];
        $rating = intval($_POST['rating']);

        if ($rating < 1 || $rating > 5) {
            die('คะแนนต้องอยู่ระหว่าง 1 ถึง 5');
        }

        // บันทึกคะแนนลงในตาราง reviews
        $stmt = $conn->prepare("INSERT INTO reviews (book_id, user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?");
        $stmt->bind_param('iiii', $book_id, $user_id, $rating, $rating);
        $stmt->execute();
        $stmt->close();

        // อัปเดตสถานะ reviewed = 1
        $stmt_update = $conn->prepare("UPDATE user_books SET reviewed = 1 WHERE user_id = ? AND book_id = ?");
        $stmt_update->bind_param('ii', $user_id, $book_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Redirect หลังให้คะแนนสำเร็จ
        header('Location: user_dashboard.php?success=ให้คะแนนสำเร็จ');
        exit();
    } else {
        die('ข้อมูลไม่ครบถ้วน');
    }
}
?>
