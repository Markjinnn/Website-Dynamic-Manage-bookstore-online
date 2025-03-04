<?php
session_start();
include('config.php');
include('notify.php');

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
            <section class="book-list">
                <h2>หนังสือแนะนำให้ เช่า/จอง</h2>
                <div class="book-slider">
                    <div class="slider-container">
                        <button class="slider-nav prev"><i class="fas fa-chevron-left"></i></button>
                        <button class="slider-nav next"><i class="fas fa-chevron-right"></i></button>
                        
                        <div class="books-container">
                            <div class="books-wrapper">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM books");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($book = $result->fetch_assoc()):
                                ?>
                                <div class="book" 
                                    data-id="<?= $book['id'] ?>" 
                                    data-title="<?= htmlspecialchars($book['title']) ?>" 
                                    data-author="<?= htmlspecialchars($book['author']) ?>" 
                                    data-description="<?= htmlspecialchars($book['description']) ?>" 
                                    data-price="<?= $book['price'] ?>" 
                                    data-stock="<?= $book['stock'] ?>" 
                                    data-cover="<?= htmlspecialchars($book['cover_image']) ?>">
                                    <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                                    <h3><?= $book['title'] ?></h3>
                                    <p>จำนวนคงเหลือ: <?= $book['stock'] ?> เล่ม</p>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <?php if ($book['stock'] <= 0): ?> 
                                            <p>หนังสือหมดชั่วคราว</p>
                                        <?php else: ?>
                                            <a href="book_details.php?id=<?= $book['id'] ?>" class="rent-button">จองหนังสือตอนนี้</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p>กรุณาเข้าสู่ระบบเพื่อเช่า/จองหนังสือ</p>
                                    <?php endif; ?>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>                            

    
        </main>

        <main>
            <section class="popular-books">
                <h2>หนังสือยอดนิยม</h2>
                <div class="books">
                    <?php
                    $stmt = $conn->prepare("
                        SELECT books.title, books.cover_image, COUNT(user_books.book_id) AS rent_count, 
                            AVG(reviews.rating) AS average_rating
                        FROM user_books 
                        JOIN books ON user_books.book_id = books.id 
                        LEFT JOIN reviews ON reviews.book_id = books.id
                        GROUP BY books.id 
                        ORDER BY rent_count DESC LIMIT 5
                    ");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($book = $result->fetch_assoc()):
                        // คำนวณคะแนนเฉลี่ยที่แสดงเป็นดาว
                        $average_rating = round($book['average_rating']); 
                    ?>
                    <div class="book">
                        <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                        <p>จำนวนครั้งที่เช่า/จอง: <?= $book['rent_count'] ?> ครั้ง</p>
                        <p>
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $average_rating ? '⭐' : '';
                            }
                            ?>
                        </p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>

        <?php
            //  จำนวนสมาชิกทั้งหมด
            $stmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
            $stmt->execute();
            $stmt->bind_result($total_users);
            $stmt->fetch();
            $stmt->close();

            //  จำนวนหนังสือที่พร้อมจำหน่าย
            $stmt = $conn->prepare("SELECT SUM(stock) AS available_books FROM books");
            $stmt->execute();
            $stmt->bind_result($available_books);
            $stmt->fetch();
            $stmt->close();

            //  จำนวนหนังสือที่จำหน่ายไปแล้ว
            $stmt = $conn->prepare("SELECT COUNT(*) AS sold_books FROM user_books");
            $stmt->execute();
            $stmt->bind_result($sold_books);
            $stmt->fetch();
            $stmt->close();
        ?>

        <section class="shop-status">
            <div class="status-cards">
                <div class="status-card">
                    <i class="fas fa-users"></i>
                    <h3><?= $total_users ?> คน</h3>
                    <p>สมาชิกทั้งหมด</p>
                </div>
                <div class="status-card">
                    <i class="fas fa-truck"></i>
                    <h3><?= $available_books ?> ชิ้น</h3>
                    <p>พร้อมจำหน่าย</p>
                </div>
                <div class="status-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h3><?= $sold_books ?> ชิ้น</h3>
                    <p>จำหน่ายไปแล้ว</p>
                </div>
            </div>
        </section>

        
        <footer>
            <p>Create By Watcharapol &copy; 2024</p>
        </footer>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="js/usermenu.js"></script>
    <script src="js/slide.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.globe.min.js"></script>
    <script src="js/vanta.js"></script>
</body>
</html>
