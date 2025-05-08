-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 08, 2025 at 01:27 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

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
  `tanggal` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `nama_objek` enum('gardu','sutm') COLLATE utf8mb3_swedish_ci NOT NULL,
  `id_sub_kategori` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `data_pemeliharaan`
--

INSERT INTO `data_pemeliharaan` (`id_data_pemeliharaan`, `tanggal`, `nama_objek`, `id_sub_kategori`) VALUES
(182, 'Maret-2023', 'gardu', 134),
(183, 'Maret-2023', 'gardu', 135),
(184, 'Maret-2023', 'gardu', 136),
(185, 'Maret-2023', 'gardu', 137),
(186, 'Maret-2023', 'gardu', 138),
(187, 'Maret-2023', 'gardu', 139),
(188, 'Maret-2023', 'gardu', 140),
(189, 'Maret-2023', 'gardu', 141),
(190, 'Maret-2023', 'gardu', 142),
(191, 'Maret-2023', 'gardu', 143),
(192, 'Maret-2023', 'gardu', 144),
(193, 'Maret-2023', 'gardu', 145),
(194, 'April-2023', 'gardu', 146),
(195, 'April-2023', 'gardu', 147),
(196, 'April-2023', 'gardu', 148),
(197, 'April-2023', 'gardu', 149),
(198, 'April-2023', 'gardu', 150),
(199, 'April-2023', 'gardu', 151),
(200, 'April-2023', 'gardu', 152),
(201, 'April-2023', 'gardu', 153),
(202, 'April-2023', 'gardu', 154),
(203, 'April-2023', 'gardu', 155),
(204, 'April-2023', 'gardu', 156),
(205, 'April-2023', 'gardu', 157),
(206, 'Mei-2023', 'gardu', 158),
(207, 'Mei-2023', 'gardu', 159),
(208, 'Mei-2023', 'gardu', 160),
(209, 'Mei-2023', 'gardu', 161),
(210, 'Mei-2023', 'gardu', 162),
(211, 'Mei-2023', 'gardu', 163),
(212, 'Mei-2023', 'gardu', 164),
(213, 'Mei-2023', 'gardu', 165),
(214, 'Mei-2023', 'gardu', 166),
(215, 'Mei-2023', 'gardu', 167),
(216, 'Mei-2023', 'gardu', 168),
(217, 'Mei-2023', 'gardu', 169),
(218, 'Juni-2023', 'gardu', 170),
(219, 'Juni-2023', 'gardu', 171),
(220, 'Juni-2023', 'gardu', 172),
(221, 'Juni-2023', 'gardu', 173),
(222, 'Juni-2023', 'gardu', 174),
(223, 'Juni-2023', 'gardu', 175),
(224, 'Juni-2023', 'gardu', 176),
(225, 'Juni-2023', 'gardu', 177),
(226, 'Juni-2023', 'gardu', 178),
(227, 'Juni-2023', 'gardu', 179),
(228, 'Juni-2023', 'gardu', 180),
(229, 'Juni-2023', 'gardu', 181),
(230, 'Juli-2023', 'gardu', 182),
(231, 'Juli-2023', 'gardu', 183),
(232, 'Juli-2023', 'gardu', 184),
(233, 'Juli-2023', 'gardu', 185),
(234, 'Juli-2023', 'gardu', 186),
(235, 'Juli-2023', 'gardu', 187),
(236, 'Juli-2023', 'gardu', 188),
(237, 'Juli-2023', 'gardu', 189),
(238, 'Juli-2023', 'gardu', 190),
(239, 'Juli-2023', 'gardu', 191),
(240, 'Juli-2023', 'gardu', 192),
(241, 'Juli-2023', 'gardu', 193),
(242, 'Agustus-2023', 'gardu', 194),
(243, 'Agustus-2023', 'gardu', 195),
(244, 'Agustus-2023', 'gardu', 196),
(245, 'Agustus-2023', 'gardu', 197),
(246, 'Agustus-2023', 'gardu', 198),
(247, 'Agustus-2023', 'gardu', 199),
(248, 'Agustus-2023', 'gardu', 200),
(249, 'Agustus-2023', 'gardu', 201),
(250, 'Agustus-2023', 'gardu', 202),
(251, 'Agustus-2023', 'gardu', 203),
(252, 'Agustus-2023', 'gardu', 204),
(253, 'Agustus-2023', 'gardu', 205),
(254, 'September-2023', 'gardu', 206),
(255, 'September-2023', 'gardu', 207),
(256, 'September-2023', 'gardu', 208),
(257, 'September-2023', 'gardu', 209),
(258, 'September-2023', 'gardu', 210),
(259, 'September-2023', 'gardu', 211),
(260, 'September-2023', 'gardu', 212),
(261, 'September-2023', 'gardu', 213),
(262, 'September-2023', 'gardu', 214),
(263, 'September-2023', 'gardu', 215),
(264, 'September-2023', 'gardu', 216),
(265, 'September-2023', 'gardu', 217),
(266, 'Oktober-2023', 'gardu', 218),
(267, 'Oktober-2023', 'gardu', 219),
(268, 'Oktober-2023', 'gardu', 220),
(269, 'Oktober-2023', 'gardu', 221),
(270, 'Oktober-2023', 'gardu', 222),
(271, 'Oktober-2023', 'gardu', 223),
(272, 'Oktober-2023', 'gardu', 224),
(273, 'Oktober-2023', 'gardu', 225),
(274, 'Oktober-2023', 'gardu', 226),
(275, 'Oktober-2023', 'gardu', 227),
(276, 'Oktober-2023', 'gardu', 228),
(277, 'Oktober-2023', 'gardu', 229),
(278, 'November-2023', 'gardu', 230),
(279, 'November-2023', 'gardu', 231),
(280, 'November-2023', 'gardu', 232),
(281, 'November-2023', 'gardu', 233),
(282, 'November-2023', 'gardu', 234),
(283, 'November-2023', 'gardu', 235),
(284, 'November-2023', 'gardu', 236),
(285, 'November-2023', 'gardu', 237),
(286, 'November-2023', 'gardu', 238),
(287, 'November-2023', 'gardu', 239),
(288, 'November-2023', 'gardu', 240),
(289, 'November-2023', 'gardu', 241),
(290, 'Desember-2023', 'gardu', 242),
(291, 'Desember-2023', 'gardu', 243),
(292, 'Desember-2023', 'gardu', 244),
(293, 'Desember-2023', 'gardu', 245),
(294, 'Desember-2023', 'gardu', 246),
(295, 'Desember-2023', 'gardu', 247),
(296, 'Desember-2023', 'gardu', 248),
(297, 'Desember-2023', 'gardu', 249),
(298, 'Desember-2023', 'gardu', 250),
(299, 'Desember-2023', 'gardu', 251),
(300, 'Desember-2023', 'gardu', 252),
(301, 'Desember-2023', 'gardu', 253),
(327, 'Maret-2023', 'sutm', 13),
(328, 'Maret-2023', 'sutm', 14),
(329, 'Maret-2023', 'sutm', 15),
(330, 'Maret-2023', 'sutm', 16),
(331, 'Maret-2023', 'sutm', 17),
(332, 'Maret-2023', 'sutm', 18),
(333, 'Maret-2023', 'sutm', 19),
(334, 'Maret-2023', 'sutm', 20),
(335, 'Maret-2023', 'sutm', 21),
(336, 'Maret-2023', 'sutm', 22),
(337, 'Maret-2023', 'sutm', 23),
(338, 'Maret-2023', 'sutm', 24),
(339, 'April-2023', 'sutm', 25),
(340, 'April-2023', 'sutm', 26),
(341, 'April-2023', 'sutm', 27),
(342, 'April-2023', 'sutm', 28),
(343, 'April-2023', 'sutm', 29),
(344, 'April-2023', 'sutm', 30),
(345, 'April-2023', 'sutm', 31),
(346, 'April-2023', 'sutm', 32),
(347, 'April-2023', 'sutm', 33),
(348, 'April-2023', 'sutm', 34),
(349, 'April-2023', 'sutm', 35),
(350, 'April-2023', 'sutm', 36),
(351, 'Mei-2023', 'sutm', 37),
(352, 'Mei-2023', 'sutm', 38),
(353, 'Mei-2023', 'sutm', 39),
(354, 'Mei-2023', 'sutm', 40),
(355, 'Mei-2023', 'sutm', 41),
(356, 'Mei-2023', 'sutm', 42),
(357, 'Mei-2023', 'sutm', 43),
(358, 'Mei-2023', 'sutm', 44),
(359, 'Mei-2023', 'sutm', 45),
(360, 'Mei-2023', 'sutm', 46),
(361, 'Mei-2023', 'sutm', 47),
(362, 'Mei-2023', 'sutm', 48);

-- --------------------------------------------------------

--
-- Table structure for table `gardu`
--

CREATE TABLE `gardu` (
  `id_gardu` int NOT NULL,
  `nama_penyulang` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `t1_inspeksi` float DEFAULT '0',
  `t1_realisasi` float DEFAULT '0',
  `t2_inspeksi` float DEFAULT '0',
  `t2_realisasi` float DEFAULT '0',
  `pengukuran` float DEFAULT '0',
  `pergantian_arrester` float DEFAULT '0',
  `pergantian_fco` float DEFAULT '0',
  `relokasi_gardu` float DEFAULT '0',
  `pembangunan_gardu_siapan` float DEFAULT '0',
  `penyimbang_beban_gardu` float DEFAULT '0',
  `pemecahan_beban_gardu` float DEFAULT '0',
  `perubahan_tap_charger_trafo` float DEFAULT '0',
  `pergantian_box` float DEFAULT '0',
  `pergantian_opstic` float DEFAULT '0',
  `perbaikan_grounding` float DEFAULT '0',
  `accesoris_gardu` float DEFAULT '0',
  `pergantian_kabel_isolasi` float DEFAULT '0',
  `pemasangan_cover_isolasi` float DEFAULT '0',
  `pemasangan_penghalang_panjat` float DEFAULT '0',
  `alat_ultrasonik` float DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `gardu`
--

INSERT INTO `gardu` (`id_gardu`, `nama_penyulang`, `t1_inspeksi`, `t1_realisasi`, `t2_inspeksi`, `t2_realisasi`, `pengukuran`, `pergantian_arrester`, `pergantian_fco`, `relokasi_gardu`, `pembangunan_gardu_siapan`, `penyimbang_beban_gardu`, `pemecahan_beban_gardu`, `perubahan_tap_charger_trafo`, `pergantian_box`, `pergantian_opstic`, `perbaikan_grounding`, `accesoris_gardu`, `pergantian_kabel_isolasi`, `pemasangan_cover_isolasi`, `pemasangan_penghalang_panjat`, `alat_ultrasonik`) VALUES
(134, 'LK 01 / COT GIREK', 297, 297, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0),
(135, 'LK 02 / INC PL3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(136, 'LK 03 / BUKIT HAGU', 115, 115, 0, 0, 9, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2, 0),
(137, 'LK 04 / LHOKSUKON', 18, 18, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(138, 'LK 05 / MATANG KULI', 173, 173, 0, 0, 24, 0, 0, 0, 0, 2, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(139, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(140, 'LK 07 / LAPANG', 0, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(141, 'LK 08 / POLRES,PDAM', 47, 47, 0, 0, 17, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(142, 'LK 09 / PAYA BAKONG', 64, 64, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(143, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(144, 'LW 10', 2, 2, 0, 0, 25, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0),
(145, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(146, 'LK 01 / COT GIREK', 29.9, 29.9, 0, 0, 17, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(147, 'LK 02 / INC PL3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(148, 'LK 03 / BUKIT HAGU', 10.2, 10.2, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(149, 'LK 04 / LHOKSUKON', 14.9, 14.9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 18, 3, 0),
(150, 'LK 05 / MATANG KULI', 6.5, 6.5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(151, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(152, 'LK 07 / LAPANG', 24, 24, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0),
(153, 'LK 08 / POLRES,PDAM', 24.9, 24.9, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(154, 'LK 09 / PAYA BAKONG', 0, 0, 0, 0, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(155, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(156, 'LW 10', 14.3, 14.3, 0, 0, 30, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(157, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(158, 'LK 01 / COT GIREK', 26.1, 26.1, 0, 0, 14, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0),
(159, 'LK 02 / INC PL3', 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(160, 'LK 03 / BUKIT HAGU', 19, 19, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0),
(161, 'LK 04 / LHOKSUKON', 0, 0, 0, 0, 26, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0),
(162, 'LK 05 / MATANG KULI', 62, 62, 0, 0, 28, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0),
(163, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(164, 'LK 07 / LAPANG', 0, 0, 0, 0, 18, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(165, 'LK 08 / POLRES,PDAM', 6.9, 6.9, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(166, 'LK 09 / PAYA BAKONG', 22.4, 22.4, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(167, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(168, 'LW 10', 14.5, 14.5, 0, 0, 21, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(169, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(170, 'LK 01 / COT GIREK', 11.59, 11.59, 0, 0, 19, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0),
(171, 'LK 02 / INC PL3', 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(172, 'LK 03 / BUKIT HAGU', 11.5, 11.5, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(173, 'LK 04 / LHOKSUKON', 7.5, 7.5, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(174, 'LK 05 / MATANG KULI', 23.7, 23.7, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(175, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(176, 'LK 07 / LAPANG', 12.9, 12.9, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(177, 'LK 08 / POLRES,PDAM', 14, 14, 0, 0, 18, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(178, 'LK 09 / PAYA BAKONG', 17, 17, 0, 0, 21, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(179, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(180, 'LW 10', 0, 0, 0, 0, 29, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(181, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(182, 'LK 01 / COT GIREK', 4.3, 4.3, 0, 0, 16, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0),
(183, 'LK 02 / INC PL3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(184, 'LK 03 / BUKIT HAGU', 31.3, 31.3, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0),
(185, 'LK 04 / LHOKSUKON', 16, 16, 0, 0, 12, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(186, 'LK 05 / MATANG KULI', 0, 0, 0, 0, 19, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0),
(187, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(188, 'LK 07 / LAPANG', 4.7, 4.7, 0, 0, 14, 6, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0),
(189, 'LK 08 / POLRES,PDAM', 0, 0, 0, 0, 21, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(190, 'LK 09 / PAYA BAKONG', 9.1, 9.1, 0, 0, 13, 3, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(191, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(192, 'LW 10', 47.9, 47.9, 0, 0, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(193, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(194, 'LK 01 / COT GIREK', 41.4, 41.4, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(195, 'LK 02 / INC PL3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(196, 'LK 03 / BUKIT HAGU', 3.9, 3.9, 0, 0, 17, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0),
(197, 'LK 04 / LHOKSUKON', 0, 0, 0, 0, 16, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(198, 'LK 05 / MATANG KULI', 21.6, 21.6, 0, 0, 25, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0),
(199, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(200, 'LK 07 / LAPANG', 3.7, 3.7, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(201, 'LK 08 / POLRES,PDAM', 8, 0, 0, 0, 2, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(202, 'LK 09 / PAYA BAKONG', 19, 19, 0, 0, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(203, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(204, 'LW 10', 3.1, 3.1, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(205, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(206, 'LK 01 / COT GIREK', 16.7, 16.7, 0, 0, 22, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(207, 'LK 02 / INC PL3', 3.5, 3.5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(208, 'LK 03 / BUKIT HAGU', 13.5, 13.5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(209, 'LK 04 / LHOKSUKON', 0, 0, 0, 0, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(210, 'LK 05 / MATANG KULI', 3.6, 3.6, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(211, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(212, 'LK 07 / LAPANG', 3.1, 3.1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(213, 'LK 08 / POLRES,PDAM', 11.2, 11.2, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(214, 'LK 09 / PAYA BAKONG', 19.8, 19.8, 0, 0, 22, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(215, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(216, 'LW 10', 20.7, 20.7, 0, 0, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(217, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(218, 'LK 01 / COT GIREK', 22.4, 22.4, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0),
(219, 'LK 02 / INC PL3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(220, 'LK 03 / BUKIT HAGU', 17.1, 17.1, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0),
(221, 'LK 04 / LHOKSUKON', 2.6, 2.6, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0),
(222, 'LK 05 / MATANG KULI', 12.2, 12.2, 0, 0, 11, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(223, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(224, 'LK 07 / LAPANG', 6.1, 6.1, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(225, 'LK 08 / POLRES,PDAM', 6.5, 6.5, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(226, 'LK 09 / PAYA BAKONG', 13.5, 13.5, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(227, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(228, 'LW 10', 3.6, 3.6, 0, 0, 23, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(229, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(230, 'LK 01 / COT GIREK', 12.1, 12.1, 0, 0, 22, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(231, 'LK 02 / INC PL3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(232, 'LK 03 / BUKIT HAGU', 9.9, 9.9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(233, 'LK 04 / LHOKSUKON', 17.4, 17.4, 0, 0, 8, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(234, 'LK 05 / MATANG KULI', 30.6, 30.6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(235, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(236, 'LK 07 / LAPANG', 6.3, 6.3, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(237, 'LK 08 / POLRES,PDAM', 4.3, 4.3, 0, 0, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(238, 'LK 09 / PAYA BAKONG', 12, 12, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(239, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(240, 'LW 10', 33.7, 33.7, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(241, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(242, 'LK 01 / COT GIREK', 0, 0, 0, 0, 18, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(243, 'LK 02 / INC PL3', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(244, 'LK 03 / BUKIT HAGU', 41.9, 41.9, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 0),
(245, 'LK 04 / LHOKSUKON', 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(246, 'LK 05 / MATANG KULI', 26.9, 26.9, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(247, 'LK 06 / INC LW9', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(248, 'LK 07 / LAPANG', 15.1, 15.1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(249, 'LK 08 / POLRES,PDAM', 10, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(250, 'LK 09 / PAYA BAKONG', 10.4, 10.4, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(251, 'LW 09', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(252, 'LW 10', 7.6, 7.6, 0, 0, 20, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(253, 'PL 03', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `hasil_prediksi_risiko`
--

CREATE TABLE `hasil_prediksi_risiko` (
  `id_prediksi` int NOT NULL,
  `nama_penyulang` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `tingkat_risiko` varchar(10) COLLATE utf8mb3_swedish_ci NOT NULL,
  `nilai_risiko` decimal(10,2) NOT NULL,
  `tanggal_prediksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_kegiatan` int DEFAULT '0',
  `k_value` int DEFAULT '3'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `hasil_prediksi_risiko`
--

INSERT INTO `hasil_prediksi_risiko` (`id_prediksi`, `nama_penyulang`, `tingkat_risiko`, `nilai_risiko`, `tanggal_prediksi`, `total_kegiatan`, `k_value`) VALUES
(157, 'LK 01 / COT GIREK', 'TINGGI', '93.23', '2025-05-08 20:13:27', 799, 5),
(158, 'LK 02 / INC PL3', 'RENDAH', '24.53', '2025-05-08 20:13:27', 3, 5),
(159, 'LK 03 / BUKIT HAGU', 'TINGGI', '80.61', '2025-05-08 20:13:27', 426, 5),
(160, 'LK 04 / LHOKSUKON', 'RENDAH', '20.23', '2025-05-08 20:13:27', 257, 5),
(161, 'LK 05 / MATANG KULI', 'RENDAH', '19.83', '2025-05-08 20:13:27', 153, 5),
(162, 'LK 06 / INC LW9', 'RENDAH', '37.38', '2025-05-08 20:13:27', 15, 5),
(163, 'LK 07 / LAPANG', 'RENDAH', '15.29', '2025-05-08 20:13:27', 154, 5),
(164, 'LK 08 / POLRES,PDAM', 'SEDANG', '74.37', '2025-05-08 20:13:27', 278, 5),
(165, 'LK 09 / PAYA BAKONG', 'SEDANG', '57.44', '2025-05-08 20:13:27', 416, 5),
(166, 'LW 09', 'RENDAH', '30.50', '2025-05-08 20:13:27', 0, 5),
(167, 'LW 10', 'SEDANG', '53.88', '2025-05-08 20:13:27', 420, 5),
(168, 'PL 03', 'RENDAH', '28.69', '2025-05-08 20:13:27', 0, 5),
(169, 'LK 05 /  MATANG KULI', 'TINGGI', '78.99', '2025-05-08 20:13:27', 627, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sutm`
--

CREATE TABLE `sutm` (
  `id_sutm` int NOT NULL,
  `nama_penyulang` varchar(100) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `t1_inspeksi` int DEFAULT NULL,
  `t1_realisasi` int DEFAULT NULL,
  `t2_inspeksi` int DEFAULT NULL,
  `t2_realisasi` int DEFAULT NULL,
  `pangkas_kms` decimal(10,2) DEFAULT NULL,
  `pangkas_batang` int DEFAULT NULL,
  `tebang` int DEFAULT NULL,
  `row_lain` varchar(255) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `pin_isolator` int DEFAULT NULL,
  `suspension_isolator` int DEFAULT NULL,
  `traves_dan_armtie` int DEFAULT NULL,
  `tiang` int DEFAULT NULL,
  `accesoris_sutm` int DEFAULT NULL,
  `arrester_sutm` int DEFAULT NULL,
  `fco_sutm` int DEFAULT NULL,
  `grounding_sutm` int DEFAULT NULL,
  `perbaikan_andong_kendor` int DEFAULT NULL,
  `kawat_terburai` int DEFAULT NULL,
  `jamperan_sutm` int DEFAULT NULL,
  `skur` int DEFAULT NULL,
  `ganti_kabel_isolasi` int DEFAULT NULL,
  `pemasangan_cover_isolasi` int DEFAULT NULL,
  `pemasangan_penghalang_panjang` int DEFAULT NULL,
  `alat_ultrasonik` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `sutm`
--

INSERT INTO `sutm` (`id_sutm`, `nama_penyulang`, `t1_inspeksi`, `t1_realisasi`, `t2_inspeksi`, `t2_realisasi`, `pangkas_kms`, `pangkas_batang`, `tebang`, `row_lain`, `pin_isolator`, `suspension_isolator`, `traves_dan_armtie`, `tiang`, `accesoris_sutm`, `arrester_sutm`, `fco_sutm`, `grounding_sutm`, `perbaikan_andong_kendor`, `kawat_terburai`, `jamperan_sutm`, `skur`, `ganti_kabel_isolasi`, `pemasangan_cover_isolasi`, `pemasangan_penghalang_panjang`, `alat_ultrasonik`) VALUES
(13, 'LK 01 / COT GIREK', 69, 69, 0, 0, '69.10', 297, 16, '2', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 2, 0),
(14, 'LK 02 / INC PL3', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(15, 'LK 03 / BUKIT HAGU', 34, 34, 0, 0, '34.00', 115, 16, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2, 0),
(16, 'LK 04 / LHOKSUKON', 5, 5, 0, 0, '5.40', 18, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(17, 'LK 05 /  MATANG KULI', 31, 31, 0, 0, '30.70', 173, 13, '5', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(18, 'LK 06 / INC LW9', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 'LK 07 / LAPANG', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(20, 'LK 08 / POLRES,PDAM', 19, 19, 0, 0, '19.30', 47, 2, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(21, 'LK 09 / PAYA BAKONG', 21, 21, 0, 0, '21.00', 64, 6, '2', 0, 0, 1, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0),
(22, 'LW 09', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(23, 'LW 10', 3, 3, 0, 0, '2.50', 2, 2, '1', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(24, 'PL 03', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(25, 'LK 01 / COT GIREK', 30, 30, 0, 0, '0.00', 0, 37, '2', 3, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(26, 'LK 02 / INC PL3', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(27, 'LK 03 / BUKIT HAGU', 10, 10, 0, 0, '0.00', 30, 29, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(28, 'LK 04 / LHOKSUKON', 15, 15, 0, 0, '0.00', 0, 21, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 18, 3, 0),
(29, 'LK 05 /  MATANG KULI', 7, 7, 0, 0, '0.00', 10, 7, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(30, 'LK 06 / INC LW9', 0, 0, 0, 0, '0.00', 15, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(31, 'LK 07 / LAPANG', 24, 24, 0, 0, '0.00', 7, 36, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0),
(32, 'LK 08 / POLRES,PDAM', 25, 25, 0, 0, '0.00', 0, 60, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(33, 'LK 09 / PAYA BAKONG', 0, 0, 0, 0, '0.00', 24, 0, '0', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0),
(34, 'LW 09', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(35, 'LW 10', 14, 14, 0, 0, '0.00', 0, 23, '0', 3, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(36, 'PL 03', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(37, 'LK 01 / COT GIREK', 26, 26, 0, 0, '26.10', 124, 5, '0', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0),
(38, 'LK 02 / INC PL3', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(39, 'LK 03 / BUKIT HAGU', 19, 19, 0, 0, '19.00', 50, 3, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 9, 0, 0),
(40, 'LK 04 / LHOKSUKON', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 3, 0, 0, 0, 5, 0, 0, 9, 0, 0),
(41, 'LK 05 /  MATANG KULI', 62, 62, 0, 0, '62.00', 287, 42, '5', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0),
(42, 'LK 06 / INC LW9', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(43, 'LK 07 / LAPANG', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(44, 'LK 08 / POLRES,PDAM', 7, 7, 0, 0, '6.90', 28, 2, '2', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(45, 'LK 09 / PAYA BAKONG', 22, 22, 0, 0, '22.40', 64, 11, '2', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(46, 'LW 09', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(47, 'LW 10', 15, 15, 0, 0, '14.50', 94, 28, '1', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(48, 'PL 03', 0, 0, 0, 0, '0.00', 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

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
  ADD PRIMARY KEY (`id_data_pemeliharaan`),
  ADD KEY `id_sub_kategori` (`id_sub_kategori`),
  ADD KEY `nama_objek` (`nama_objek`);

--
-- Indexes for table `gardu`
--
ALTER TABLE `gardu`
  ADD PRIMARY KEY (`id_gardu`);

--
-- Indexes for table `hasil_prediksi_risiko`
--
ALTER TABLE `hasil_prediksi_risiko`
  ADD PRIMARY KEY (`id_prediksi`),
  ADD KEY `idx_prediksi_penyulang` (`nama_penyulang`),
  ADD KEY `idx_prediksi_tingkat` (`tingkat_risiko`);

--
-- Indexes for table `sutm`
--
ALTER TABLE `sutm`
  ADD PRIMARY KEY (`id_sutm`);

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
  MODIFY `id_data_pemeliharaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

--
-- AUTO_INCREMENT for table `gardu`
--
ALTER TABLE `gardu`
  MODIFY `id_gardu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `hasil_prediksi_risiko`
--
ALTER TABLE `hasil_prediksi_risiko`
  MODIFY `id_prediksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `sutm`
--
ALTER TABLE `sutm`
  MODIFY `id_sutm` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
