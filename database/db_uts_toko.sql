-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2024 at 02:20 AM
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
-- Database: `db_uts_toko`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `idbarang` char(6) NOT NULL,
  `namabarang` varchar(50) DEFAULT NULL,
  `merk` varchar(35) DEFAULT NULL,
  `deskripsi` varchar(200) DEFAULT NULL,
  `stok` int(9) DEFAULT NULL,
  `harga` int(10) NOT NULL,
  `foto` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`idbarang`, `namabarang`, `merk`, `deskripsi`, `stok`, `harga`, `foto`) VALUES
('B001', 'Kulkas', 'Sharp', 'Kulkas 2 Pintu', 20, 20000000, 'B001.png'),
('B002', 'Iphone 14 Pro Max', 'Apple', 'Iphone 14 Pro Max 2024', 10, 25000000, 'B002.png'),
('B003', 'Asus Vivo Book 14', 'Asus', 'Laptop Asus 14 Inch', 30, 11000000, 'B003.png'),
('B004', 'Headphone Sony', 'Sony', 'Headphone Bluetooth Sony', 10, 250000, 'B004.png'),
('B005', 'Mesin Cuci', 'Samsung', 'Mesin cuci 1 pintu', 50, 30000000, 'B005.png'),
('B006', 'Blender', 'Philip', 'Blender Buah', 50, 200000, 'B006.png'),
('B007', 'Printer', 'Canon', 'Printer CMYK', 30, 500000, 'B007.png'),
('B008', 'Mircowave', 'Panasonic', 'Microwave untuk pemanas makanan', 20, 700000, 'B008.png'),
('B009', 'Speaker JBL', 'JBL', 'Speaker Bluetooth', 20, 300000, 'B009.png'),
('B010', 'Kipas angin', 'Cosmos', 'Kipas angin listrik', 20, 100000, 'B010.png'),
('B011', 'TV 32 inch', 'Samsung', 'TV LED 32 Inch', 20, 30000000, 'B011.png');

-- --------------------------------------------------------

--
-- Table structure for table `identitasusaha`
--

CREATE TABLE `identitasusaha` (
  `idusaha` char(2) NOT NULL,
  `namausaha` varchar(50) DEFAULT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `notelepon` varchar(14) DEFAULT NULL,
  `fax` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `bank` varchar(35) DEFAULT NULL,
  `noaccount` varchar(25) DEFAULT NULL,
  `atasnama` varchar(35) DEFAULT NULL,
  `pimpinan` varchar(35) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `identitasusaha`
--

INSERT INTO `identitasusaha` (`idusaha`, `namausaha`, `alamat`, `notelepon`, `fax`, `email`, `npwp`, `bank`, `noaccount`, `atasnama`, `pimpinan`) VALUES
('U1', 'Jaya Elektronik', 'Jalan Pulang No 1', '08123456789', '02112345', 'Jaya_Elektronik@gmail.com', '2024123456789012', 'BCA', '332131313123123', 'Dono', 'Doni');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `staffid` int(10) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `foto` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`staffid`, `username`, `nama`, `password`, `foto`) VALUES
(1, 'budi123', 'Budi', '$2y$10$gGzOYjfdLEoflzkcUPhQveOQszigop3g8.kaY5H5rRy.gi.eq4Ism', 'budi123.png'),
(2, 'mawar123', 'Mawar', '$2y$10$3toU.7z1BJyRpfbUu90ITejOvg9b9RtZWMZeAIbHJq8/7UANgPgyO', 'mawar123.png');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `idbeli` char(9) NOT NULL,
  `idbarang` char(6) NOT NULL,
  `jumlah` int(9) DEFAULT NULL,
  `hargasatuan` int(10) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`idbeli`, `idbarang`, `jumlah`, `hargasatuan`, `tanggal`) VALUES
('PB0000001', 'B003', 10, 100000, '2024-10-11');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `idjual` char(9) NOT NULL,
  `idbarang` char(6) NOT NULL,
  `jumlah` int(9) DEFAULT NULL,
  `hargasatuan` int(10) NOT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`idbarang`);

--
-- Indexes for table `identitasusaha`
--
ALTER TABLE `identitasusaha`
  ADD PRIMARY KEY (`idusaha`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`staffid`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`idbeli`),
  ADD KEY `idbarang` (`idbarang`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`idjual`),
  ADD KEY `idbarang` (`idbarang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `staffid` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`);

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
