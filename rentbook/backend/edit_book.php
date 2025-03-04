<?php
    session_start();
    include('../config.php');
    include('notifyonbackend.php');

    if ($_SESSION['role'] !== 'admin') {
        header('Location: login.php');
        exit();
    }

    error_reporting(E_ERROR | E_PARSE);


    $search = "";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }


    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ?");
    $search_param = "%" . $search . "%";
    $stmt->bind_param('s', $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTBOOK ZONE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .btn-edit, .btn-delete {
            padding: 8px 16px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            font-size: 14px;
        }
        .btn-edit {
            background-color: #4CAF50;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .btn-edit:hover {
            background-color: #45a049;
        }
        .btn-delete:hover {
            background-color: #e60000;
        }
    </style>
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
            <h1>จัดการหนังสือ</h1>
            <form class="search-form1" method="GET" action="">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="ค้นหาหนังสือ" value="<?= htmlspecialchars($search) ?>">
                </div>
            </form>
            
            <table class="book-table">
                <thead>
                    <tr>
                        <th>ชื่อหนังสือ</th>
                        <th>ผู้แต่ง</th>
                        <th>ราคา</th>
                        <th>จัดการ</th>
                        <th>STOCK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($book = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $book['title'] ?></td>
                        <td><?= $book['author'] ?></td>
                        <td><?= $book['price'] ?> บาท</td>
                        <td>
                            <a href="edit_booksystem.php?id=<?= $book['id'] ?>" class="btn-edit">แก้ไข</a>
                            <a href="del_booksystem.php?id=<?= $book['id'] ?>" class="btn-delete" onclick="return confirm('คุณต้องการลบหนังสือเล่มนี้หรือไม่?');">ลบ</a>
                        </td>
                        <td>
                            <form method="POST" action="update_stock.php">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <label for="stock">จำนวนในคลัง:</label>
                                <input type="number" name="stock" id="stock" value="<?= $book['stock'] ?>" min="0">
                                <button type="submit" class="btn">อัปเดตสต๊อก</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <footer>
        <p>Create By Watcharapol &copy; 2024</p>
    </footer>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="../js/usermenu.js"></script>
</body>
</html>
