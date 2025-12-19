-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 10:49 AM
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
-- Database: `spp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `tingkat` enum('X','XI','XII') NOT NULL DEFAULT 'X',
  `jurusan` varchar(50) NOT NULL,
  `wali_kelas` varchar(100) DEFAULT NULL,
  `kapasitas` int(3) UNSIGNED NOT NULL DEFAULT 40,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `tingkat`, `jurusan`, `wali_kelas`, `kapasitas`, `created_at`, `updated_at`) VALUES
(1, 'X MIPA 1', 'X', 'MIPA', 'Dr. Ahmad Santoso, M.Pd', 40, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(2, 'X MIPA 2', 'X', 'MIPA', 'Siti Nurhaliza, S.Pd', 38, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(3, 'X IPS 1', 'XI', 'IPS', 'Budi Prasetyo, M.Pd', 42, '2025-12-16 08:36:25', '2025-12-17 09:34:59'),
(4, 'X IPS 2', 'X', 'IPS', 'Dewi Anggraeni, S.Pd', 35, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(5, 'XI MIPA 1', 'XI', 'MIPA', 'Prof. Rina Wijaya, M.Sc', 39, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(6, 'XI MIPA 2', 'XI', 'MIPA', 'Agus Supriyanto, S.Pd', 40, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(7, 'XI IPS 1', 'XI', 'IPS', 'Maya Sari, M.Pd', 37, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(8, 'XI IPS 2', 'XI', 'IPS', 'Joko Widodo, S.Pd', 41, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(9, 'XII MIPA 1', 'XII', 'MIPA', 'Dr. Fitriani, Ph.D', 36, '2025-12-16 08:36:25', '2025-12-16 08:36:25'),
(10, 'XII MIPA 2', 'XII', 'MIPA', 'Andi Permana, M.Pd', 40, '2025-12-16 08:36:25', '2025-12-16 08:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `bulan` varchar(20) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `total_transaksi` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `total_pembayaran` decimal(15,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `data_detail` longtext DEFAULT NULL COMMENT 'JSON data containing detailed report information',
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-12-06-053518', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1765805019, 1),
(2, '2025-12-06-053539', 'App\\Database\\Migrations\\CreateKelasTable', 'default', 'App', 1765805019, 1),
(3, '2025-12-06-053549', 'App\\Database\\Migrations\\CreateSiswaTable', 'default', 'App', 1765805019, 1),
(4, '2025-12-06-053559', 'App\\Database\\Migrations\\CreateSPPTable', 'default', 'App', 1765805020, 1),
(5, '2025-12-06-053608', 'App\\Database\\Migrations\\CreatePembayaranTable', 'default', 'App', 1765805020, 1),
(7, '2025-12-06-060000', 'App\\Database\\Migrations\\CreateLaporanTable', 'default', 'App', 1765972228, 2),
(8, '2025-12-06-053550', 'App\\Database\\Migrations\\AddFotoToSiswaTable', 'default', 'App', 1766064841, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_siswa` int(11) UNSIGNED NOT NULL,
  `id_spp` int(11) UNSIGNED DEFAULT NULL,
  `bulan` enum('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember') NOT NULL,
  `tahun` year(4) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` enum('Tunai','Transfer','QRIS') NOT NULL DEFAULT 'Tunai',
  `keterangan` text DEFAULT NULL,
  `status_pembayaran` enum('Lunas','Belum Lunas') NOT NULL DEFAULT 'Belum Lunas',
  `id_user` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `id_siswa`, `id_spp`, `bulan`, `tahun`, `tanggal_bayar`, `jumlah_bayar`, `metode_pembayaran`, `keterangan`, `status_pembayaran`, `id_user`, `created_at`, `updated_at`) VALUES
(50, 1, 1, 'Februari', '2025', '2025-12-18', 550000.00, 'Tunai', '', 'Lunas', 1, '2025-12-18 16:46:28', '2025-12-18 16:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) UNSIGNED NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL DEFAULT 'L',
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `id_kelas` int(11) UNSIGNED DEFAULT NULL,
  `tahun_masuk` year(4) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL COMMENT 'Password default adalah NISN (akan di-hash)',
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nisn`, `nama_siswa`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_hp`, `id_kelas`, `tahun_masuk`, `foto`, `password`, `remember_token`, `last_login`, `status`, `created_at`, `updated_at`) VALUES
(1, '1002003004', 'Muhammad Alfis Azis', 'L', 'Batusangkar', '2025-12-02', 'bkt', '082299129507', 1, '2025', NULL, '$2y$10$mG7OgNfBCv/Cq.63.dj9XOBx.bMwLBwVgRIqkXl7jeDalXAiVm.PG', NULL, '2025-12-18 13:36:45', 'active', '2025-12-16 10:36:00', '2025-12-18 13:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `spp`
--

CREATE TABLE `spp` (
  `id` int(11) UNSIGNED NOT NULL,
  `tahun_ajaran` varchar(9) NOT NULL,
  `tingkat` enum('X','XI','XII') NOT NULL DEFAULT 'X',
  `nominal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `status` enum('aktif','nonaktif','arsip') NOT NULL DEFAULT 'aktif',
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `spp`
--

INSERT INTO `spp` (`id`, `tahun_ajaran`, `tingkat`, `nominal`, `keterangan`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2025/2026', 'X', 550000.00, 'SPP Tahun Ajaran 2025/2026 untuk Kelas X', 'aktif', 13, 13, '2025-12-16 08:41:41', '2025-12-17 10:47:55', NULL),
(2, '2025/2026', 'XI', 600000.00, 'SPP Tahun Ajaran 2025/2026 untuk Kelas XI', 'aktif', 13, 13, '2025-12-16 08:41:41', '2025-12-17 10:49:43', NULL),
(3, '2025/2026', 'XII', 650000.00, 'SPP Tahun Ajaran 2025/2026 untuk Kelas XII', 'aktif', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL),
(4, '2024/2025', 'X', 500000.00, 'SPP Tahun Ajaran 2024/2025 untuk Kelas X', 'aktif', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL),
(5, '2024/2025', 'XI', 550000.00, 'SPP Tahun Ajaran 2024/2025 untuk Kelas XI', 'aktif', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL),
(6, '2024/2025', 'XII', 600000.00, 'SPP Tahun Ajaran 2024/2025 untuk Kelas XII', 'aktif', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL),
(7, '2023/2024', 'X', 450000.00, 'SPP Tahun Ajaran 2023/2024 untuk Kelas X', 'arsip', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL),
(8, '2023/2024', 'XI', 500000.00, 'SPP Tahun Ajaran 2023/2024 untuk Kelas XI', 'arsip', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL),
(9, '2023/2024', 'XII', 550000.00, 'SPP Tahun Ajaran 2023/2024 untuk Kelas XII', 'arsip', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL),
(10, '2022/2023', 'X', 400000.00, 'SPP Tahun Ajaran 2022/2023 untuk Kelas X', 'arsip', 13, 13, '2025-12-16 08:41:41', '2025-12-16 08:41:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `nama_lengkap`, `role`, `status`, `remember_token`, `last_login`, `foto_profil`, `no_telepon`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'superadmin@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Super Administrator', 'admin', 'active', NULL, NULL, NULL, '081234567890', 'Jl. Administrasi No. 1', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(2, 'petugas1', 'petugas1@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Budi Santoso', 'petugas', 'active', NULL, NULL, NULL, '081234567891', 'Jl. Petugas No. 1', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(3, 'petugas2', 'petugas2@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Siti Rahayu', 'petugas', 'active', NULL, NULL, NULL, '081234567892', 'Jl. Petugas No. 2', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(4, 'petugas3', 'petugas3@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Ahmad Fauzi', 'petugas', 'active', NULL, NULL, NULL, '081234567893', 'Jl. Petugas No. 3', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(5, 'petugas4', 'petugas4@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Rina Melati', 'petugas', 'active', NULL, NULL, NULL, '081234567894', 'Jl. Petugas No. 4', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(6, 'petugas5', 'petugas5@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Joko Prasetyo', 'petugas', 'active', NULL, NULL, NULL, '081234567895', 'Jl. Petugas No. 5', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(7, 'admin2', 'admin2@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Administrator 2', 'admin', 'active', NULL, '2025-12-18 02:55:08', NULL, '081234567896', 'Jl. Administrasi No. 2', '2025-12-16 08:33:48', '2025-12-18 02:55:08'),
(8, 'petugas6', 'petugas6@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Dewi Anggraini', 'petugas', 'active', NULL, NULL, NULL, '081234567897', 'Jl. Petugas No. 6', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(9, 'petugas7', 'petugas7@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Agus Salim', 'petugas', 'active', NULL, NULL, NULL, '081234567898', 'Jl. Petugas No. 7', '2025-12-16 08:33:48', '2025-12-16 08:33:48'),
(10, 'petugas8', 'petugas8@spp.com', '$2y$10$9g5bHBwqmf72LA8/dn2ygeTflPBHFFtpcTwu6ORdqVD/UYQoY4Ora', 'Maya Sari', 'petugas', 'active', NULL, NULL, NULL, '081234567899', 'Jl. Petugas No. 8', '2025-12-16 08:33:48', '2025-12-16 08:33:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_created_by_foreign` (`created_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_id_siswa_foreign` (`id_siswa`),
  ADD KEY `pembayaran_id_spp_foreign` (`id_spp`),
  ADD KEY `pembayaran_id_user_foreign` (`id_user`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `siswa_id_kelas_foreign` (`id_kelas`);

--
-- Indexes for table `spp`
--
ALTER TABLE `spp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tahun_tingkat` (`tahun_ajaran`,`tingkat`),
  ADD KEY `idx_tahun_ajaran` (`tahun_ajaran`),
  ADD KEY `idx_tingkat` (`tingkat`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_updated_at` (`updated_at`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_updated_by` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `spp`
--
ALTER TABLE `spp`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_id_spp_foreign` FOREIGN KEY (`id_spp`) REFERENCES `spp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
