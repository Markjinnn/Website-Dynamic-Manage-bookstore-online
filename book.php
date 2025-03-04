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
                <a href="index.php" class="nav-link">หน้าหลัก</a>
                <a href="book.php" class="nav-link">หนังสือ</a>
                <a href="top_up.php" class="nav-link">เติมเงิน</a>
            </div>
            <div class="user-menu">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i> <i class="fas fa-caret-down"></i>
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
                <h2>หนังสือให้ เช่า/จอง</h2>
                <aside class="sidebar" id="sidebar">
                    <div class="sidebar-header">
                        <h3>หมวดหมู่หนังสือ</h3>
                        <button class="sidebar-toggle" id="sidebarToggle">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                    <ul class="sidebar-content">
                        <?php
                        $currentCategory = isset($_GET['category']) ? $_GET['category'] : '';

                        $activeClass = ($currentCategory == '') ? 'active' : '';
                        echo '<li><a href="book.php" class="'.$activeClass.'">ทั้งหมด</a></li>';

                        $stmt = $conn->query("SELECT * FROM categories");
                        while ($category = $stmt->fetch_assoc()):
                            $isActive = ($currentCategory == $category['id']) ? 'active' : '';
                        ?>
                            <li>
                                <a href="book.php?category=<?= $category['id'] ?>" class="<?= $isActive ?>">
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </aside>     
                            
                <form class="search-form" method="GET" action="book.php">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search_query" placeholder="ค้นหาหนังสือ, ผู้แต่ง, สำนักพิมพ์" required>
                    </div>
                    <input type="hidden" name="category" value="<?= isset($_GET['category']) ? $_GET['category'] : '' ?>">
                </form>
       

                <div class="books">
                    <?php
                    $search_query = isset($_GET['search_query']) ? '%' . $_GET['search_query'] . '%' : '%';
                    $category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

                    if ($category_id > 0) {
                        $stmt = $conn->prepare("SELECT * FROM books WHERE (title LIKE ? OR author LIKE ? OR description LIKE ?) AND category_id = ?");
                        $stmt->bind_param('sssi', $search_query, $search_query, $search_query, $category_id);
                    } else {
                        $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR description LIKE ?");
                        $stmt->bind_param('sss', $search_query, $search_query, $search_query);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0):
                        while ($book = $result->fetch_assoc()):
                            $book_id = $book['id'];
                            $stmt2 = $conn->prepare("SELECT * FROM user_books WHERE user_id = ? AND book_id = ?");
                            $stmt2->bind_param('ii', $_SESSION['user_id'], $book_id);
                            $stmt2->execute();
                            $stmt2->store_result();
                            $is_rented = $stmt2->num_rows > 0;
                            $stmt2->close();
                    ?>
                    <div class="book">
                        <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                        <h3><?= $book['title'] ?></h3>
                        <p>ผู้แต่ง: <?= $book['author'] ?></p>
                        <p>จำนวนคงเหลือ: <?= $book['stock'] ?> เล่ม</p> 

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($book['stock'] <= 0): ?> 
                                <p>หนังสือหมดชั่วคราว</p>
                            <?php else: ?>
                                <a href="book_details.php?id=<?= $book['id'] ?>" class="rent-button">จองหนังสือ</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>กรุณาเข้าสู่ระบบเพื่อเช่า/จองหนังสือ</p>
                        <?php endif; ?>
                    </div>
                    <?php
                        endwhile;
                    else:
                        echo "<p>ไม่พบหนังสือตามคำค้นหา</p>";
                    endif;
                    ?>
                </div>
            </section>
        </div>
    </main>
    <footer>
        <p>Create By Watcharapol &copy; 2024</p>
    </footer>
    </div>  
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="js/usermenu.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.globe.min.js"></script>
    <script src="js/vanta.js"></script>
    <script src="js/sidebar.js"></script>

</body>
</html>
