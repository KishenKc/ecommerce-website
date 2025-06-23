-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql308.infinityfree.com
-- Generation Time: Jun 22, 2025 at 05:47 AM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39232569_kish`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `username`, `password_hash`) VALUES
(10, 'Kishen', 'Kishenlc.kc@gmail.com', 'Kishenkc', '$2y$10$rEFvnKhD9OkE62VJNwxa8epx4x75ga8T8ZvXvGTdJB/i.DJkt7RYq'),
(11, 'kc', 'kc@gmail.com', 'Kc', '$2y$10$YNM2RxTQ5gbnZ/TDLqi6e.TJQg5ZyU22CyO9D684P2/b1MEc9Pc1O');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `CustomerID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`CustomerID`, `FullName`, `Email`, `PasswordHash`, `CreatedAt`) VALUES
(1, 'New Customer', 'Customer@gmail.com', '$2y$10$zUGFHA7wk.RUCFRVZ3aPteGf4LKD56pQQxH0n2EQzA4U1NMlHLTA2', '2025-06-19 07:12:33'),
(2, 'Kc', 'Kishen@gmail.com', '$2y$10$akdpzhOlgQ4CFkz9rPbOqeb3TuJj4fDE2hnksoBeQdP0ZQvWzKopm', '2025-06-20 22:35:40'),
(3, 'Hello', 'Hello@gmail.com', '$2y$10$pT2AvWFU17JH1Bsxp2Is6eoh3mklC1mGM4.GwHDeAIQbLAYamB6Z2', '2025-06-20 23:01:04'),
(4, 'Hi', 'Hi@gmail.com', '$2y$10$APbzOR3EQjnLd4EPZIlx5u7bh1SdkUEo1380ZI81kJwQRrjk0Zu1G', '2025-06-20 23:03:30'),
(5, 'Sam', 'Sam@gmail.com', '$2y$10$pUw.ZlmqblQ5zBMREhrtx.7wW/NY3NAByT8XZDoT6YLZdlvofe/.G', '2025-06-21 05:58:38'),
(6, 'Luvano', 'luvanozaal123@gmail.com', '$2y$10$WXzLeOO5GTthwd41l94ImesIMEROpWAPbJlUu3H8Q/VoJpywi7Tdq', '2025-06-21 12:54:25'),
(7, 'Mal', 'mk@gmail.com', '$2y$10$2F6YG7gDtEUq8l/kLw1Z3.s.WZn5./9nJOkaGycmAIJ9ink1QX1FK', '2025-06-22 09:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `SupplierID` int(11) DEFAULT NULL,
  `FullName` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Phone` varchar(50) DEFAULT NULL,
  `AddressLine1` varchar(255) DEFAULT NULL,
  `AddressLine2` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `PostalCode` varchar(20) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `ShippingMethod` varchar(50) DEFAULT NULL,
  `ShippingCost` decimal(10,2) DEFAULT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `Total` decimal(10,2) DEFAULT NULL,
  `OrderDate` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `SupplierID`, `FullName`, `Email`, `Phone`, `AddressLine1`, `AddressLine2`, `City`, `PostalCode`, `Country`, `ShippingMethod`, `ShippingCost`, `Subtotal`, `Total`, `OrderDate`) VALUES
(6, NULL, 'Kishen', 'Kishen@gmail.com', '0790808845', '57 Sirkon avenue', 'Sandton', 'Johannesburg', '1777', 'South Africa', '0', '10.00', '16000.00', '16010.00', '2025-06-19 01:52:01'),
(7, NULL, 'Kishen Ch', 'Kishenlc.kc@gmail.com', '0790808845', '123 fishers hill, Long street', '', 'Johannesburg', '1619', 'South Africa', '0', '10.00', '48000.00', '48010.00', '2025-06-20 14:03:52'),
(4, NULL, 'Kishen', 'Kishen@gmail.com', '0790808845', '57 Sirkon avenue', 'Sandton', 'Johannesburg', '1777', 'South Africa', '0', '30.00', '2600.00', '2630.00', '2025-06-14 23:30:41'),
(8, NULL, 'Sammy', 'Sam@gmail.com', '0784567789', '123 fishers hill, Long street', '', 'Johannesburg', '1619', 'South Africa', '0', '10.00', '16850.00', '16860.00', '2025-06-20 22:59:45'),
(9, NULL, 'Kishen Ch', 'Kishenlc.kc12@gmail.com', '0790808845', '123 fishers hill, Long street', '', 'Johannesburg', '1619', 'South Africa', '0', '10.00', '33500.00', '33510.00', '2025-06-21 05:40:33'),
(10, NULL, 'Luvano', 'luvanozaal123@gmail.com', '0823210575', '987 Jaarsveld', '', 'Edenvale', '16001', 'South Africa', '0', '30.00', '3000.00', '3030.00', '2025-06-21 05:55:45'),
(11, NULL, 'Mal', 'mk@gmail.com', '0724534852', '24 sleepy lane Germiston', '', 'Johannesburg', '1401', 'South Africa', '0', '30.00', '16499.00', '16529.00', '2025-06-22 02:22:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`OrderItemID`, `OrderID`, `ProductID`, `Quantity`, `Price`) VALUES
(1, 1, 3, 1, '11.00'),
(2, 2, 3, 1, '11.00'),
(3, 2, 1, 4, '333.00'),
(4, 3, 3, 8, '11.00'),
(5, 4, 5, 1, '950.00'),
(6, 4, 6, 1, '1200.00'),
(7, 4, 4, 1, '450.00'),
(8, 5, 8, 4, '2.00'),
(9, 6, 12, 1, '16000.00'),
(10, 7, 12, 3, '16000.00'),
(11, 8, 13, 1, '850.00'),
(12, 8, 12, 1, '16000.00'),
(13, 9, 12, 2, '16000.00'),
(14, 9, 9, 1, '1500.00'),
(15, 10, 9, 2, '1500.00'),
(16, 11, 12, 1, '16000.00'),
(17, 11, 21, 1, '499.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `SupplierID` int(11) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ImageURL` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `SupplierID`, `Name`, `Description`, `Price`, `ImageURL`, `CreatedAt`) VALUES
(24, 9, 'Tool Box', 'Blue 39-Piece Tool Set General Household Hand Tool Kit with Storage Case', '599.00', 'https://i5.walmartimages.com/seo/Cartman-Blue-39Piece-Tool-Set-General-Household-Hand-Tool-Kit-with-Storage-Case_9371eec0-a58e-40d6-8a01-da5e42674d65.8b21a448bed5cc140b11817bc09238d4.jpeg?odnHeight=640&odnWidth=640&odnBg=FFFFFF', '2025-06-22 08:13:24'),
(18, 10, 'Body products', '', '234.00', 'https://m.media-amazon.com/images/I/71EHDomQGeL._AC_SX679_.jpg', '2025-06-21 12:49:04'),
(20, 9, 'Shoes', 'Air Force 1', '999.00', 'https://footwearnews.com/wp-content/uploads/2024/10/nike-air-force-1-HF2893_100_E_PREM-edited.jpg', '2025-06-22 07:38:58'),
(21, 9, 'Shoes', 'Sandals', '499.00', 'https://www.aldoshoes.in/on/demandware.static/-/Sites-aldo_master_catalog/default/dw8c6cd198/large/alamassi115022_1.jpg', '2025-06-22 07:53:36'),
(23, 9, 'Shoes', 'Casual Shoes', '399.00', 'https://thursdayboots.com/cdn/shop/files/1024x1024-Men-Premier-HighTop-Black-101123-3.jpg?v=1697472158&width=1024', '2025-06-22 08:09:11'),
(19, 9, 'Shoes', 'Basic Shoes', '130.00', 'https://ae-pic-a1.aliexpress-media.com/kf/S8d7b6b84e3954afcb3d64f09a9e87c85o.jpg_960x960q75.jpg_.avif', '2025-06-22 07:28:31'),
(9, 6, 'Timberlands', 'Inspired by our original 6-inch waterproof boot, this all-season style gives you tireless waterproof performance and an instantly recognizable work boot style. Rugged and great-looking, these classic men\'s boots feature PrimaLoft ECO insulation and nubuck Better Leather sourced from a sustainable tannery and the comfy EVA footbed offers cushioning and shock absorption.', '1500.00', 'https://th.bing.com/th/id/OIP.fP7HQLYRzxvRxY8VNJBUuQHaFP?r=0&rs=1&pid=ImgDetMain', '2025-06-19 07:57:57'),
(12, 6, 'Rolex', 'The Cosmograph Daytona is presented with a turquoise blue lacquer dial with bright black counters. The intensity of the colours, the exquisite 18 kt yellow gold case, and the technical sophistication of the Oysterflex bracelet and the black Cerachrom bezel with tachymetric scale combine to create contrasts of astonishing originality.', '16000.00', 'https://media.rolex.com/image/upload/q_auto/f_auto/c_limit,w_1920/v1741607447/rolexcom/new-watches/2025/watches/new-dials/roller/new-watches-2025-new-dials-cosmograph-daytona-roller_m126518ln-0014_2501stojan_001', '2025-06-19 08:28:59'),
(13, 6, 'Tools', 'Offices, mobile businesses and even people who work from home may have to complete basic maintenance or repairs. To make sure you have the right tools on hand in case you have to hang a whiteboard, assemble a desk or handle other common business projects, collect this essential list of hand tools.', '850.00', 'https://th.bing.com/th/id/OIP.RVloreANKsonObhxWGQgqAHaE8?r=0&rs=1&pid=ImgDetMain', '2025-06-19 08:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `SupplierID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `CompanyName` varchar(100) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`SupplierID`, `FullName`, `Email`, `PasswordHash`, `PhoneNumber`, `CompanyName`, `CreatedAt`) VALUES
(6, '1', 'Supplier@gmail.com', '$2y$10$a9KaFattrM8Mkvgy3K3Y0OfnB6rWjwME5lexJ8WNdOPeVLydz9ZDG', '0790808845', 'Nike', '2025-06-15 07:03:26'),
(5, 'Kishen', 'Kishen@gmail.com', '$2y$10$DUrioReDWWfjCobBppPvn.WoTbiiHqF1QpzrSkRcq1guMsWwjowl6', '0790808845', 'Nike', '2025-06-15 06:14:39'),
(8, 'Kishen', 'Kishenlc.kc@gmail.com', '$2y$10$Ug1paHll6iXp3Rj7JiHMyOUobpfp/YkObZJJLqnuOo2fHrOQP8TDm', '0790808845', NULL, '2025-06-20 23:22:44'),
(9, 'Kishen', 'Kc@gmail.com', '$2y$10$cgtRNPG2AmkdGZezL/VFqexOwS0V/bWgpmqcPOeEtUruhQrF0YABy', '0790808845', NULL, '2025-06-21 06:49:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','supplier') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `SupplierID` (`SupplierID`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`SupplierID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `SupplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
