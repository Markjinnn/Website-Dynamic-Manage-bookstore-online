<?php
session_start();
include('../config.php');
include('notifyonbackend.php');

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        header("Location: manage_user.php"); 
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการลบผู้ใช้: " . $stmt->error;
    }
}

if (isset($_POST['update_balance'])) {
    $user_id = $_POST['user_id'];
    $new_balance = $_POST['balance'];

    $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $stmt->bind_param('di', $new_balance, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_user.php"); 
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตยอดเงิน: " . $stmt->error;
    }
}

if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param('si', $new_role, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_user.php"); 
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตสิทธิ์: " . $stmt->error;
    }
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE ? OR email LIKE ?");
$search_param = "%" . $search . "%";
$stmt->bind_param('ss', $search_param, $search_param);
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
    <link rel="stylesheet" href="../css/user.css">
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
        <div class="container">
            <h1>รายการผู้ใช้</h1>

            <form class="search-form1" method="GET" action="manage_user.php">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="ค้นหาผู้ใช้" value="<?= htmlspecialchars($search) ?>">
                </div>
            </form>


            <table class="user-table">
                <thead>
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>อีเมล</th>
                        <th>สิทธิ์</th>
                        <th>ยอดเงินคงเหลือ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td>
                            <form method="POST" action="manage_user.php">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role">
                                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" class="btn" name="update_role">อัปเดตสิทธิ์</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="manage_user.php">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="number" step="0.01" name="balance" value="<?= $user['balance'] ?>">
                                <button type="submit" class="btn" name="update_balance">อัปเดตยอดเงิน</button>
                            </form>
                        </td>
                        <td>
                            <a href="manage_user.php?delete=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('คุณต้องการลบผู้ใช้คนนี้หรือไม่?');">ลบ</a>
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
