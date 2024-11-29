-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 07:24 PM
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
-- Database: `agriculture_product_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `agri_officer`
--

CREATE TABLE `agri_officer` (
  `agri_officer_employee_id` int(11) NOT NULL,
  `product_scanned` tinyint(1) DEFAULT NULL,
  `check_storage_condition` tinyint(1) DEFAULT NULL,
  `data_received` tinyint(1) DEFAULT NULL,
  `selling_price_per_kg` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumer_phone_number`
--

CREATE TABLE `consumer_phone_number` (
  `consumer_id` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumer_product`
--

CREATE TABLE `consumer_product` (
  `product_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `f_name` varchar(50) DEFAULT NULL,
  `l_name` varchar(50) DEFAULT NULL,
  `consumer_demand` int(11) DEFAULT NULL,
  `purchase_history` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `f_name`, `l_name`, `consumer_demand`, `purchase_history`) VALUES
(1, 'soumic', 'shahriar', 5, 'gg'),
(2, 's', 'm', 4, 'ff'),
(3, 'hh', 'aa', 9, 'ff');

-- --------------------------------------------------------

--
-- Table structure for table `customer_purchase_history`
--

CREATE TABLE `customer_purchase_history` (
  `id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_purchase_history`
--

INSERT INTO `customer_purchase_history` (`id`, `consumer_id`, `product_id`, `product_name`, `quantity`, `price`, `total_price`, `purchase_date`) VALUES
(12, 1, 1, '', 1, 125.00, 125.00, '2024-11-29'),
(13, 1, 2, '', 1, 110.00, 110.00, '2024-11-29'),
(14, 1, 1, 'Rice', 1, 125.00, 125.00, '2024-11-29'),
(15, 1, 1, 'Rice', 1, 125.00, 125.00, '2024-11-29'),
(16, 1, 1, 'Rice', 1, 125.00, 125.00, '2024-11-29'),
(17, 1, 1, 'Rice', 2, 125.00, 250.00, '2024-11-29'),
(18, 1, 1, 'Rice', 1, 125.00, 125.00, '2024-11-29'),
(19, 1, 1, 'Rice', 2, 125.00, 250.00, '2024-11-29'),
(20, 1, 1, 'Rice', 1, 125.00, 125.00, '2024-11-29'),
(21, 1, 1, 'Rice', 1, 125.00, 125.00, '2024-11-29'),
(22, 1, 2, 'Wheat', 1, 110.00, 110.00, '2024-11-29'),
(23, 1, 2, 'Wheat', 82, 110.00, 9020.00, '2024-11-29');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `road_no` varchar(50) DEFAULT NULL,
  `house_no` varchar(50) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `employee_name`, `phone`, `email`, `road_no`, `house_no`, `hire_date`, `role`, `office_id`) VALUES
(1, 'soumic', '01704442185', 'soumicshahriar1@gmail.com', 'Block-c, road-5, Bashundhara Residential Area', '51', '2024-11-27', 'Market Manager', 1001),
(2, 'shahriar', '01779552185', 'shahriar1@gmail.com', 'Block-c, road-5, Bashundhara Residential Area', '7', '2024-11-27', 'Agriculture Officer', 1002),
(3, 'mizu', '01763086924', 'mizu@gmail.com', 'Block-c, road-2, Bashundhara Residential Area', '37', '2024-11-27', 'Food Quality Officer', 1001);

-- --------------------------------------------------------

--
-- Table structure for table `farmer`
--

CREATE TABLE `farmer` (
  `farmer_employee_id` int(11) NOT NULL,
  `wheat` tinyint(1) DEFAULT NULL,
  `planting_date` date DEFAULT NULL,
  `harvest_date` date DEFAULT NULL,
  `price_per_kg` decimal(10,2) DEFAULT NULL,
  `tomatoes` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farmer_agri_officer_supplies`
--

CREATE TABLE `farmer_agri_officer_supplies` (
  `farmer_employee_id` int(11) NOT NULL,
  `agri_officer_employee_id` int(11) NOT NULL,
  `seed` text DEFAULT NULL,
  `produced_crops` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_quality_officer`
--

CREATE TABLE `food_quality_officer` (
  `f_q_o_employee_id` int(11) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `inspection_report` text DEFAULT NULL,
  `inspection_product_info` text DEFAULT NULL,
  `check_crops_quality` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fqo_nutrition`
--

CREATE TABLE `fqo_nutrition` (
  `warehouse_manager_employee_id` int(11) NOT NULL,
  `f_q_o_employee_id` int(11) NOT NULL,
  `nutrition_level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `government_office`
--

CREATE TABLE `government_office` (
  `office_id` int(11) NOT NULL,
  `road_no` varchar(50) DEFAULT NULL,
  `house_no` varchar(50) DEFAULT NULL,
  `storage_capacity` int(11) DEFAULT NULL,
  `current_stock_level` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `government_office`
--

INSERT INTO `government_office` (`office_id`, `road_no`, `house_no`, `storage_capacity`, `current_stock_level`) VALUES
(1001, 'Block-c, road-2, Bashundhara Residential Area', '61', NULL, NULL),
(1002, 'Block-F, road-7, Bashundhara Residential Area', '37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `market_manager`
--

CREATE TABLE `market_manager` (
  `market_manager_employee_id` int(11) NOT NULL,
  `product_demand_analysis` tinyint(1) DEFAULT NULL,
  `make_analytic_chart` tinyint(1) DEFAULT NULL,
  `analyses_region_for_harvesting` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `market_manager_consumer`
--

CREATE TABLE `market_manager_consumer` (
  `market_manager_employee_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_level`
--

CREATE TABLE `nutrition_level` (
  `nutrition_level_id` int(11) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein` decimal(5,2) DEFAULT NULL,
  `fat` decimal(5,2) DEFAULT NULL,
  `carbohydrates` decimal(5,2) DEFAULT NULL,
  `fiber` decimal(5,2) DEFAULT NULL,
  `sugar` decimal(5,2) DEFAULT NULL,
  `vitamins` text DEFAULT NULL,
  `minerals` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `seasonality` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `category`, `seasonality`) VALUES
(1, 'Rice', 'Cereal/Grain', 'Moonsoon season (june to september), Winter(Octobe'),
(2, 'Wheat', 'Cereal / Grain', 'Winter season (November to March)'),
(3, 'Maize (Corn)', 'Cereal / Grain', 'Summer (March to June)'),
(4, 'Barley', 'Cereal / Grain', 'Winter season (November to March)'),
(5, 'Sugarcane', 'Cash Crop', 'Tropical regions, harvested throughout the year wi'),
(6, 'Cotton', 'Fiber Crop', 'Hot climates, harvested from October to March'),
(7, 'Soybean', 'Legume', 'Summer season (June to October)'),
(8, 'Peas', 'Legume', 'Winter (November to February)'),
(9, 'Tomato', 'Vegetable', 'Summer (March to June), Winter (October to Decembe'),
(10, 'Potato', 'Root Vegetable', 'Year-round, but major harvest in winter (October t'),
(11, 'Carrot', 'Root Vegetable', 'Winter and early spring (October to March)'),
(12, 'Onion', 'Bulb Vegetable', 'Winter (November to March)'),
(13, 'Spinach', 'Leafy Vegetable', 'Winter (October to March)'),
(14, 'Cabbage', 'Leafy Vegetable', 'Winter (October to March)'),
(15, 'Cauliflower', 'Cruciferous Vegetable', 'Winter (October to February)'),
(16, 'Apple', 'Fruit', 'Fall season (August to November)'),
(17, 'Orange', 'Fruit', 'Winter season (November to March)'),
(18, 'Mango', 'Fruit', 'Summer (April to June)'),
(19, 'Banana', 'Fruit', 'Tropical, year-round'),
(20, 'Grapes', 'Fruit', 'Summer (March to May)'),
(21, 'Pineapple', 'Tropical Fruit', 'Year-round, with peak in March to July'),
(22, 'Papaya', 'Tropical Fruit', 'Year-round'),
(23, 'Chili', 'Spice', 'Summer and rainy season (June to September)'),
(24, 'Garlic', 'Spice', 'Winter (October to March)'),
(25, 'Ginger', 'Spice', 'Monsoon season (June to August)'),
(26, 'Turmeric', 'Spice', 'Monsoon season (June to August)'),
(27, 'Coffee', 'Beverage Crop', 'Harvest season (October to March)'),
(28, 'Tea', 'Beverage Crop', 'Year-round, with peak in summer (April to August)'),
(29, 'Cocoa', 'Beverage Crop', 'Year-round, with peak in wet season (October to Ma'),
(30, 'Coconut', 'Tree Crop', 'Year-round');

-- --------------------------------------------------------

--
-- Table structure for table `product_farmer_agri_officer`
--

CREATE TABLE `product_farmer_agri_officer` (
  `product_id` int(11) NOT NULL,
  `farmer_employee_id` int(11) NOT NULL,
  `agri_officer_employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_info`
--

CREATE TABLE `product_info` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `production_cost` decimal(10,2) NOT NULL,
  `production_date` date NOT NULL,
  `expiration_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_info`
--

INSERT INTO `product_info` (`id`, `product_id`, `quantity`, `new_price`, `old_price`, `production_cost`, `production_date`, `expiration_date`) VALUES
(24, 1, 261, 125.00, 115.00, 100.00, '2024-11-30', '2026-11-28'),
(25, 2, 0, 110.00, 110.00, 85.00, '2024-11-25', '2024-12-07'),
(30, 3, 71, 100.00, NULL, 95.00, '2024-11-28', '2024-12-04'),
(32, 4, -1, 150.00, 120.00, 100.00, '2024-12-02', '2024-12-07'),
(33, 6, 0, 130.00, 130.00, 120.00, '2024-11-28', '2024-12-02');

-- --------------------------------------------------------

--
-- Table structure for table `product_info_all`
--

CREATE TABLE `product_info_all` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `production_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_info_all`
--

INSERT INTO `product_info_all` (`id`, `product_id`, `new_price`, `old_price`, `production_date`) VALUES
(5, 3, 100.00, NULL, '2024-11-28'),
(6, 8, 110.00, NULL, '2024-11-28'),
(7, 4, 120.00, 110.00, '0000-00-00'),
(8, 4, 150.00, 120.00, '2024-12-02'),
(9, 2, 110.00, 110.00, '2024-11-25'),
(10, 6, 130.00, NULL, '2024-11-28'),
(11, 6, 130.00, NULL, '2024-11-28'),
(12, 6, 130.00, NULL, '2024-11-28'),
(13, 7, 130.00, 110.00, '2024-12-04'),
(14, 6, 130.00, 130.00, '2024-11-28'),
(15, 6, 130.00, 130.00, '2024-11-28');

-- --------------------------------------------------------

--
-- Table structure for table `product_market_manager_agri_officer`
--

CREATE TABLE `product_market_manager_agri_officer` (
  `product_id` int(11) NOT NULL,
  `market_manager_employee_id` int(11) NOT NULL,
  `agri_officer_employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_region`
--

CREATE TABLE `product_region` (
  `product_id` int(11) NOT NULL,
  `region_of_production` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_warehouse`
--

CREATE TABLE `product_warehouse` (
  `warehouse_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_warehouse_manager_agri_officer`
--

CREATE TABLE `product_warehouse_manager_agri_officer` (
  `product_id` int(11) NOT NULL,
  `warehouse_manager_employee_id` int(11) NOT NULL,
  `agri_officer_employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

CREATE TABLE `warehouse` (
  `warehouse_id` int(11) NOT NULL,
  `stored_crops` text DEFAULT NULL,
  `total_quantity` int(11) DEFAULT NULL,
  `quantity_available` int(11) DEFAULT NULL,
  `storage_location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_manager`
--

CREATE TABLE `warehouse_manager` (
  `warehouse_manager_employee_id` int(11) NOT NULL,
  `check_quality` tinyint(1) DEFAULT NULL,
  `add_barcode_tag` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agri_officer`
--
ALTER TABLE `agri_officer`
  ADD PRIMARY KEY (`agri_officer_employee_id`);

--
-- Indexes for table `consumer_phone_number`
--
ALTER TABLE `consumer_phone_number`
  ADD PRIMARY KEY (`consumer_id`,`phone_number`);

--
-- Indexes for table `consumer_product`
--
ALTER TABLE `consumer_product`
  ADD PRIMARY KEY (`product_id`,`consumer_id`),
  ADD KEY `consumer_id` (`consumer_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_purchase_history`
--
ALTER TABLE `customer_purchase_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consumer_id` (`consumer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `office_id` (`office_id`);

--
-- Indexes for table `farmer`
--
ALTER TABLE `farmer`
  ADD PRIMARY KEY (`farmer_employee_id`);

--
-- Indexes for table `farmer_agri_officer_supplies`
--
ALTER TABLE `farmer_agri_officer_supplies`
  ADD PRIMARY KEY (`farmer_employee_id`,`agri_officer_employee_id`),
  ADD KEY `agri_officer_employee_id` (`agri_officer_employee_id`);

--
-- Indexes for table `food_quality_officer`
--
ALTER TABLE `food_quality_officer`
  ADD PRIMARY KEY (`f_q_o_employee_id`);

--
-- Indexes for table `fqo_nutrition`
--
ALTER TABLE `fqo_nutrition`
  ADD PRIMARY KEY (`warehouse_manager_employee_id`,`f_q_o_employee_id`,`nutrition_level_id`),
  ADD KEY `f_q_o_employee_id` (`f_q_o_employee_id`),
  ADD KEY `nutrition_level_id` (`nutrition_level_id`);

--
-- Indexes for table `government_office`
--
ALTER TABLE `government_office`
  ADD PRIMARY KEY (`office_id`);

--
-- Indexes for table `market_manager`
--
ALTER TABLE `market_manager`
  ADD PRIMARY KEY (`market_manager_employee_id`);

--
-- Indexes for table `market_manager_consumer`
--
ALTER TABLE `market_manager_consumer`
  ADD PRIMARY KEY (`market_manager_employee_id`,`consumer_id`),
  ADD KEY `consumer_id` (`consumer_id`);

--
-- Indexes for table `nutrition_level`
--
ALTER TABLE `nutrition_level`
  ADD PRIMARY KEY (`nutrition_level_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_farmer_agri_officer`
--
ALTER TABLE `product_farmer_agri_officer`
  ADD PRIMARY KEY (`product_id`,`farmer_employee_id`,`agri_officer_employee_id`),
  ADD KEY `farmer_employee_id` (`farmer_employee_id`),
  ADD KEY `agri_officer_employee_id` (`agri_officer_employee_id`);

--
-- Indexes for table `product_info`
--
ALTER TABLE `product_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_info_all`
--
ALTER TABLE `product_info_all`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_market_manager_agri_officer`
--
ALTER TABLE `product_market_manager_agri_officer`
  ADD PRIMARY KEY (`product_id`,`market_manager_employee_id`,`agri_officer_employee_id`),
  ADD KEY `market_manager_employee_id` (`market_manager_employee_id`),
  ADD KEY `agri_officer_employee_id` (`agri_officer_employee_id`);

--
-- Indexes for table `product_region`
--
ALTER TABLE `product_region`
  ADD PRIMARY KEY (`product_id`,`region_of_production`);

--
-- Indexes for table `product_warehouse`
--
ALTER TABLE `product_warehouse`
  ADD PRIMARY KEY (`warehouse_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_warehouse_manager_agri_officer`
--
ALTER TABLE `product_warehouse_manager_agri_officer`
  ADD PRIMARY KEY (`product_id`,`warehouse_manager_employee_id`,`agri_officer_employee_id`),
  ADD KEY `warehouse_manager_employee_id` (`warehouse_manager_employee_id`),
  ADD KEY `agri_officer_employee_id` (`agri_officer_employee_id`);

--
-- Indexes for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`warehouse_id`);

--
-- Indexes for table `warehouse_manager`
--
ALTER TABLE `warehouse_manager`
  ADD PRIMARY KEY (`warehouse_manager_employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_purchase_history`
--
ALTER TABLE `customer_purchase_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `product_info`
--
ALTER TABLE `product_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `product_info_all`
--
ALTER TABLE `product_info_all`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agri_officer`
--
ALTER TABLE `agri_officer`
  ADD CONSTRAINT `agri_officer_ibfk_1` FOREIGN KEY (`agri_officer_employee_id`) REFERENCES `employee` (`employee_id`);

--
-- Constraints for table `consumer_phone_number`
--
ALTER TABLE `consumer_phone_number`
  ADD CONSTRAINT `consumer_phone_number_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `consumer_product`
--
ALTER TABLE `consumer_product`
  ADD CONSTRAINT `consumer_product_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `consumer_product_ibfk_2` FOREIGN KEY (`consumer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `customer_purchase_history`
--
ALTER TABLE `customer_purchase_history`
  ADD CONSTRAINT `customer_purchase_history_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `customer_purchase_history_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `government_office` (`office_id`);

--
-- Constraints for table `farmer`
--
ALTER TABLE `farmer`
  ADD CONSTRAINT `farmer_ibfk_1` FOREIGN KEY (`farmer_employee_id`) REFERENCES `employee` (`employee_id`);

--
-- Constraints for table `farmer_agri_officer_supplies`
--
ALTER TABLE `farmer_agri_officer_supplies`
  ADD CONSTRAINT `farmer_agri_officer_supplies_ibfk_1` FOREIGN KEY (`farmer_employee_id`) REFERENCES `farmer` (`farmer_employee_id`),
  ADD CONSTRAINT `farmer_agri_officer_supplies_ibfk_2` FOREIGN KEY (`agri_officer_employee_id`) REFERENCES `agri_officer` (`agri_officer_employee_id`);

--
-- Constraints for table `food_quality_officer`
--
ALTER TABLE `food_quality_officer`
  ADD CONSTRAINT `food_quality_officer_ibfk_1` FOREIGN KEY (`f_q_o_employee_id`) REFERENCES `employee` (`employee_id`);

--
-- Constraints for table `fqo_nutrition`
--
ALTER TABLE `fqo_nutrition`
  ADD CONSTRAINT `fqo_nutrition_ibfk_1` FOREIGN KEY (`warehouse_manager_employee_id`) REFERENCES `warehouse_manager` (`warehouse_manager_employee_id`),
  ADD CONSTRAINT `fqo_nutrition_ibfk_2` FOREIGN KEY (`f_q_o_employee_id`) REFERENCES `food_quality_officer` (`f_q_o_employee_id`),
  ADD CONSTRAINT `fqo_nutrition_ibfk_3` FOREIGN KEY (`nutrition_level_id`) REFERENCES `nutrition_level` (`nutrition_level_id`);

--
-- Constraints for table `market_manager`
--
ALTER TABLE `market_manager`
  ADD CONSTRAINT `market_manager_ibfk_1` FOREIGN KEY (`market_manager_employee_id`) REFERENCES `employee` (`employee_id`);

--
-- Constraints for table `market_manager_consumer`
--
ALTER TABLE `market_manager_consumer`
  ADD CONSTRAINT `market_manager_consumer_ibfk_1` FOREIGN KEY (`market_manager_employee_id`) REFERENCES `market_manager` (`market_manager_employee_id`),
  ADD CONSTRAINT `market_manager_consumer_ibfk_2` FOREIGN KEY (`consumer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `product_farmer_agri_officer`
--
ALTER TABLE `product_farmer_agri_officer`
  ADD CONSTRAINT `product_farmer_agri_officer_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_farmer_agri_officer_ibfk_2` FOREIGN KEY (`farmer_employee_id`) REFERENCES `farmer` (`farmer_employee_id`),
  ADD CONSTRAINT `product_farmer_agri_officer_ibfk_3` FOREIGN KEY (`agri_officer_employee_id`) REFERENCES `agri_officer` (`agri_officer_employee_id`);

--
-- Constraints for table `product_info`
--
ALTER TABLE `product_info`
  ADD CONSTRAINT `product_info_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product_info_all`
--
ALTER TABLE `product_info_all`
  ADD CONSTRAINT `product_info_all_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product_market_manager_agri_officer`
--
ALTER TABLE `product_market_manager_agri_officer`
  ADD CONSTRAINT `product_market_manager_agri_officer_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_market_manager_agri_officer_ibfk_2` FOREIGN KEY (`market_manager_employee_id`) REFERENCES `market_manager` (`market_manager_employee_id`),
  ADD CONSTRAINT `product_market_manager_agri_officer_ibfk_3` FOREIGN KEY (`agri_officer_employee_id`) REFERENCES `agri_officer` (`agri_officer_employee_id`);

--
-- Constraints for table `product_region`
--
ALTER TABLE `product_region`
  ADD CONSTRAINT `product_region_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product_warehouse`
--
ALTER TABLE `product_warehouse`
  ADD CONSTRAINT `product_warehouse_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`warehouse_id`),
  ADD CONSTRAINT `product_warehouse_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product_warehouse_manager_agri_officer`
--
ALTER TABLE `product_warehouse_manager_agri_officer`
  ADD CONSTRAINT `product_warehouse_manager_agri_officer_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_warehouse_manager_agri_officer_ibfk_2` FOREIGN KEY (`warehouse_manager_employee_id`) REFERENCES `warehouse_manager` (`warehouse_manager_employee_id`),
  ADD CONSTRAINT `product_warehouse_manager_agri_officer_ibfk_3` FOREIGN KEY (`agri_officer_employee_id`) REFERENCES `agri_officer` (`agri_officer_employee_id`);

--
-- Constraints for table `warehouse_manager`
--
ALTER TABLE `warehouse_manager`
  ADD CONSTRAINT `warehouse_manager_ibfk_1` FOREIGN KEY (`warehouse_manager_employee_id`) REFERENCES `employee` (`employee_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
