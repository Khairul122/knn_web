-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 14, 2025 at 03:57 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_knn`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_pemeliharaan`
--

CREATE TABLE `data_pemeliharaan` (
  `id_data_pemeliharaan` int NOT NULL,
  `nama_penyulang` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `kategori` enum('gardu','sutm') COLLATE utf8mb3_swedish_ci NOT NULL,
  `pengukuran` int DEFAULT '0',
  `arrester` int DEFAULT '0',
  `grounding` int DEFAULT '0',
  `ultrasonic` int DEFAULT '0',
  `cover_isolasi` int DEFAULT '0',
  `pangkas` float DEFAULT '0',
  `tebang` float DEFAULT '0',
  `row_lain_lain` int DEFAULT '0',
  `jamperan` int DEFAULT '0',
  `kawat_terburai` int DEFAULT '0',
  `gangguan` tinyint(1) DEFAULT '0',
  `set_label` enum('latih','uji') COLLATE utf8mb3_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `data_pemeliharaan`
--

INSERT INTO `data_pemeliharaan` (`id_data_pemeliharaan`, `nama_penyulang`, `kategori`, `pengukuran`, `arrester`, `grounding`, `ultrasonic`, `cover_isolasi`, `pangkas`, `tebang`, `row_lain_lain`, `jamperan`, `kawat_terburai`, `gangguan`, `set_label`) VALUES
(1, 'LK 01 / COT GIREK', 'gardu', 10, 1, 1, 0, 1, 3.5, 10, 2, 1, 0, 1, 'latih'),
(2, 'LK 02 / INC PL3', 'sutm', 15, 0, 0, 0, 0, 8, 15, 0, 0, 0, 0, 'latih'),
(3, 'LK 03 / BUKIT HAGU', 'gardu', 7, 1, 0, 0, 0, 4, 12, 3, 1, 1, 1, 'latih'),
(4, 'LK 04 / LHOKSUKON', 'gardu', 15, 0, 0, 0, 0, 5, 17, 7, 0, 0, 1, 'uji'),
(5, 'LK 05 / MATANG KULI', 'sutm', 22, 0, 0, 1, 1, 21.5, 80, 36, 0, 0, 0, 'uji'),
(6, 'LK 04 / LHOKSUKON', 'gardu', 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 0, 'latih'),
(7, 'LK 04 / LHOKSUKON', 'sutm', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'uji'),
(8, 'LK 04 / LHOKSUKON1', 'sutm', 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 'uji'),
(9, 'LK 04 / LHOKSUKON3', 'sutm', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 'latih');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `no_hp` varchar(20) COLLATE utf8mb3_swedish_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb3_swedish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_swedish_ci NOT NULL,
  `level` varchar(30) COLLATE utf8mb3_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `email`, `no_hp`, `username`, `password`, `level`) VALUES
(1, 'admin@gmail.com', '081261802409', 'admin', 'admin', 'Administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_pemeliharaan`
--
ALTER TABLE `data_pemeliharaan`
  ADD PRIMARY KEY (`id_data_pemeliharaan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_pemeliharaan`
--
ALTER TABLE `data_pemeliharaan`
  MODIFY `id_data_pemeliharaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
