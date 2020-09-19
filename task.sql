-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2020 at 02:41 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `task`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `idCategory` int(11) NOT NULL,
  `nameCategory` varchar(50) NOT NULL,
  `idDepartment` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `idDepartment` int(11) NOT NULL,
  `nameDepartment` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

CREATE TABLE `manufacturers` (
  `idManufacturer` int(11) NOT NULL,
  `nameManufacturer` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `idProduct` int(11) NOT NULL,
  `model_number` varchar(20) NOT NULL,
  `upc` varchar(10) NOT NULL,
  `sku` varchar(10) NOT NULL,
  `regular_price` float(10,2) NOT NULL,
  `sale_price` float(10,2) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL,
  `idCategory` int(11) DEFAULT NULL,
  `idManufacturer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_products`
-- (See below for the actual view)
--
CREATE TABLE `view_products` (
`model_number` varchar(20)
,`idProduct` int(11)
,`upc` varchar(10)
,`sku` varchar(10)
,`regular_price` float(10,2)
,`sale_price` float(10,2)
,`description` text
,`url` varchar(200)
,`idCategory` int(11)
,`category` varchar(50)
,`idManufacturer` int(11)
,`manufacturer` varchar(50)
,`department` varchar(50)
);

-- --------------------------------------------------------

--
-- Structure for view `view_products`
--
DROP TABLE IF EXISTS `view_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_products`  AS  select `products`.`model_number` AS `model_number`,`products`.`idProduct` AS `idProduct`,`products`.`upc` AS `upc`,`products`.`sku` AS `sku`,`products`.`regular_price` AS `regular_price`,`products`.`sale_price` AS `sale_price`,`products`.`description` AS `description`,`products`.`url` AS `url`,`products`.`idCategory` AS `idCategory`,(select `categories`.`nameCategory` from `categories` where `products`.`idCategory` = `categories`.`idCategory`) AS `category`,`products`.`idManufacturer` AS `idManufacturer`,(select `manufacturers`.`nameManufacturer` from `manufacturers` where `products`.`idManufacturer` = `manufacturers`.`idManufacturer`) AS `manufacturer`,(select `departments`.`nameDepartment` from `departments` where `departments`.`idDepartment` = (select `categories`.`idDepartment` from `categories` where `categories`.`idCategory` = `products`.`idCategory`)) AS `department` from `products` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`idCategory`),
  ADD KEY `idDepartment` (`idDepartment`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`idDepartment`);

--
-- Indexes for table `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD PRIMARY KEY (`idManufacturer`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`idProduct`),
  ADD KEY `idCategory` (`idCategory`),
  ADD KEY `idManufacturer` (`idManufacturer`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `idCategory` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14726;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `idDepartment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1870;

--
-- AUTO_INCREMENT for table `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `idManufacturer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13586;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `idProduct` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2451;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`idDepartment`) REFERENCES `departments` (`idDepartment`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`idCategory`) REFERENCES `categories` (`idCategory`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`idManufacturer`) REFERENCES `manufacturers` (`idManufacturer`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
