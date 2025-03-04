<?php
session_start();
include('config.php');
include('notify.php');

if (!isset($_GET['id'])) {
    header('Location: book.php');
    exit();
}

$book_id = $_GET['id'];


$stmt = $conn->prepare("SELECT books.*, categories.category_name FROM books 
                        LEFT JOIN categories ON books.category_id = categories.id
                        WHERE books.id = ?");
$stmt->bind_param('i', $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    echo "<script>alert('ไม่พบหนังสือ'); window.location='book.php';</script>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <div id="vanta">

    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>RENTBOOK ZONE</h1>
            </div>
            <div class="menu">
                <a href="index.php" class="nav-link">หน้าหลัก</a>
                <a href="book.php" class="nav-link">หนังสือ</a>
                <a href="top_up.php" class="nav-link">เติมเงิน</a>
            </div>
            <div class="user-menu">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i> <i class="fas fa-caret-down"></i>
                    </div>
                    <div class="dropdown">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="backend/admin_dashboard.php"><i class="fas fa-tools"></i> Admin Dashboard</a>
                        <?php endif; ?>
                        <?php
                            $user_id = $_SESSION['user_id']; 
                            $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                            $stmt->bind_param('i', $user_id);
                            $stmt->execute();
                            $stmt->bind_result($balance);
                            $stmt->fetch();
                            $stmt->close();
                        ?>
                        <a href="#"><i class="fas fa-coins"></i> ยอดคงเหลือ: <?= number_format($balance, 2) ?> บาท</a>
                        <a href="user_dashboard.php"><i class="fas fa-user"></i> User Dashboard</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                    <a href="register.php" class="register-btn"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <center>
            <div class="book-details">
                <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="ปกหนังสือ">
                <h1><?= htmlspecialchars($book['title']) ?></h1>
                <h1>รายละเอียดสินค้า</h1>
                <h3>ผู้แต่ง: <?= htmlspecialchars($book['author']) ?></h3>
                <h4>หมวดหมู่: <?= htmlspecialchars($book['category_name']) ?></h4>
                <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                <form id="rentForm_<?= $book['id'] ?>" action="rent_book.php" method="POST">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">             
                    <label for="rental_period">เลือกระยะเวลาการเช่า:</label>
                    <select name="rental_period" id="rental_period_<?= $book['id'] ?>">
                        <option value="1">1 วัน - <?= $book['price'] * 1 ?> บาท</option>
                        <option value="3">3 วัน - <?= $book['price'] * 3 ?> บาท</option>
                        <option value="7">7 วัน - <?= $book['price'] * 7 ?> บาท</option>
                        <option value="30">1 เดือน - <?= $book['price'] * 30 ?> บาท</option>
                        <option value="lifetime">ตลอดชีพ - <?= $book['price'] * 100 ?> บาท</option>
                    </select>
                    <br>
                    <button type="submit" class="rent-button">เช่าหนังสือ</button>
                </form>
            </div>
    </center>
    <footer>
        <p>Create By Watcharapol &copy; 2024</p>
    </footer>
    
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="js/usermenu.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.globe.min.js"></script>
    <script src="js/vanta.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rentForm = document.getElementById('rentForm_<?= $book['id'] ?>');
            rentForm.addEventListener('submit', function(event) {
                event.preventDefault(); 

                Swal.fire({
                    title: 'ยืนยันการจองหนังสือ',
                    text: 'คุณแน่ใจหรือไม่ที่ต้องการจองหนังสือนี้?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                }).then((result) => {
                    if (result.isConfirmed) {
                        rentForm.submit(); 
                    }
                });
            });
        });
    </script>
    
</body>
</html>
