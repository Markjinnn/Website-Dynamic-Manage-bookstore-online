<?php
session_start();
include('config.php');
include('notify.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// คืนสต๊อกอัตโนมัติสำหรับหนังสือที่ครบกำหนดเวลา
$stmt = $conn->prepare("SELECT id, book_id FROM user_books WHERE expire_date IS NOT NULL AND expire_date <= NOW() AND status = 'Approved'");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $rental_id = $row['id'];
    $book_id = $row['book_id'];

    $stmt_update = $conn->prepare("UPDATE books SET stock = stock + 1 WHERE id = ?");
    $stmt_update->bind_param('i', $book_id);
    $stmt_update->execute();
    $stmt_update->close();

    $stmt_delete = $conn->prepare("DELETE FROM user_books WHERE id = ?");
    $stmt_delete->bind_param('i', $rental_id);
    $stmt_delete->execute();
    $stmt_delete->close();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTBOOK ZONE</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>
<body>
<div id="vanta">

    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>RENTBOOK ZONE</h1>
            </div>
            <div class="menu">
                <a href="index.php" class="nav-link">หน้าหลัก</a>
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
        <section class="user-info">
            <h2>ข้อมูลของคุณ</h2>
            <p>Username: <?= $_SESSION['username'] ?></p>
            <p>Email: 
            <?php 
            $stmt = $conn->prepare("SELECT email, balance FROM users WHERE id = ?");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            echo $user['email'];
            ?>
            </p>
            <p>ยอดคงเหลือ: <?= number_format($user['balance'], 2) ?> บาท</p>
        </section>

        <h2>หนังสือที่คุณเช่า/จอง</h2>
        <?php
            // แจ้งเตือนแบบ popup หนังสือใกล้หมดก่อน1วัน
            if (isset($_SESSION['user_id'])) {
                $stmt = $conn->prepare("SELECT books.title, user_books.expire_date 
                                        FROM user_books 
                                        JOIN books ON user_books.book_id = books.id 
                                        WHERE user_books.user_id = ? 
                                        AND user_books.expire_date <= DATE_ADD(NOW(), INTERVAL 1 DAY) 
                                        AND user_books.status = 'Approved'");
                $stmt->bind_param('i', $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<button onclick="openNotification()" class="notification-btn">แจ้งเตือน !!!!!</button>';
                    
                    echo '<div id="notificationModal" class="modal">';
                    echo '<div class="modal-content">';
                    echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
                    echo '<h2>แจ้งเตือน</h2>';
                    echo '<p><strong>กรุณาตรวจสอบรายการของคุณ:</strong></p>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<p>หนังสือ: "' . htmlspecialchars($row['title']) . '" หมดอายุวันที่: ' . htmlspecialchars($row['expire_date']) . '</p>';
                    }
                    echo '</div>';
                    echo '</div>';
                }

                $stmt->close();
            }
        ?>

        <div class="books">
            <?php
            $stmt = $conn->prepare("
                SELECT user_books.id AS rental_id, 
                    books.id AS book_id, 
                    books.title, 
                    books.cover_image, 
                    user_books.rent_date, 
                    user_books.expire_date, 
                    user_books.status, 
                    user_books.reviewed 
                FROM user_books 
                JOIN books ON user_books.book_id = books.id 
                WHERE user_books.user_id = ?
            ");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($rental = $result->fetch_assoc()) {
                    echo '<div class="book" id="rental_' . $rental['rental_id'] . '">';
                    echo '<img src="' . $rental['cover_image'] . '" alt="' . $rental['title'] . '" class="book-cover">';
                    echo '<h3>' . $rental['title'] . '</h3>';

                    if ($rental['status'] === 'Pending') {
                        echo '<p>สถานะ: รอการยืนยัน</p>';
                        echo '<button onclick="cancelRental(' . $rental['rental_id'] . ')" class="btn btn-cancel">ยกเลิกการเช่า</button>';
                    } elseif ($rental['status'] === 'Approved') {
                        if ($rental['expire_date'] === NULL) {
                            echo '<p>สถานะ: เช่าตลอดชีพ</p>';
                        } else {
                            $expire_date = new DateTime($rental['expire_date']);
                            $current_time = new DateTime();
                            $interval = $current_time->diff($expire_date);

                            echo'<p>วันหมดอายุ: ' . $expire_date->format('d-m-Y') . '</p>';
                            if ($interval->days > 1) {
                                echo '<p>เหลืออีก ' . $interval->days . ' วัน</p>';
                            } else {
                                echo '<p>เหลืออีก ' . $interval->h . ' ชั่วโมง ' . $interval->i . ' นาที</p>';
                            }
                            echo '<button onclick="returnBook(' . $rental['rental_id'] . ')" class="btn">คืนหนังสือ</button>';
                        }

                        if ($rental['reviewed'] == 0) {
                            echo '<form action="submit_rating.php" method="POST" class="rating-form">';
                            echo '<input type="hidden" name="book_id" value="' . $rental['book_id'] . '">';
                            echo '<label for="rating_' . $rental['book_id'] . '">ให้คะแนน:</label>';
                            echo '<select name="rating" id="rating_' . $rental['book_id'] . '" required>';
                            echo '<option value="5">⭐⭐⭐⭐⭐</option>';
                            echo '<option value="4">⭐⭐⭐⭐</option>';
                            echo '<option value="3">⭐⭐⭐</option>';
                            echo '<option value="2">⭐⭐</option>';
                            echo '<option value="1">⭐</option>';
                            echo '</select>';
                            echo '<button class="btn" type="submit">ส่งคะแนน</button>';
                            echo '</form>';
                        } else {
                            echo '';
                        }
                    } elseif ($rental['status'] === 'Rejected') {
                        echo '<p>สถานะ: ถูกปฏิเสธ</p>';
                    }

                    echo '</div>';
                }
            } else {
                echo '<p>คุณยังไม่มีหนังสือที่เช่าในขณะนี้</p>';
            }
            ?>
        </div>
        
    </main>

    <footer>
        <p>Create By Watcharapol &copy; 2024</p>
    </footer>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/usermenu.js"></script>
    <script src="js/cancelrental.js"></script>
    <script src="js/returnBook.js"></script>
    <script>
        function openNotification() {
            document.getElementById('notificationModal').style.display = 'block';
        }

        function closeNotification() {
            document.getElementById('notificationModal').style.display = 'none';
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.globe.min.js"></script>
    <script src="js/vanta.js"></script>

</body>
</html>
