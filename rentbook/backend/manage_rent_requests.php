<?php
session_start();
include('../config.php');
include('notifyonbackend.php');


if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$message = '';

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

if (isset($_POST['action']) && isset($_POST['id'])) {
    $reservation_id = $_POST['id'];
    $action = $_POST['action'];

    $stmt = $conn->prepare("SELECT user_books.user_id, user_books.book_id, user_books.rental_price, users.balance FROM user_books 
                            JOIN users ON user_books.user_id = users.id 
                            WHERE user_books.id = ? AND user_books.status = 'Pending'");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('i', $reservation_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $book_id, $rental_price, $balance);
    
    if ($stmt->fetch()) {
        $stmt->close();
        //approve
        if ($action === 'approve') {
            if ($balance >= $rental_price) {
                $new_balance = $balance - $rental_price;
                $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $stmt->bind_param('di', $new_balance, $user_id);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("UPDATE user_books SET status = 'Approved' WHERE id = ?");
                $stmt->bind_param('i', $reservation_id);
                $stmt->execute();
                $stmt->close();

                $message = "การเช่าถูกยืนยันและหักยอดเงินเรียบร้อยแล้ว";
                $alert_type = "success";
            } else {
                $message = "ยอดเงินของผู้ใช้ไม่เพียงพอสำหรับการเช่านี้";
                $alert_type = "error";
            }
            //reject
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("DELETE FROM user_books WHERE id = ?");
            $stmt->bind_param('i', $reservation_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE books SET stock = stock + 1 WHERE id = ?");
            $stmt->bind_param('i', $book_id);
            $stmt->execute();
            $stmt->close();

            $message = "คำขอเช่าถูกปฏิเสธและคืนสต๊อกเรียบร้อยแล้ว";
            $alert_type = "success";
        }
    } else {
        $message = "ไม่พบข้อมูลคำขอเช่าหรือมีการเปลี่ยนแปลงสถานะแล้ว";
        $alert_type = "error";
    }
}

// Query ค้นหา username 
$stmt = $conn->prepare("SELECT user_books.id, users.username, books.title, user_books.rental_period, user_books.rental_price 
                        FROM user_books 
                        JOIN users ON user_books.user_id = users.id 
                        JOIN books ON user_books.book_id = books.id 
                        WHERE user_books.status = 'Pending' AND (users.username LIKE ? OR books.title LIKE ?)");
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
    <link rel="stylesheet" href="../css/manage_rent_requests.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h2>จัดการคำขอเช่าหนังสือ</h2>
            
            <form class="search-form1" method="GET" action="">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="ค้นหาผู้ใช้หรือชื่อหนังสือ" value="<?= htmlspecialchars($search) ?>">
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ผู้ใช้</th>
                        <th>ชื่อหนังสือ</th>
                        <th>ระยะเวลาการเช่า</th>
                        <th>ราคาเช่า</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['rental_period']) ?> วัน</td>
                            <td><?= number_format($row['rental_price'], 2) ?> บาท</td>
                            <td>
                                <form action="manage_rent_requests.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn-approve">ยืนยัน</button>
                                    <button type="submit" name="action" value="reject" class="btn-reject">ปฏิเสธ</button>
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

    <?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            title: '<?= ($alert_type === "success" ? "สำเร็จ!" : "เกิดข้อผิดพลาด") ?>',
            text: '<?= $message ?>',
            icon: '<?= $alert_type ?>',
            confirmButtonText: 'ตกลง'
        });
    </script>
    <?php endif; ?>
</body>
</html>
