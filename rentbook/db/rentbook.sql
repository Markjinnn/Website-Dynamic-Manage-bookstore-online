-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2025 at 04:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `price`, `created_at`, `cover_image`, `stock`, `category_id`) VALUES
(30, 'house', 'das', 'dsa', 1111.00, '2024-11-13 16:22:33', 'https://amarinbooks.com/wp-content/uploads/2023/08/%E0%B8%84%E0%B8%A3%E0%B8%B1%E0%B8%AA%E0%B8%AB%E0%B8%99%E0%B8%B1%E0%B8%87%E0%B8%AA%E0%B8%B7%E0%B8%AD.png', 10, 1),
(31, 'a-level', 'thanapat', '123123', 500.00, '2024-12-27 18:20:52', 'https://th-test-11.slatic.net/p/9d6927fae372b9e7e60b55f55d2a7409.png', 4, 2),
(32, 'test', '123123', '213', 200.00, '2024-12-28 04:39:37', 'https://www.nupress.grad.nu.ac.th/shop/wp-content/uploads/2019/11/978-616-426-153-2.jpg', 6, 3),
(33, 'อาโซน', 'อาโซน', 'อาโซน', 10.00, '2024-12-28 05:10:02', 'https://connect.bu.ac.th/wp-content/uploads/2023/05/B01.png', 0, 5),
(34, 'อาเหยิน', 'อาเหยิน', 'อาเหยิน', 200.00, '2024-12-28 05:13:36', 'https://shop.sac.or.th/th/contents/products/hoverImageFile20240217104748.jpg', 0, 4),
(36, 'helloworld', 'helloworld', 'helloworld', 100.00, '2024-12-28 05:13:53', 'https://down-th.img.susercontent.com/file/sg-11134201-7rdvr-lzzijqv4ftkk1b', 10, 5),
(37, 'python for loop', 'watcharapol', 'watcharapol', 50000.00, '2024-12-28 05:28:11', 'https://www.bookcaze.com/image/cache/catalog/page1_7WlDo1654502256385uCNPS-1000x1000.jpg', 19, 1),
(38, 'c++ nopper', 'nopper', 'nopper', 500.00, '2024-12-28 05:29:43', 'https://aimphan.co.th/wp-content/uploads/2020/09/%E0%B8%9B%E0%B8%81%E0%B8%AB%E0%B8%A5%E0%B8%B1%E0%B8%81%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%80%E0%B8%82%E0%B8%B5%E0%B8%A2%E0%B8%99%E0%B9%82%E0%B8%9B%E0%B8%A3%E0%B9%81%E0%B8%81%E0%B8%A3%E0%B8%A1-scaled.jpg', 50, 4),
(39, 'backend node.js', 'arzone', 'arzone', 1000.00, '2024-12-28 05:30:20', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTskil64HkiawLzOYJQOB9yQktXgB8_T6p_MA&s', 30, 1),
(40, 'บารมีallC', 'บารมีallC', 'บารมีallC', 3000.00, '2024-12-28 05:41:47', 'https://expert-programming-tutor.com/tutorial/article/images/KG/KG003204.webp', 555, 3),
(42, 'testes', '213321', '321312', 12.00, '2025-02-01 12:33:04', '123123', 123, 7);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1, 'นิยาย'),
(2, 'วิทยาศาสตร์'),
(3, 'เทคโนโลยี'),
(4, 'การ์ตูน'),
(5, 'จิตวิทยา'),
(6, 'test'),
(7, 'estes');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `book_id`, `user_id`, `rating`, `created_at`) VALUES
(17, 30, 2, 1, '2025-01-13 22:15:56'),
(18, 37, 2, 5, '2025-01-13 22:15:57'),
(19, 32, 2, 5, '2025-02-01 21:07:02'),
(20, 37, 2, 5, '2025-02-01 21:35:34');

-- --------------------------------------------------------

--
-- Table structure for table `top_up_requests`
--

CREATE TABLE `top_up_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `slip` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `top_up_requests`
--

INSERT INTO `top_up_requests` (`id`, `user_id`, `amount`, `slip`, `status`, `request_date`) VALUES
(4, 2, 23.00, 'slip_1738423925.jpg', 'approved', '2025-02-01 15:32:05'),
(5, 2, 231321.00, 'slip_1738424112.jpg', 'approved', '2025-02-01 15:35:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `role`, `created_at`, `balance`) VALUES
(2, 'ekill2555@gmail.com', 'das', '$2y$10$8tMFu6enqs4Cp5G4Sc82XuuOEYExVo0upnxzyw4a/RDGhyP53hrQu', 'admin', '2024-10-23 07:49:24', 46.00),
(3, '2d@gmail.com', 'user', '$2y$10$vwUySZ1DpbEus37LQcsxk.JxmjwfUxSHTeysUuy5BGmIpwU1Vu5ba', 'admin', '2024-10-23 07:54:35', 1111.00),
(4, 'test@gmail.com', 'testtest', '$2y$10$KxaJr3VA4Xks9RVKq3ZzSeY6.GdNe30x71hAT24nCal4H48mKjgXW', 'admin', '2024-10-23 08:00:31', 1111.00),
(5, 'dasdsadasdasdsa@gmail.com', 'dasdsadasdasdsa', '$2y$10$3/U2CJQVmfB7hrWzH8G2rODm0uMukIpZogo2NhwdKtZZzSO/UiP4m', 'user', '2024-10-23 14:58:15', 0.00),
(6, 'dasdsadas312312dasdsa@gmail.com', 'echo \".\"; ', '$2y$10$WfJ5RJMSVLu.HvXxeZC2D.ubckq3zZ85I0hw9nxT11yi.2TwbdoKC', 'user', '2024-10-23 14:58:45', 0.00),
(8, '213123@gmail.com', 'nutt', '$2y$10$rKVbOPawC6qRtxChSQaAH.aUNPRCpfGzb7s5REu1knG2QGBqk4XZe', 'user', '2025-01-13 15:11:22', 99999499.99);

-- --------------------------------------------------------

--
-- Table structure for table `user_books`
--

CREATE TABLE `user_books` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rent_date` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `rental_period` int(11) DEFAULT NULL,
  `rental_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `reviewed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_books`
--

INSERT INTO `user_books` (`id`, `user_id`, `book_id`, `rent_date`, `expire_date`, `rental_period`, `rental_price`, `status`, `reviewed`) VALUES
(86, 2, 32, '2025-02-01 15:06:25', '2025-02-04 15:06:25', 3, 600.00, 'Approved', 1),
(91, 2, 37, '2025-02-01 15:13:04', '2025-02-02 15:13:04', 1, 50000.00, 'Approved', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `top_up_requests`
--
ALTER TABLE `top_up_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_books`
--
ALTER TABLE `user_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `top_up_requests`
--
ALTER TABLE `top_up_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_books`
--
ALTER TABLE `user_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `top_up_requests`
--
ALTER TABLE `top_up_requests`
  ADD CONSTRAINT `top_up_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_books`
--
ALTER TABLE `user_books`
  ADD CONSTRAINT `user_books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
