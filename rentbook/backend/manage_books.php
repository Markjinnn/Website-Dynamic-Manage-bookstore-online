<?php
session_start();
include('../config.php');
include('notifyonbackend.php');

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

error_reporting(E_ERROR | E_PARSE);

// เพิ่มหมวดหมู่
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param('s', $category_name);
        if ($stmt->execute()) {
            echo ".";
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'เพิ่มหมวดหมู่สำเร็จ!',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location = 'manage_books.php';
                });
            </script>";
        }
    }
}

// เพิ่มหนังสือ
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cover_image_url = $_POST['cover_image_url'];
    $category_id = $_POST['category_id']; 

    $stmt = $conn->prepare("INSERT INTO books (title, author, description, cover_image, price, stock, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssdis', $title, $author, $description, $cover_image_url, $price, $stock, $category_id);
    
    if ($stmt->execute()) {
        echo ".";
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'เพิ่มหนังสือสำเร็จ!',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then(function() {
                window.location = 'manage_books.php';
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
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
        <div class="Purm">
            <h1>เพิ่มหมวดหมู่หนังสือ</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="category_name">ชื่อหมวดหมู่:</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>
                <button type="submit" name="add_category" class="btn">เพิ่มหมวดหมู่</button>
            </form>
        </div>
                <br>
        <div class="Purm">
            <h1>เพิ่มหนังสือใหม่</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="title">ชื่อหนังสือ:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="author">ผู้แต่ง:</label>
                    <input type="text" id="author" name="author" required>
                </div>
                <div class="form-group">
                    <label for="description">คำอธิบาย:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">ราคา:</label>
                    <input type="number" id="price" step="0.01" name="price" required>
                </div>
                <div class="form-group">
                    <label for="stock">จำนวนในสต๊อก:</label>
                    <input type="number" id="stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="cover_image_url">URL รูปภาพปก:</label>
                    <input type="text" id="cover_image_url" name="cover_image_url" required>
                </div>
                <div class="form-group">
                    <label for="category_id">หมวดหมู่:</label>
                    <select id="category_id" name="category_id" required>
                        <option value=""> เลือกหมวดหมู่ </option>
                        <?php
                        $result = $conn->query("SELECT * FROM categories");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="add_book" class="btn">เพิ่มหนังสือ</button>
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
