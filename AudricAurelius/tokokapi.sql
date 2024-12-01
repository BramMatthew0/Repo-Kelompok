-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 03:43 PM
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
-- Database: `tokokapi`
--

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `Nama_Pelanggan_Review` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Nama_Produk_Review` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Komentar_Review` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`Nama_Pelanggan_Review`, `Nama_Produk_Review`, `Komentar_Review`) VALUES
('Bram', 'Kursi Gaming', 'Bagus sekali!'),
('Rafli', 'Meja Belajar', 'Bagus sekali dan Rapih!'),
('Afifah', 'Meja Makan', 'Kurang Presisi tapi harga sudah oke'),
('John Dode', 'Kaki Meja', 'Bagus , cuman pengecatan kurang rapih '),
('Jane Doe', 'Kaki kursi', 'jelek , bahan ga sesuai'),
('Bambang', 'Kasur', 'Ga empuk , tapi kualitas sesuai harga'),
('Yesaya', 'gagang pintu A', 'pengecatan kurang rapih , tapi bentuknya sesuai ');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
