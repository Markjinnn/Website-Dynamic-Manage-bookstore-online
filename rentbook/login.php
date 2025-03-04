<?php
include('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            echo ".";
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'เข้าสู่ระบบสำเร็จ!',
                    text: 'ยินดีต้อนรับ, $username!',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location = 'index.php';
                });
            </script>";
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบผู้ใช้นี้";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTBOOK ZONE</title>
    <link rel="stylesheet" href="css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="form-container">
        <h2>เข้าสู่ระบบ</h2>

        <?php if (isset($error)): ?>
            <script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '<?= $error ?>',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            </script>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">เข้าสู่ระบบ</button>
            <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
        </form>
    </div>
</body>
</html>


