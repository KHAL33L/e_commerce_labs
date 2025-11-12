-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 11:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbforlab`
--

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(150) NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `brand_name`, `category_id`, `user_id`, `created_at`) VALUES
(2, 'Bath and Body Works', 7, 1, '2025-10-23 17:53:38'),
(3, 'Samsung', 8, 1, '2025-10-23 17:54:07'),
(4, 'Mr. DIY', 9, 1, '2025-10-23 17:54:41'),
(5, 'Lego', 10, 1, '2025-10-23 17:54:49'),
(6, 'Monopoly', 10, 1, '2025-10-23 17:54:55'),
(7, 'Mercedes', 11, 1, '2025-10-23 17:55:05'),
(8, 'BMW', 11, 1, '2025-10-23 17:55:09'),
(10, 'Vaseline', 7, 1, '2025-10-23 17:55:23'),
(11, 'Johnson\'s', 7, 1, '2025-10-23 17:56:06'),
(12, 'Do It', 9, 1, '2025-10-23 17:56:19'),
(13, 'Baker Furniture', 12, 1, '2025-10-23 17:56:47'),
(14, 'Barclay Butera', 12, 1, '2025-10-23 17:57:11'),
(18, 'Nike', 6, 1, '2025-10-23 17:58:07'),
(19, 'Adidas', 6, 1, '2025-10-23 17:58:16'),
(20, 'Gap', 13, 1, '2025-10-23 17:58:24'),
(21, 'Corteiz', 13, 1, '2025-10-23 17:58:33'),
(23, 'LC Waikiki', 13, 1, '2025-10-23 17:58:59'),
(24, 'Gucci', 14, 1, '2025-10-23 17:59:08'),
(25, 'LV', 14, 1, '2025-10-23 17:59:12'),
(26, 'Hermes', 14, 1, '2025-10-23 17:59:19'),
(27, 'Under Armor', 13, 1, '2025-10-26 15:30:56'),
(28, 'Defacto', 13, 1, '2025-10-26 15:31:57'),
(29, 'J and S Construction', 15, 1, '2025-10-26 15:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `p_id` int(11) NOT NULL,
  `ip_add` varchar(50) NOT NULL,
  `c_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`p_id`, `ip_add`, `c_id`, `qty`) VALUES
(1, '::1', 2, 4),
(2, '::1', 2, 3),
(2, '::1', 5, 2),
(10, '::1', 1, 11),
(9, '::1', 1, 3),
(11, '::1', 1, 5),
(12, '::1', 1, 1),
(12, '::1', 2, 1),
(12, '::1', 5, 1),
(12, '::1', 3, 1),
(12, '::1', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `user_id`, `created_at`) VALUES
(6, 'Footwear', 1, '2025-10-23 17:41:34'),
(7, 'Beauty and personal care', 1, '2025-10-23 17:42:45'),
(8, 'Electronics', 1, '2025-10-23 17:43:06'),
(9, 'DIY and hardware', 1, '2025-10-23 17:43:21'),
(10, 'Toys and hobbies', 1, '2025-10-23 17:43:31'),
(11, 'Auto and parts', 1, '2025-10-23 17:43:43'),
(12, 'Furniture', 1, '2025-10-23 17:43:50'),
(13, 'Clothing', 1, '2025-10-23 17:46:23'),
(14, 'Accessories', 1, '2025-10-23 17:46:44'),
(15, 'Construction', 1, '2025-10-26 15:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_image`, `user_role`) VALUES
(1, 'Ibrahim Dasuki', 'dasukikhaleel@gmail.com', '$2y$10$Yvs764IOq/RDMKpMSudQDO0aFX2YtuAGlSJgCRCtwy1Tbni.Z8hDu', 'Ghana', 'Accra', '0555158870', NULL, 1),
(2, 'User One', 'userone@gmail.com', '$2y$10$8LQQIlI3INQkRUK/gMTQI.5FWn5feaVAqUwGv./w2ymBKkui7Ri7u', 'Canada', 'Otowa', '0555158870', NULL, 2),
(3, 'User Two', 'usertwo@gmail.com', '$2y$10$vJ9EGxsGgzEtOGeVbjut.uhlf6oPc5iUN.m7ytRSyg0xv4ZHN8krC', 'Brazil', 'Rio', '0555158870', NULL, 2),
(5, 'User Three', 'userthree@gmail.com', '$2y$10$ZrcT40K8NxOsHUiPTW5Je.uL2KCQUVWsS/GBZADWA83PDZRj/04z6', 'Nigeria', 'Kaduna', '09038926752', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `pay_id` int(11) NOT NULL,
  `amt` double NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `currency` text NOT NULL,
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `title`, `description`, `price`, `category_id`, `brand_id`, `user_id`, `image_path`, `keywords`, `created_at`) VALUES
(1, 'Hypervenom football boots', 'Super sleek and precision boots', 2300.00, 6, 18, 1, '', '', '2025-10-23 18:02:56'),
(2, 'Sambas', 'Cool looking sneakers', 1800.00, 6, 19, 1, '', '', '2025-10-23 19:26:09'),
(4, 'Diamond necklace', 'Beautiful designed', 1200322.00, 14, 24, 1, '', 'necklace, diamonds, jewelry', '2025-10-26 15:27:31'),
(5, 'test', 'test', 130999.00, 14, 26, 1, '', 'test', '2025-10-26 16:19:56'),
(6, 'Steel rods', 'construction streel rods', 120000.00, 15, 29, 1, '', 'steel, construction, metal, rod, rods', '2025-10-26 16:22:46'),
(7, 'Street Hoodie', 'nice cotton hoodie', 150.00, 13, 28, 1, '', 'hoodie, brown, cotton', '2025-10-26 16:29:27'),
(8, 'Italian Leather Couch', 'good coach', 15999.00, 12, 14, 1, '', 'coach, chair, Italian, leather', '2025-10-26 16:34:35'),
(9, 'OEM Spark Plugs', 'nice spark plugs', 12333.00, 11, 8, 1, '', 'spark plug, car, engine', '2025-10-26 16:35:48'),
(10, 'Monopoly Board Game', '', 190.00, 10, 6, 1, '', '', '2025-10-26 16:36:43'),
(11, 'Lego Gotham City Set', 'fun legos', 100.00, 10, 5, 1, '', 'fun, activity, build, Lego, batman, Gotham', '2025-10-26 16:37:48'),
(12, 'Wood Planks', 'High quality planks sawed with precision', 15000.00, 15, 29, 1, '', 'wood, planks, wood planks, construction, timber', '2025-11-12 21:16:38');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_cat` int(11) NOT NULL,
  `product_brand` int(11) NOT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_price` double NOT NULL,
  `product_desc` varchar(500) DEFAULT NULL,
  `product_image` varchar(100) DEFAULT NULL,
  `product_keywords` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_cat_brand` (`user_id`,`category_id`,`brand_name`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD KEY `p_id` (`p_id`),
  ADD KEY `c_id` (`c_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_cat` (`user_id`,`category_name`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `product_cat` (`product_cat`),
  ADD KEY `product_brand` (`product_brand`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`product_brand`) REFERENCES `brands` (`brand_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
