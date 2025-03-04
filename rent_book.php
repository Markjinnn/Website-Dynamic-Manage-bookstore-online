<?php
include('config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];
$rental_period = $_POST['rental_period'];

$stmt = $conn->prepare("SELECT price, stock FROM books WHERE id = ?");
$stmt->bind_param('i', $book_id);
$stmt->execute();
$stmt->bind_result($book_price, $stock);
$stmt->fetch();
$stmt->close();

if ($stock <= 0) {
    echo "";
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'ไม่สามารถจองหนังสือได้',
            text: 'หนังสือเล่มนี้หมดสต๊อกแล้ว',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        }).then(function() {
            window.location = 'index.php';
        });
    </script>";
    exit();
}

// คำนวณราคาทั้งหมด
$total_price = $book_price * ($rental_period === 'lifetime' ? 100 : $rental_period);

// ตรวจสอบยอดเงิน
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($user_balance);
$stmt->fetch();
$stmt->close();

if ($user_balance < $total_price) {
    echo ".";
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'ยอดเงินไม่เพียงพอ',
            text: 'ยอดเงินในบัญชีของคุณไม่พอสำหรับการจองหนังสือเล่มนี้',
            icon: 'warning',
            confirmButtonText: 'ตกลง'
        }).then(function() {
            window.location = 'user_dashboard.php';
        });
    </script>";
    exit();
}
//

$rent_date = new DateTime();
$expire_date = new DateTime();

if ($rental_period === 'lifetime') {
    $expire_date = NULL; 
} else {
    $expire_date->modify("+$rental_period days");
}

$rent_date = $rent_date->format('Y-m-d H:i:s');
$expire_date = $expire_date ? $expire_date->format('Y-m-d H:i:s') : NULL;

$stmt = $conn->prepare("UPDATE books SET stock = stock - 1 WHERE id = ?");
$stmt->bind_param('i', $book_id);
$stmt->execute();
$stmt->close();

// บันทึกข้อมูลจอง
$stmt = $conn->prepare("INSERT INTO user_books (user_id, book_id, rent_date, expire_date, rental_period, status, rental_price) VALUES (?, ?, ?, ?, ?, 'Pending', ?)");
$stmt->bind_param('iissid', $user_id, $book_id, $rent_date, $expire_date, $rental_period, $total_price);

if ($stmt->execute()) {
    echo ".";
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'คำขอจองสำเร็จ!',
            text: 'คำขอจองของคุณถูกส่งแล้ว โปรดรอการยืนยันจากแอดมิน',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then(function() {
            window.location = 'user_dashboard.php';
        });
    </script>";
} else {
    echo ".";
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'เกิดข้อผิดพลาดในการทำคำขอจอง',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        }).then(function() {
            window.location = 'index.php';
        });
    </script>";
}

$stmt->close();
?>
