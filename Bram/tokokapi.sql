-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 03:23 PM
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
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `Nama_Contact` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Review_Contact` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Date_Contact` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`Nama_Contact`, `Review_Contact`, `Date_Contact`) VALUES
('bram', 'Barang bisa di kirim ke daerah jakarta tidak?', '2024-12-01');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `Nama_Pelanggan_History` varchar(100) DEFAULT NULL,
  `Id_Produk_History` varchar(100) DEFAULT NULL,
  `Nama_Produk_History` varchar(255) DEFAULT NULL,
  `Material_Produk_History` varchar(255) DEFAULT NULL,
  `Warna_Produk_History` varchar(50) DEFAULT NULL,
  `Harga_Produk_History` decimal(10,2) DEFAULT NULL,
  `Jumlah_Produk_History` varchar(100) DEFAULT NULL,
  `Tanggal_History` date NOT NULL DEFAULT current_timestamp(),
  `Tanggal_Estimasi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`Nama_Pelanggan_History`, `Id_Produk_History`, `Nama_Produk_History`, `Material_Produk_History`, `Warna_Produk_History`, `Harga_Produk_History`, `Jumlah_Produk_History`, `Tanggal_History`, `Tanggal_Estimasi`) VALUES
('Bram', '1', 'Kursi', 'Kayu', 'Merah', 700000.00, '1', '2024-12-02', '2024-12-04'),
('bram', '2', 'Meja', 'Kaca', 'Kuning', 200000.00, '1', '2024-11-02', '2024-11-06'),
('bram', '2', 'Meja', 'Kaca', 'Kuning', 200000.00, '1', '2020-2-02', '2020-2-06'),
('bram', '2', 'Meja', 'Kaca', 'Kuning', 200000.00, '1', '2022-4-10', '2022-4-12'),
('bram', '2', 'Meja', 'Kaca', 'Kuning', 200000.00, '1', '2023-1-01', '2023-1-03'),
('Bram', '3', 'Lemari', 'Kayu', 'Coklat', 1500000.00, '1', '2024-12-02', '2024-12-10');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `Nama_Pelanggan_Keranjang` varchar(255) DEFAULT NULL,
  `Id_Produk_Keranjang` int(11) NOT NULL,
  `Nama_Produk_Keranjang` varchar(255) DEFAULT NULL,
  `Material_Produk_Keranjang` varchar(255) DEFAULT NULL,
  `Warna_Produk_Keranjang` varchar(50) DEFAULT NULL,
  `Harga_Produk_Keranjang` decimal(10,2) DEFAULT NULL,
  `Jumlah_Produk_Keranjang` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`Nama_Pelanggan_Keranjang`, `Id_Produk_Keranjang`, `Nama_Produk_Keranjang`, `Material_Produk_Keranjang`, `Warna_Produk_Keranjang`, `Harga_Produk_Keranjang`, `Jumlah_Produk_Keranjang`) VALUES
('bram', 2, 'Meja', 'Kaca', 'Coklat', 200000.00, '4');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `Nama` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `No_HP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Alamat` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Kecamatan` enum('Andir','Astana Anyar','Antapani','Arcamanik','Babakan CIparay','Bandung Kidul','Bandung Kulon','Bandung Wetan','Batununggal','Bojongloa Kaler','Bojongloa Kidul','Buah Batu','Cibeunying Kaler','Cibeunying Kidul','Cibiru','Cicendo','Cidadap','Cinambo','Coblong','GedeBage','KiaraCondong','Lengkong','Mandalajati','Pangyileukan','Rancasari','Regol','Sukajadi','Sukasari','Sumur Bandung','Ujung Berung') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Kelurahan` enum('Ciroyom','Garuda','Kebon Jeruk','Maleber','Dungus Cariang','Cempaka','Karasak','Nyengseret','Panjunan','Pelindung Hewan','Cisaranten Bina Harapan','Antapani Kidul','Antapani Tengah','Antapani Wetan','Sukamiskin','Cisaranten Kulon','Cisantren Endah','Cisantren Kidul','Cijawura','Derwati','Manjahlega','Margasari','Margahayu Tengah','Ciateul','Balonggede','Paledang','Ancol','Karang Anyar','Dago','Pasteur','Cipedes','Sukabungah','Sukarasa','Isola','Geger Kalong','SariJadi','Babakan Ciamis','Merdeka','Kebon Pisang','Braga','Padasuka','Pasanggrahan','Cigending','Cijawura') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`Nama`, `No_HP`, `Email`, `Alamat`, `Kecamatan`, `Kelurahan`, `Username`, `Password`) VALUES
('bram', '082115424343', 'bram@gmail.com', 'bukit jarian', 'Cidadap', 'Cisaranten Kulon', 'bram', '$2y$10$9Or8ndhkiStEB.eSJSGh.OSxqlXpLgE8xh/916gbx5J9SRIggDmZ2');

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `Id_Pemilik` int(11) NOT NULL,
  `Nama_Pemilik` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Username_Pemilik` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Password_Pemilik` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`Id_Pemilik`, `Nama_Pemilik`, `Username_Pemilik`, `Password_Pemilik`) VALUES
(1, 'Audric Aurelius', 'audric', '12345678');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `Id_Produk` int(11) NOT NULL,
  `Nama_Produk` varchar(255) NOT NULL,
  `Warna_Produk` varchar(50) NOT NULL,
  `Harga_Produk` decimal(10,2) NOT NULL,
  `Material_Produk` varchar(255) NOT NULL,
  `Stok_Produk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`Id_Produk`, `Nama_Produk`, `Warna_Produk`, `Harga_Produk`, `Material_Produk`, `Stok_Produk`) VALUES
(0, 'Kasur Spring Bed Anak', 'Abu-abu', 2000000.00, 'Kain', 10),
(1, 'Lemari Pakaian', 'Coklat', 1500000.00, 'Kayu', 10),
(2, 'Lemari Dapur', 'Putih', 1200000.00, 'Kaca', 8),
(3, 'Lemari Buku', 'Hitam', 800000.00, 'Logam', 15),
(4, 'Kasur Spring Bed', 'Putih', 2500000.00, 'Kain', 5),
(5, 'Kasur Busa', 'Biru', 1800000.00, 'Kain', 7),
(6, 'Kaki Meja Besi', 'Hitam', 50000.00, 'Logam', 50),
(7, 'Kaki Meja Kayu', 'Coklat', 40000.00, 'Kayu', 30),
(8, 'Kaki Kasur Logam', 'Perak', 60000.00, 'Logam', 20),
(9, 'Kaki Kasur Plastik', 'Putih', 30000.00, 'Plastik', 40),
(10, 'Pegangan Pintu Stainless', 'Perak', 70000.00, 'Logam', 25),
(11, 'Pegangan Pintu Kayu', 'Coklat', 50000.00, 'Kayu', 15),
(12, 'Meja Makan', 'Coklat', 1800000.00, 'Kayu', 10),
(13, 'Meja Kantor', 'Hitam', 1500000.00, 'Logam', 8),
(14, 'Meja Tamu', 'Putih', 1200000.00, 'Kaca', 12),
(15, 'Meja Belajar', 'Biru', 800000.00, 'Kayu', 20),
(16, 'Kursi Tamu', 'Hitam', 500000.00, 'Kayu', 15),
(17, 'Kursi Kantor', 'Merah', 750000.00, 'Plastik', 10),
(18, 'Kursi Lipat', 'Abu-Abu', 300000.00, 'Logam', 25),
(19, 'Kursi Anak', 'Kuning', 200000.00, 'Kayu', 30);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `Nama_Pelanggan_Review` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Nama_Produk_Review` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Komentar_Review` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Nilai Penilaian` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`Nama_Pelanggan_Review`, `Nama_Produk_Review`, `Komentar_Review`, `Nilai Penilaian`) VALUES
('Bram', 'Kursi Gaming', 'Bagus sekali!', 5),
('Rafli', 'Meja Belajar', 'Bagus sekali dan Rapih!', 5),
('Afifah', 'Meja Makan', 'Kurang Presisi tapi harga sudah oke', 3),
('John Dode', 'Kaki Meja', 'Bagus , cuman pengecatan kurang rapih ', 4),
('Jane Doe', 'Kaki kursi', 'jelek , bahan ga sesuai', 1),
('Bambang', 'Kasur', 'Ga empuk , tapi kualitas sesuai harga', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`Id_Produk_Keranjang`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`Nama`),
  ADD KEY `Username` (`Username`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`Id_Pemilik`) USING BTREE,
  ADD KEY `Username` (`Nama_Pemilik`) USING BTREE;

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`Id_Produk`) USING BTREE,
  ADD KEY `Id_Produk` (`Nama_Produk`) USING BTREE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
