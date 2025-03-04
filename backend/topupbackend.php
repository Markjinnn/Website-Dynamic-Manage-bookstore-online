<?php
session_start();
include('../config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT tr.id, tr.user_id, tr.amount, tr.slip, tr.status, u.username 
          FROM top_up_requests tr
          JOIN users u ON tr.user_id = u.id
          WHERE tr.status = 'pending'";

if ($search) {
    $query .= " AND (u.username LIKE ? OR tr.id LIKE ?)";
}

$query .= " ORDER BY tr.request_date DESC";

$stmt = $conn->prepare($query);

if ($search) {
    $searchTerm = "%$search%";
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET['id'], $_GET['action'])) {
    $request_id = $_GET['id'];
    $action = $_GET['action'];

    $stmt = $conn->prepare("SELECT status FROM top_up_requests WHERE id = ?");
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    if ($status === 'approved' || $status === 'rejected') {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถดำเนินการได้',
                    text: 'คำขอนี้ได้รับการดำเนินการแล้ว!',
                }).then(() => {
                    window.location.href = 'topupbackend.php';
                });
              </script>";
        exit();  
    }

    
    if ($action === 'approve') {
        $stmt = $conn->prepare("SELECT user_id, amount FROM top_up_requests WHERE id = ?");
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $stmt->bind_result($user_id, $amount);
        $stmt->fetch();
        $stmt->close();
    
        $stmt = $conn->prepare("UPDATE top_up_requests SET status = 'approved' WHERE id = ?");
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $stmt->close();
    
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param('di', $amount, $user_id);
        $stmt->execute();
        $stmt->close();
    
        header("Location: topupbackend.php"); 
        exit();
    } else if ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE top_up_requests SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $stmt->close();
    
        header("Location: topupbackend.php"); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTBOOK ZONE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/manage_rent_requests.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h2>จัดการคำขอเติมเงิน</h2>

            <form class="search-form1" method="GET" action="">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="ค้นหาผู้ใช้" value="<?= htmlspecialchars($search) ?>">
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>จำนวนเงิน</th>
                        <th>สลิป</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['username'] ?></td>
                            <td><?= number_format($row['amount'], 2) ?> บาท</td>
                            <td><img src="../uploads/slips/<?= $row['slip'] ?>" alt="Slip" width="100"></td>
                            <td>
                                <a href="topupbackend.php?id=<?= $row['id'] ?>&action=approve" 
                                class="rent-button" 
                                onclick="confirmAction('ยืนยันการเติมเงิน?', event);">ยืนยัน</a> | 
                                <a href="topupbackend.php?id=<?= $row['id'] ?>&action=reject" 
                                class="rent-button" 
                                onclick="confirmAction('คุณแน่ใจว่าจะปฏิเสธการเติมเงิน?', event);">ปฏิเสธ</a>
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

    <script>
        function confirmAction(message, event) {
            event.preventDefault();  

            const link = event.target;
            link.style.pointerEvents = 'none';  
            link.innerText = 'กำลังดำเนินการ...';  

            Swal.fire({
                title: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่',
                cancelButtonText: 'ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    window.location.href = link.href;
                } else {
                    
                    link.style.pointerEvents = 'auto';  
                    link.innerText = 'ยืนยัน';  
                }
            });
        }
    </script>

</body>
</html>
