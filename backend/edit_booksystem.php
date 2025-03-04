<?php

session_start();
include('../config.php');
include('notifyonbackend.php');

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $cover_image_url = $_POST['cover_image']; 

    $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, description = ?, price = ?, cover_image = IF(?='',cover_image,?) WHERE id = ?");
    $stmt->bind_param('ssssdsi', $title, $author, $description, $price, $cover_image_url, $cover_image_url, $id);
    
    if ($stmt->execute()) {
        echo ".";
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'อัปเดตสำเร็จ!',
                text: 'ข้อมูลหนังสือถูกอัปเดตเรียบร้อยแล้ว',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then(function() {
                window.location = 'edit_book.php';
            });
        </script>";
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการแก้ไขหนังสือ: " . $stmt->error;
    }
} else {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
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
                <h1>E-Book Admin</h1>
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
        <div class="edit">
            <h1>แก้ไขหนังสือ</h1>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $book['id'] ?>">
                <div class="form-group">
                    <label for="title">ชื่อหนังสือ:</label>
                    <input type="text" id="title" name="title" value="<?= $book['title'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="author">ผู้แต่ง:</label>
                    <input type="text" id="author" name="author" value="<?= $book['author'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">คำอธิบาย:</label>
                    <textarea id="description" name="description" required><?= $book['description'] ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">ราคา:</label>
                    <input type="number" id="price" step="0.01" name="price" value="<?= $book['price'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="cover_image">URL ปกหนังสือ:</label>
                    <input type="url" id="cover_image" name="cover_image" value="<?= $book['cover_image'] ?>">
                </div>
                <button type="submit" name="update" class="btn">อัปเดตหนังสือ</button>
            </form>
        </div>
    </main>

    <footer>
        <p>Create By Watcharapol &copy; 2024</p>
    </footer>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="../js/usermenu.js"></script>

</body>
</html>
