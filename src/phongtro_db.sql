-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2024 at 05:59 PM
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
-- Database: `phongtro_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `danhgia`
--

CREATE TABLE `danhgia` (
  `maDanhGia` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã đánh giá',
  `maNguoiDung` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã người dùng',
  `maPhongTro` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã phòng trọ',
  `diemSo` float UNSIGNED NOT NULL COMMENT 'Điểm đánh giá',
  `nhanXet` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Nhận xét của người dùng',
  `ngayNhanXet` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày đăng nhận xét'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `danhgia`
--

INSERT INTO `danhgia` (`maDanhGia`, `maNguoiDung`, `maPhongTro`, `diemSo`, `nhanXet`, `ngayNhanXet`) VALUES
('DG00001', 'ND00001', 'PT00001', 3.5, 'Phòng trọ vô cùng tốt, dịch vụ đầy đủ', '2024-12-27 09:30:24'),
('DG00002', 'ND00002', 'PT00001', 5, 'abc', '2024-12-27 09:45:32'),
('DG00003', 'ND00002', 'PT00001', 1, '123', '2024-12-27 09:45:46');

-- --------------------------------------------------------

--
-- Table structure for table `dichvu`
--

CREATE TABLE `dichvu` (
  `maDichVu` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã dịch vụ',
  `donVi` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Đơn vị tính tiền của dịch vụ',
  `tenDichVu` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Tên dịch vụ',
  `moTaDichVu` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mô tả của dịch vụ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `dichvu`
--

INSERT INTO `dichvu` (`maDichVu`, `donVi`, `tenDichVu`, `moTaDichVu`) VALUES
('DV00003', 'Khối', 'Tiền Nước', 'Tiền nước sinh hoạt, đóng tiền vào cuối tháng'),
('DV00004', 'kW', 'Tiền điện', 'Tiền điện, đóng tiền theo tháng');

-- --------------------------------------------------------

--
-- Table structure for table `hinhanhphong`
--

CREATE TABLE `hinhanhphong` (
  `maHinhAnh` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã hình ảnh',
  `maPhongTro` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã phòng trọ',
  `anhDaiDien` varchar(255) NOT NULL COMMENT 'Hình ảnh đại diện của phòng trọ',
  `hinhAnh` varchar(255) NOT NULL COMMENT 'Hình ảnh minh họa của phòng trọ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `hinhanhphong`
--

INSERT INTO `hinhanhphong` (`maHinhAnh`, `maPhongTro`, `anhDaiDien`, `hinhAnh`) VALUES
('', 'PT00003', '../images/phong_tro_9.jpg', '../images/phong_tro_9.jpg\r\n../images/phong_tro_10.jpg\r\n../images/phong_tro_11.jpg\r\n../images/phong_tro_12.jpg\r\n../images/phong_tro_13.jpg\r\n../images/phong_tro_14.jpg'),
('HA00001', 'PT00001', '../images/phong_tro_1.jpg', '../images/phong_tro_1.jpg\r\n../images/phong_tro_2.jpg\r\n../images/phong_tro_3.jpg\r\n../images/phong_tro_4.jpg'),
('HA00002', 'PT00002', '../images/phong_tro_5.jpg', '../images/phong_tro_5.jpg\r\n../images/phong_tro_6.jpg\r\n../images/phong_tro_7.jpg\r\n../images/phong_tro_8.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `khutro`
--

CREATE TABLE `khutro` (
  `maKhuTro` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã khu trọ',
  `tenKhuTro` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Tên khu trọ',
  `diaChi` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Địa chỉ khu trọ',
  `chuTro` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Tên chủ trọ',
  `sdtChuTro` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Số điện thoại của chủ trọ',
  `googleMap` text NOT NULL COMMENT 'Địa chỉ Google Map'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `khutro`
--

INSERT INTO `khutro` (`maKhuTro`, `tenKhuTro`, `diaChi`, `chuTro`, `sdtChuTro`, `googleMap`) VALUES
('KT00001', 'Phòng trọ bà Sáu Tây', 'Quán nhậu Cầu Lông Bình 3, Cầu Lông Bình 3, Xã Long Đức, Thành phố Trà Vinh, Trà Vinh', 'Thảo Uyên', '0559740592', '9.972942842412204, 106.33459978973937'),
('KT00002', 'nhà. cho thuê', '  Đường Võ Văn Kiệt, Phường 1, Thành phố Trà Vinh, Trà Vinh', 'Đạt Nguyễn', '0816747257', '9.953575637185054, 106.33612417139155'),
('KT00003', 'cho thuê phòng trọ phường 7 gần Tịnh Xá Ngọc Vân', 'Đường Nguyễn Thị Minh Khai, Phường 7, Thành phố Trà Vinh, Trà Vinh', 'Nhan', '070809****', '9.930203400304874, 106.3330390901221');

-- --------------------------------------------------------

--
-- Table structure for table `khutro_dichvu`
--

CREATE TABLE `khutro_dichvu` (
  `id` int(11) NOT NULL,
  `maDichVu` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `maKhuTro` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `giaCa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khutro_dichvu`
--

INSERT INTO `khutro_dichvu` (`id`, `maDichVu`, `maKhuTro`, `giaCa`) VALUES
(31, 'DV00003', 'KT00003', 3500),
(32, 'DV00004', 'KT00003', 15000);

-- --------------------------------------------------------

--
-- Table structure for table `loaiphong`
--

CREATE TABLE `loaiphong` (
  `maLoaiPhong` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã loại phòng',
  `tenLoaiPhong` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Tên loại phòng',
  `giaPhong` int(10) UNSIGNED NOT NULL COMMENT 'Giá cả phòng trọ',
  `moTaPhongTro` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mô tả phòng trọ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `loaiphong`
--

INSERT INTO `loaiphong` (`maLoaiPhong`, `tenLoaiPhong`, `giaPhong`, `moTaPhongTro`) VALUES
('LP00001', 'Loại 1', 500000, 'Phòng trọ nằm ngay chân cầu Long Bình 3 diện tích 4-5m2 phòng ở thoáng mát anh ninh tốt , chưa tính điện nước\r\nAi có nhu cầu liên hệ số\r\nSđt 0559740592'),
('LP00002', 'Loại 2', 2000000, '2 phòng ngủ, 2 phòng khách rộng rãi thoải mái. \r\nPhường 1, Võ Văn Kiệt, đèn xanh đỏ, Phú Hòa, gần karake mon. \r\n2 triệu/tháng .'),
('LP00003', 'Loại 3', 800000, 'Phòng trọ mới sữa chữa như mới sạch sẽ. Mặt tiền hẻm cao ráo. Điện 3500/kw nước 15000/khối. Còn 2p trống.');

-- --------------------------------------------------------

--
-- Table structure for table `nguoidung`
--

CREATE TABLE `nguoidung` (
  `maNguoiDung` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã người dùng',
  `tenNguoiDung` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Tên của người dùng',
  `matKhau` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mật khẩu đăng nhập',
  `tenDangNhap` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Tên đăng nhập của người dùng',
  `sdtNguoiDung` varchar(10) NOT NULL COMMENT 'Số điện thoại',
  `emailNguoiDung` varchar(255) NOT NULL COMMENT 'Email của người dùng',
  `vaiTro` enum('user','admin') NOT NULL COMMENT 'Vai trò của người dùng',
  `ngayDangKy` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày đăng ký tài khoản'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`maNguoiDung`, `tenNguoiDung`, `matKhau`, `tenDangNhap`, `sdtNguoiDung`, `emailNguoiDung`, `vaiTro`, `ngayDangKy`) VALUES
('AD00005', 'Administrator', 'admin123456', 'admin', '0969707708', 'habengialoc@gmail.com', 'admin', '2024-12-28 08:58:55'),
('ND00001', 'Trần Phạm', '123456', 'tranpham', '0969707809', 'tranpham@gmail.com', 'user', '2024-12-28 08:58:55'),
('ND00002', 'Hà Gia Lộc', '123456789', 'hagialoc', '', '', 'user', '2024-12-28 08:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `phongtro`
--

CREATE TABLE `phongtro` (
  `maPhongTro` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã phòng trọ',
  `maKhuTro` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã khu trọ',
  `maLoaiPhong` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Mã loại phòng',
  `dienTich` int(3) NOT NULL COMMENT 'Diện tích phòng trọ',
  `tinhTrang` enum('empty','rented') NOT NULL COMMENT 'Tình trạng phòng trọ',
  `ngayDang` date NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày đăng phòng trọ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `phongtro`
--

INSERT INTO `phongtro` (`maPhongTro`, `maKhuTro`, `maLoaiPhong`, `dienTich`, `tinhTrang`, `ngayDang`) VALUES
('PT00001', 'KT00001', 'LP00001', 12, 'empty', '2024-12-24'),
('PT00002', 'KT00002', 'LP00002', 80, 'empty', '2024-12-24'),
('PT00003', 'KT00003', 'LP00003', 20, 'empty', '2024-12-29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`maDanhGia`),
  ADD KEY `fk_danhgia-nguoidung` (`maNguoiDung`),
  ADD KEY `fk_danhgia-phongtro` (`maPhongTro`);

--
-- Indexes for table `dichvu`
--
ALTER TABLE `dichvu`
  ADD PRIMARY KEY (`maDichVu`);

--
-- Indexes for table `hinhanhphong`
--
ALTER TABLE `hinhanhphong`
  ADD PRIMARY KEY (`maHinhAnh`),
  ADD KEY `fk_hinhanhphong-phongtro` (`maPhongTro`);

--
-- Indexes for table `khutro`
--
ALTER TABLE `khutro`
  ADD PRIMARY KEY (`maKhuTro`);

--
-- Indexes for table `khutro_dichvu`
--
ALTER TABLE `khutro_dichvu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_khuTro_dichVu-dichVu` (`maDichVu`),
  ADD KEY `FK_khuTro_dichVu-khuTro` (`maKhuTro`);

--
-- Indexes for table `loaiphong`
--
ALTER TABLE `loaiphong`
  ADD PRIMARY KEY (`maLoaiPhong`);

--
-- Indexes for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`maNguoiDung`);

--
-- Indexes for table `phongtro`
--
ALTER TABLE `phongtro`
  ADD PRIMARY KEY (`maPhongTro`),
  ADD KEY `fk_phongtro-khutro` (`maKhuTro`),
  ADD KEY `fk_phongtro-loaiphong` (`maLoaiPhong`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `khutro_dichvu`
--
ALTER TABLE `khutro_dichvu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `fk_danhgia-nguoidung` FOREIGN KEY (`maNguoiDung`) REFERENCES `nguoidung` (`maNguoiDung`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_danhgia-phongtro` FOREIGN KEY (`maPhongTro`) REFERENCES `phongtro` (`maPhongTro`) ON UPDATE CASCADE;

--
-- Constraints for table `hinhanhphong`
--
ALTER TABLE `hinhanhphong`
  ADD CONSTRAINT `fk_hinhanhphong-phongtro` FOREIGN KEY (`maPhongTro`) REFERENCES `phongtro` (`maPhongTro`) ON UPDATE CASCADE;

--
-- Constraints for table `khutro_dichvu`
--
ALTER TABLE `khutro_dichvu`
  ADD CONSTRAINT `FK_khuTro_dichVu-dichVu` FOREIGN KEY (`maDichVu`) REFERENCES `dichvu` (`maDichVu`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_khuTro_dichVu-khuTro` FOREIGN KEY (`maKhuTro`) REFERENCES `khutro` (`maKhuTro`);

--
-- Constraints for table `phongtro`
--
ALTER TABLE `phongtro`
  ADD CONSTRAINT `fk_phongtro-khutro` FOREIGN KEY (`maKhuTro`) REFERENCES `khutro` (`maKhuTro`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phongtro-loaiphong` FOREIGN KEY (`maLoaiPhong`) REFERENCES `loaiphong` (`maLoaiPhong`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
