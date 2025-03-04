<?php
session_start();
include('config.php');
include('notify.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];

    if (isset($_FILES['slip']) && $_FILES['slip']['error'] === UPLOAD_ERR_OK) {
        $slip = $_FILES['slip'];
        $slip_name = $slip['name'];
        $slip_tmp_name = $slip['tmp_name'];
        $slip_ext = strtolower(pathinfo($slip_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($slip_ext, $allowed_ext)) {
            $slip_new_name = 'slip_' . time() . '.' . $slip_ext;
            $slip_path = 'uploads/slips/' . $slip_new_name;
            
            if (move_uploaded_file($slip_tmp_name, $slip_path)) {
                if ($amount > 0) {
                    $stmt = $conn->prepare("INSERT INTO top_up_requests (user_id, amount, slip) VALUES (?, ?, ?)");
                    $stmt->bind_param('ids', $user_id, $amount, $slip_new_name);
                    if ($stmt->execute()) {
                        $stmt->close();                        
                        $message = "เติมเงินสำเร็จ! โปรดรอการตรวจสอบสลิป.";
                        $alert_type = 'success';
                    } else {
                        $message = "เกิดข้อผิดพลาดในการเติมเงิน.";
                        $alert_type = 'error';
                    }
                } else {
                    $message = "จำนวนเงินไม่ถูกต้อง.";
                    $alert_type = 'warning';
                }
            } else {
                $message = "เกิดข้อผิดพลาดในการอัปโหลดสลิป.";
                $alert_type = 'error';
            }
        } else {
            $message = "โปรดอัปโหลดไฟล์รูปภาพเท่านั้น.";
            $alert_type = 'error';
        }
    } else {
        $message = "กรุณาอัปโหลดสลิปการเติมเงิน.";
        $alert_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTBOOK ZONE</title>
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
                <a href="index.php" class="nav-link" >หน้าหลัก</a>
                <a href="book.php" class="nav-link">หนังสือ</a>
                <a href="top_up.php" class="nav-link">เติมเงิน</a>
            </div>
            <div class="user-menu">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i>  <i class="fas fa-caret-down"></i>
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

    <main>
        <section class="top-up-section">
            <h2>เติมเงิน</h2>
            <form method="POST" action="top_up.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="amount">จำนวนเงินที่ต้องการเติม (บาท):</label>
                    <input type="number" name="amount" id="amount" min="1" required>
                </div>
                <div class="form-group">
                    <label for="slip">อัปโหลดสลิปการเติมเงิน:</label>
                    <input type="file" name="slip" id="slip" accept="image/*" required>
                </div>
                <button type="submit" class="btn">เติมเงิน</button>
                <br><br>
                <center>
                    <h3>โปรดสแกน QR Code ด้านล่างเพื่อโอนเงิน</h3>
                    <br>
                    <img src="images/topup.png" alt="QR Code" class="qr-code" width="200px" height="200px">
                    <br>
                    <p><strong>ชื่อบัญชี:</strong> นาย สมชาย ใจดี</p>
                    <p><strong>ธนาคาร:</strong> กรุงเทพ 123-4-56789-0</p>
                </center>
            </form>
        </section>
    </main>

    <footer>
        <p>Create By Watcharapol &copy; 2024</p>
    </footer>
    </div>
    <?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            icon: '<?= $alert_type ?>',
            title: '<?= $message ?>',
            confirmButtonText: 'ตกลง'
        });
    </script>
    <?php endif; ?>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="js/usermenu.js"></script>
    <script src="js/slide.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.globe.min.js"></script>
    <script src="js/vanta.js"></script>

</body>
</html>
