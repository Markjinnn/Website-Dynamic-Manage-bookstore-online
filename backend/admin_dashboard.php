<?php
session_start();
include('../config.php');
include('notifyonbackend.php');

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTBOOK ZONE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>RENTBOOK Admin</h1>
            </div>
            <div class="menu">
                <a href="../index.php" class="nav-link">หน้าหลัก</a>
                <a href="../book.php" class="nav-link">หนังสือ</a>
                <a href="../top_up.php" class="nav-link">เติมเงิน</a>
            </div>
            <div class="user-menu">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i>  <i class="fas fa-caret-down"></i>
                    </div>
                    <div class="dropdown">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="admin_dashboard.php"><i class="fas fa-tools"></i> Admin Dashboard</a>
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
                        <a href="../user_dashboard.php"><i class="fas fa-user"></i> User Dashboard</a>
                        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                    </div>
                <?php else: ?>
                    <a href="../login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                    <a href="../register.php" class="register-btn"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <div class="dashboard-container">
            <div class="dashboard-sections">
                <div class="dashboard-section">
                    <h2>เพิ่มหนังสือ</h2>
                    <p>เพิ่มข้อมูลหนังสือในระบบ</p>
                    <a href="manage_books.php" class="btn">จัดการหนังสือ</a>
                </div>
                <div class="dashboard-section">
                    <h2>แก้ไขคลังหนังสือ</h2>
                    <p>แก้ไขคลังหนังสือ เพิ่มสต๊อกหนังสือ</p>
                    <a href="edit_book.php" class="btn">จัดการหนังสือ</a>
                </div>
                <div class="dashboard-section">
                    <h2>จัดการสมาชิก</h2>
                    <p>จัดการสมาชิกในระบบ</p>
                    <a href="manage_user.php" class="btn">จัดการสมาชิก</a>
                </div>
            </div>
        </div>
    </main>

    <main>
        <div class="dashboard-container">
            <div class="dashboard-sections">
                <div class="dashboard-section">
                    <h2>คำขอเช่าหนังสือ</h2>
                    <p>ยืนยันคำขอ</p>
                    <a href="manage_rent_requests.php" class="btn">จัดการคำขอเช่าหนังสือ</a>
                </div>
                
                <div class="dashboard-section">
                    <h2>คำขอเติมเงิน</h2>
                    <p>ยืนยันคำขอ</p>
                    <a href="topupbackend.php" class="btn">จัดการคำขอเติมเงิน</a>
                </div>

            </div>
        </div>
    </main>



    <footer>
        <p>Create By Watcharapol &copy; 2024</p>
    </footer>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="../js/usermenu.js"></script>

</body>
</html>
