<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "อีเมลหรือชื่อผู้ใช้นี้ถูกใช้แล้ว";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $email, $username, $password);
        if ($stmt->execute()) {
            echo "."; 
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'สมัครสมาชิกสำเร็จ!',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function() {
                        window.location = 'login.php';
                    });
                </script>";
            exit();
        } else {
            $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก";
        }
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
    <script>
        function validateForm() {
            var username = document.getElementById("username").value;
            var usernamePattern = /^[a-zA-Z]+$/;
            if (!usernamePattern.test(username)) {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ชื่อผู้ใช้ต้องเป็นภาษาอังกฤษเท่านั้น (A-Z, a-z)',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
                return false; 
            }
            return true; 
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>สมัครสมาชิก</h2>
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
        <form method="POST" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">สมัครสมาชิก</button>
            <p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
        </form>
    </div>
</body>
</html>