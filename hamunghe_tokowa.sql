-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 25 Des 2024 pada 08.11
-- Versi server: 10.6.20-MariaDB
-- Versi PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hamunghe_tokowa`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(200) NOT NULL,
  `cookie` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `cookie`) VALUES
(1, 'admin', '$2y$10$DSFIaZXtiE12dsVnYgr6k.0jgoJHFVj8zpdSTyA4ljDkdHWnTyJ0W', 'TVsvFjdKqIO9AEJ6PReoiHuWIDr0awT3zKJV7kdsPnu6fSDxyBLXZbhhxFZ5M4at');

-- --------------------------------------------------------

--
-- Struktur dari tabel `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `img` varchar(30) NOT NULL,
  `img_android` varchar(20) NOT NULL,
  `url` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `banner`
--

INSERT INTO `banner` (`id`, `img`, `img_android`, `url`) VALUES
(21, '1626779582456.jpg', 'coba.png', 'https://nandradigital.com'),
(22, '1626779609647.jpg', 'coba.png', 'https://nandradigital.com'),
(29, '1719559277838.png', '', 'https://wa.me/6282338979723');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `icon` varchar(30) NOT NULL,
  `slug` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `slug`) VALUES
(28, 'Perawatan dan Kesehatan Tubuh', '1722001349514.png', 'perawatan-dan-kesehatan-tubuh'),
(29, 'Kerajinan Tangan Dan Souvenir', '1722028520017.png', 'kerajinan-tangan-dan-souvenir'),
(32, 'Pulsa Dan Tagihan', '1722028548945.png', 'pulsa-dan-tagihan'),
(33, 'Perlengkapan Rumah', '1722028563950.png', 'perlengkapan-rumah'),
(34, 'Bimbingan Belajar', '1722028579113.png', 'bimbingan-belajar'),
(35, 'Busana Pria Dan Wanita', '1722028633166.png', 'busana-pria-dan-wanita'),
(38, 'Peluang Bisnis Dan Kerjasama', '1722028670532.png', 'peluang-bisnis-dan-kerjasama'),
(39, 'Spesial Promo  ', '1722028789641.png', 'spesial-promo--'),
(40, 'Makanan Dan Minuman', '1722028806470.png', 'makanan-dan-minuman'),
(42, 'Buku Dan ATK ', '1722028888925.png', 'buku-dan-atk-');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cod`
--

CREATE TABLE `cod` (
  `id` int(11) NOT NULL,
  `regency_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `cod`
--

INSERT INTO `cod` (`id`, `regency_id`) VALUES
(9, 151),
(10, 152),
(11, 153),
(12, 154),
(15, 55),
(16, 42),
(17, 42);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cost_delivery`
--

CREATE TABLE `cost_delivery` (
  `id` int(11) NOT NULL,
  `destination` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `cost_delivery`
--

INSERT INTO `cost_delivery` (`id`, `destination`, `price`) VALUES
(6, 42, 20000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_send`
--

CREATE TABLE `email_send` (
  `id` int(11) NOT NULL,
  `mail_to` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `footer`
--

CREATE TABLE `footer` (
  `id` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `footer`
--

INSERT INTO `footer` (`id`, `page`, `type`) VALUES
(1, 1, 1),
(2, 3, 1),
(3, 2, 2),
(4, 1, 1),
(5, 4, 1),
(6, 5, 1),
(7, 6, 2),
(8, 7, 2),
(9, 8, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `general`
--

CREATE TABLE `general` (
  `id` int(11) NOT NULL,
  `app_name` varchar(50) NOT NULL,
  `slogan` varchar(150) NOT NULL,
  `navbar_color` varchar(10) NOT NULL,
  `api_rajaongkir` varchar(70) NOT NULL,
  `account_gmail` varchar(50) NOT NULL,
  `pass_gmail` varchar(50) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `whatsappv2` varchar(20) NOT NULL,
  `email_contact` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `general`
--

INSERT INTO `general` (`id`, `app_name`, `slogan`, `navbar_color`, `api_rajaongkir`, `account_gmail`, `pass_gmail`, `whatsapp`, `whatsappv2`, `email_contact`) VALUES
(1, 'hamunghello.com', 'Belanja Mudah Via Whatsapp', '#1CCAA1', '5f2c911d37b8596b290a295a6e435226', 'hamunghello@gmail.com', '1122333', '081234567890', '081234567890', 'hamunghello@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `img_product`
--

CREATE TABLE `img_product` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `img` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `img_product`
--

INSERT INTO `img_product` (`id`, `id_product`, `img`) VALUES
(1, 22, '1589840767903.jpg'),
(2, 22, '1589840786550.jpg'),
(5, 22, '1589840836102.jpg'),
(7, 14, '1719217506808.png'),
(8, 14, '1719217671786.png'),
(9, 14, '1719217739541.png'),
(10, 14, '1719217779261.png'),
(11, 13, '1719257012187.png'),
(12, 13, '1719257030322.png'),
(13, 13, '1719257072542.png'),
(14, 13, '1719257091024.png'),
(15, 12, '1719257360983.png'),
(16, 12, '1719257387290.png'),
(17, 12, '1719257400022.png'),
(18, 12, '1719257412279.png'),
(19, 16, '1719258010064.png'),
(20, 16, '1719258030882.png'),
(21, 16, '1719258044035.png'),
(22, 16, '1719258060653.png'),
(23, 17, '1719258312670.png'),
(24, 17, '1719258321325.png'),
(25, 17, '1719258336188.png'),
(26, 17, '1719258346360.png'),
(27, 18, '1719258711875.png'),
(28, 18, '1719258725533.png'),
(29, 18, '1719258741668.png'),
(30, 18, '1719258755427.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invoice_code` varchar(10) NOT NULL,
  `label` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telp` varchar(20) NOT NULL,
  `province` int(11) NOT NULL,
  `regency` int(11) NOT NULL,
  `district` varchar(50) NOT NULL,
  `village` varchar(50) NOT NULL,
  `zipcode` int(11) NOT NULL,
  `address` text NOT NULL,
  `courier` varchar(5) NOT NULL,
  `courier_service` varchar(70) NOT NULL,
  `ongkir` varchar(10) NOT NULL,
  `total_price` int(11) NOT NULL,
  `total_all` int(11) NOT NULL,
  `date_input` datetime NOT NULL,
  `date_limit` datetime NOT NULL,
  `process` int(11) NOT NULL,
  `send` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `invoice`
--

INSERT INTO `invoice` (`id`, `invoice_code`, `label`, `name`, `email`, `telp`, `province`, `regency`, `district`, `village`, `zipcode`, `address`, `courier`, `courier_service`, `ongkir`, `total_price`, `total_all`, `date_input`, `date_limit`, `process`, `send`) VALUES
(4, '235174', 'true', 'Must', '', '081297098900', 11, 42, 'Siliragung ', 'Kesilir', 68488, 'Jl. Mangga, No.3 RT.5/RW.3, Dusun. Silirsari, Desa.Kesilir Kec.Siliragung Kab.Banyuwangi Prov. Jawa Timur', 'jne', 'CTC', '6000', 20000, 26000, '2024-06-20 01:53:55', '2024-06-22 01:53:55', 1, 1),
(5, '776743', 'true', 'M', '', '0812970989000', 11, 42, 'Siliragung ', 'Kesilir ', 68488, 'Jl. Mangga, No.3 RT.5/RW.3, Dusun. Silirsari, Desa.Kesilir Kec.Siliragung Kab.Banyuwangi Prov. Jawa Timur.Kode Pos', 'jne', 'CTC', '6000', 25000, 31000, '2024-06-20 09:16:16', '2024-06-22 09:16:16', 1, 1),
(6, '757362', 'true', 'Babsvd', '', '0819', 11, 42, 'Rogojampi', 'Rogojampi', 68465, 'Jl. Raden Supono, Pondok Pesantren Darussalam Blokagung 2, Setail, Kec. Genteng, Kabupaten Banyuwangi, Jawa Timur 68465, Indonesia', 'jne', 'JTR', '30000', 100000, 130000, '2024-07-03 20:02:37', '2024-07-05 20:02:37', 1, 1),
(7, '804990', 'true', 'iqbal rahayuddin', '', '081234567890', 11, 42, 'kabat', 'gombolirang', 6666, 'Jl. Antogan rt. 01 rw. 01, krajan, gombolirang, kabat', 'antar', 'antar', '20000', 25000, 45000, '2024-12-25 07:50:04', '2024-12-27 07:50:04', 0, 0),
(8, '504133', 'true', 'iqbal rahayudin', '', '081234567890', 11, 42, 'kabat', 'gombolirang', 111111, 'Jl. Antogan rt. 01 rw. 01, krajan, gombolirang, kabat', 'antar', 'antar', '20000', 25000, 45000, '2024-12-25 08:01:44', '2024-12-27 08:01:44', 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `banner` varchar(30) NOT NULL,
  `link` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `package`
--

CREATE TABLE `package` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `banner` varchar(30) NOT NULL,
  `slug` varchar(110) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `package`
--

INSERT INTO `package` (`id`, `title`, `banner`, `slug`) VALUES
(17, 'LAYANAN ', '1720110080309.png', 'layanan'),
(22, 'PELUANG BINIS DAN KERJASAMA', '1722029277123.png', 'peluang-binis-dan-kerjasama'),
(23, 'SPESIAL PROMO', '1722029333501.png', 'spesial-promo');

-- --------------------------------------------------------

--
-- Struktur dari tabel `package_product`
--

CREATE TABLE `package_product` (
  `id` int(11) NOT NULL,
  `package` int(11) NOT NULL,
  `product` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `slug` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `pages`
--

INSERT INTO `pages` (`id`, `title`, `content`, `slug`) VALUES
(1, 'Tentang Kami', '<h3><strong>Tentang Kami</strong></h3><p><strong>Selamat Datang di HamungHello.com&nbsp;</strong></p><p><strong>HamungHello.com</strong> adalah destinasi belanja online yang menawarkan berbagai produk dan layanan yang memenuhi kebutuhan sehari-hari Anda. Kami menyajikan beragam kategori produk, mulai dari kebutuhan sehari-hari, elektronik, kerajinan tangan, fashion, hingga produk kesehatan. Kami juga menyediakan berbagai pelatihan dan edukasi online yang dirancang untuk memperkaya pengetahuan dan keterampilan Anda.</p><p>Kami bangga bekerja sama dengan para pelaku usaha UMKM, menjadikan HamungHello.com sebagai platform yang mendukung pertumbuhan dan keberhasilan bisnis lokal. Dengan mengusung tema kebersamaan dan kemaslahatan bersama, kami berkomitmen untuk memberikan pengalaman belanja yang tidak hanya memuaskan, tetapi juga bermanfaat bagi masyarakat luas.</p><p>Bergabunglah dengan kami di HamungHello.com dan nikmati kemudahan berbelanja sambil mendukung UMKM serta meraih pengetahuan dan keterampilan baru untuk masa depan yang lebih baik</p><h3><strong>Visi:</strong></h3><p>Menjadi destinasi belanja online terkemuka yang memberdayakan masyarakat dengan menyediakan produk berkualitas, layanan edukasi yang bermanfaat, serta mendukung pertumbuhan UMKM untuk menciptakan dampak positif yang luas bagi komunitas dan ekonomi lokal.</p><h3><strong>Misi:</strong></h3><ol><li><strong>Menyediakan Beragam Produk dan Layanan:</strong> Menawarkan berbagai kategori produk yang memenuhi kebutuhan sehari-hari, dari kebutuhan rumah tangga hingga elektronik, fashion, kerajinan tangan, dan kesehatan, dengan kualitas yang terpercaya.</li><li><strong>Memberikan Edukasi dan Pelatihan Berkualitas:</strong> Menyediakan pelatihan dan edukasi online yang dirancang untuk memperkaya pengetahuan dan keterampilan pengguna, membantu mereka mencapai potensi penuh dalam kehidupan pribadi dan profesional mereka.</li><li><strong>Mendukung UMKM dan Ekonomi Lokal:</strong> Bekerja sama dengan pelaku usaha UMKM untuk memperluas jangkauan pasar mereka, mendorong pertumbuhan bisnis lokal, dan menciptakan lapangan kerja serta peluang ekonomi baru.</li><li><strong>Menjamin Pengalaman Belanja yang Memuaskan:</strong> Mengutamakan kepuasan pelanggan melalui pengalaman belanja yang mudah, aman, dan memuaskan, dengan dukungan layanan pelanggan yang responsif dan profesional.</li><li><strong>Mengedepankan Kebersamaan dan Kemaslahatan:</strong> Mengusung tema kebersamaan dan kemaslahatan bersama untuk menciptakan nilai tambah yang bermanfaat bagi masyarakat luas serta membangun komunitas yang saling mendukung.</li></ol><h3><strong>Nilai-Nilai:</strong></h3><ol><li><strong>Integritas:</strong> Menjalankan bisnis dengan transparansi dan kejujuran, memastikan bahwa semua produk, layanan, dan interaksi dengan pelanggan memenuhi standar etika yang tinggi.</li><li><strong>Kualitas:</strong> Berkomitmen untuk menyediakan produk dan layanan yang berkualitas tinggi, memenuhi harapan pelanggan, dan mendorong perbaikan berkelanjutan.</li><li><strong>Inovasi:</strong> Selalu mencari cara baru dan kreatif untuk meningkatkan pengalaman belanja, memperluas penawaran produk, dan menyediakan solusi edukasi yang relevan.</li><li><strong>Kepedulian Sosial:</strong> Memprioritaskan dukungan terhadap UMKM dan pelaku usaha lokal, serta berkontribusi pada kesejahteraan masyarakat melalui berbagai inisiatif sosial dan pendidikan.</li><li><strong>Kolaborasi:</strong> Membangun hubungan yang kuat dengan pelanggan, mitra bisnis, dan komunitas untuk menciptakan sinergi yang menguntungkan semua pihak.</li><li><strong>Keberagaman:</strong> Menghargai dan merayakan keberagaman dalam produk, layanan, dan tenaga kerja, serta memastikan inklusivitas dalam semua aspek bisnis.</li></ol><p>Dengan visi, misi, dan nilai-nilai ini, HamungHello.com berkomitmen untuk menciptakan platform belanja online yang tidak hanya memudahkan kehidupan sehari-hari tetapi juga memberikan dampak positif yang luas bagi masyarakat.</p><h3><strong>Mengapa Memilih Berbelanja di HamungHello.com?</strong></h3><ol><li><strong>Beragam Pilihan Produk Berkualitas</strong><ul><li><strong>Kebutuhan Sehari-hari:</strong> Kami menyediakan berbagai kategori produk mulai dari kebutuhan rumah tangga hingga elektronik dan fashion, semua dengan kualitas yang dapat diandalkan.</li><li><strong>Produk Kesehatan dan Kerajinan Tangan:</strong> Dapatkan akses ke produk kesehatan yang berkualitas dan kerajinan tangan unik yang tidak hanya memenuhi kebutuhan tetapi juga menambah keindahan hidup Anda.</li></ul></li><li><strong>Edukasi dan Pelatihan yang Bermanfaat</strong><ul><li><strong>Pengembangan Keterampilan:</strong> Manfaatkan berbagai pelatihan dan edukasi online kami yang dirancang untuk memperkaya pengetahuan dan meningkatkan keterampilan Anda, baik untuk kebutuhan pribadi maupun profesional.</li><li><strong>Akses ke Pengetahuan:</strong> Dapatkan akses ke materi edukasi yang relevan dan terkini, membantu Anda meraih potensi penuh dan menghadapi tantangan dengan percaya diri.</li></ul></li><li><strong>Dukungan Terhadap UMKM dan Ekonomi Lokal</strong><ul><li><strong>Bantuan untuk Pelaku Usaha Lokal:</strong> Belanja di HamungHello.com berarti Anda turut berkontribusi pada pertumbuhan UMKM dan bisnis lokal, menciptakan lapangan kerja, dan memperkuat ekonomi komunitas Anda.</li><li><strong>Produk Lokal Berkualitas:</strong> Temukan produk-produk unik dari UMKM yang tidak hanya berkualitas tinggi tetapi juga mendukung perkembangan usaha kecil dan menengah di sekitar Anda.</li></ul></li><li><strong>Pengalaman Belanja yang Memuaskan dan Aman</strong><ul><li><strong>Kemudahan Belanja:</strong> Nikmati pengalaman berbelanja yang mudah, dengan antarmuka yang ramah pengguna dan proses transaksi yang cepat serta aman.</li><li><strong>Dukungan Pelanggan:</strong> Tim layanan pelanggan kami siap membantu Anda dengan solusi cepat dan profesional, memastikan setiap pengalaman berbelanja Anda menyenangkan dan tanpa hambatan.</li></ul></li><li><strong>Komitmen Terhadap Kebersamaan dan Kemaslahatan</strong><ul><li><strong>Masyarakat yang Lebih Baik:</strong> Dengan berbelanja di HamungHello.com, Anda bergabung dengan kami dalam misi untuk menciptakan dampak positif yang luas bagi masyarakat, melalui berbagai inisiatif sosial dan komunitas.</li><li><strong>Kebersamaan dan Solidaritas:</strong> Kami percaya dalam kekuatan kebersamaan dan kemaslahatan bersama, dan kami berusaha untuk menciptakan nilai tambah yang bermanfaat bagi semua pihak yang terlibat.</li></ul></li><li><strong>Nilai-Nilai Perusahaan yang Kuat</strong><ul><li><strong>Integritas dan Transparansi:</strong> Kami berkomitmen untuk menjalankan bisnis dengan integritas dan transparansi, memastikan bahwa setiap produk dan layanan memenuhi standar etika yang tinggi.</li><li><strong>Inovasi dan Keberagaman:</strong> Kami terus berinovasi untuk menghadirkan produk dan layanan terbaru serta merayakan keberagaman dalam semua aspek bisnis kami.</li></ul></li></ol><h3><strong>Hubungi Kami</strong></h3><p>Kami selalu senang mendengar dari Anda. Jika Anda memiliki pertanyaan, saran, atau keluhan, jangan ragu untuk menghubungi kami melalui:</p><ul><li><strong>Email:</strong> hamunghello@gmail.com</li><li><strong>Telepon:</strong> 0823-3897-9723</li><li><strong>Alamat:</strong> Jl. Mangga Rt.05 RW.03, Dusun Silirsari Kel. Kesilir Kec. Siliragung Kab. Banyuwangi Prov. Jawa Timur Indonesia</li></ul><p>Dengan memilih HamungHello.com, Anda tidak hanya memenuhi kebutuhan sehari-hari Anda, tetapi juga berpartisipasi dalam misi kami untuk mendukung UMKM, memperkaya pengetahuan, dan menciptakan dampak positif bagi masyarakat luas. Bergabunglah dengan kami dan rasakan manfaatnya!</p>', 'about'),
(2, 'Kontak Kami', '<h3>Kontak Kami</h3><p><strong>Kami di Hamung Hello.com selalu siap membantu Anda!</strong></p><p>Jika Anda memiliki pertanyaan, saran, atau membutuhkan bantuan, jangan ragu untuk menghubungi kami melalui salah satu cara berikut:</p><h4>Informasi Kontak</h4><ul><li><strong>Email:</strong> hamunghello@gmail.com</li><li><strong>Telepon:</strong> 0823-3897-9723</li><li><strong>Alamat:</strong> Jl. Mangga Rt.05 Rw.03 Dusun Silirsari, Desa Kesilir Kec.Siliragung Kab.Banyuwangi Prov. Jawa Timur Indonesia&nbsp;<br>&nbsp;</li></ul><h4>Jam Operasional</h4><ul><li><strong>Senin - Jumat:</strong> 09:00 - 20:00</li><li><strong>Sabtu:</strong> 09:00 - 20:00</li><li><strong>Minggu &amp; Hari Libur:</strong> Tutup</li></ul><h4>Formulir Kontak</h4><p>Anda juga dapat menghubungi kami dengan mengisi formulir di bawah ini. Kami akan merespons pesan Anda secepat mungkin.</p><p><strong>Nama:</strong> [_____________________]</p><p><strong>Email:</strong> [_____________________]</p><p><strong>Telepon:</strong> [_____________________]</p><p><strong>Pesan:</strong> [____________________________________]</p><p>[<strong>Kirim</strong>]</p><h4>Media Sosial</h4><p>Ikuti kami di media sosial untuk mendapatkan informasi terbaru, promo, dan penawaran menarik:</p><ul><li><strong>Facebook:</strong> <a href=\"http://facebook.com/hamungheulloholshop\">Hamung Heulloh Mart</a></li><li><strong>Instagram:</strong> <a href=\"http://instagram.com/hamungheulloholshop\">@hamungheullohomart</a></li><li><strong>Twitter:</strong> <a href=\"http://twitter.com/hamungheulloh\">@hamungheullohmart</a></li></ul><h4>Lokasi Kami</h4><p>Anda dapat mengunjungi kami di alamat berikut:</p><p><strong>Hamung Hello.com </strong>Jl. Mangga Rt.05 Rw.03 Dusun Silirsari, Desa Kesilir Kec.Siliragung Kab.Banyuwangi Prov. Jawa Timur Indonesia&nbsp;<br>&nbsp;</p><p><strong>Peta Lokasi:</strong> [Insert Google Maps iframe or image here]</p>', 'contact'),
(3, 'Testimoni', '<p>redirect page</p>', 'testimoni'),
(4, 'Kebijakan Privasi', '<h3>Kebijakan Privasi</h3><p><strong>Hamung Hello.com </strong>sangat menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi Anda. Halaman ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.</p><h4>1. Informasi yang Kami Kumpulkan</h4><p>Kami mengumpulkan berbagai jenis informasi untuk memberikan dan meningkatkan layanan kami kepada Anda, termasuk:</p><ul><li><strong>Informasi Identifikasi Pribadi:</strong> Nama, alamat email, nomor telepon, alamat pengiriman, dan informasi lainnya yang Anda berikan saat membuat akun atau melakukan pembelian.</li><li><strong>Informasi Transaksi:</strong> Rincian pembelian, seperti produk yang dibeli, jumlah transaksi, dan metode pembayaran.</li><li><strong>Informasi Teknis:</strong> Alamat IP, jenis perangkat, data cookie, dan aktivitas penjelajahan di situs kami.</li></ul><h4>2. Penggunaan Informasi</h4><p>Informasi yang kami kumpulkan digunakan untuk:</p><ul><li>Memproses dan mengirimkan pesanan Anda.</li><li>Mengelola akun Anda dan memberikan layanan pelanggan.</li><li>Mengirimkan pembaruan, penawaran, dan promosi yang relevan.</li><li>Meningkatkan pengalaman berbelanja Anda dengan analisis data dan umpan balik.</li></ul><h4>3. Perlindungan Data</h4><p>Kami mengambil langkah-langkah keamanan yang wajar untuk melindungi informasi pribadi Anda dari akses yang tidak sah, perubahan, pengungkapan, atau penghancuran. Ini termasuk:</p><ul><li><strong>Enkripsi:</strong> Data sensitif dienkripsi selama transmisi menggunakan teknologi SSL.</li><li><strong>Kontrol Akses:</strong> Hanya karyawan yang membutuhkan informasi untuk melakukan pekerjaan tertentu yang diberikan akses ke informasi pribadi.</li><li><strong>Pemantauan dan Audit:</strong> Sistem kami secara rutin dipantau dan diaudit untuk memastikan kepatuhan terhadap kebijakan keamanan.</li></ul><h4>4. Pengungkapan kepada Pihak Ketiga</h4><p>Kami tidak akan menjual, menyewakan, atau menukar informasi pribadi Anda dengan pihak ketiga. Namun, kami dapat berbagi informasi dengan:</p><ul><li><strong>Penyedia Layanan:</strong> Pihak ketiga yang membantu kami dalam pengoperasian situs web kami, seperti layanan pengiriman, pemrosesan pembayaran, dan pemasaran.</li><li><strong>Persyaratan Hukum:</strong> Jika diperlukan oleh hukum atau dalam situasi darurat untuk melindungi keselamatan publik.</li></ul><h4>5. Hak Anda</h4><p>Anda memiliki hak untuk:</p><ul><li>Mengakses informasi pribadi yang kami miliki tentang Anda.</li><li>Meminta perbaikan atau penghapusan informasi pribadi Anda.</li><li>Menolak pemrosesan informasi pribadi Anda dalam kondisi tertentu.</li><li>Menarik persetujuan yang telah Anda berikan sebelumnya.</li></ul><p>Untuk menggunakan hak Anda, silakan hubungi kami melalui informasi kontak yang diberikan di bawah.</p><h4>6. Cookie dan Teknologi Serupa</h4><p>Kami menggunakan cookie dan teknologi serupa untuk mengumpulkan informasi tentang interaksi Anda dengan situs kami untuk meningkatkan pengalaman pengguna. Anda dapat mengatur browser Anda untuk menolak cookie, tetapi ini mungkin mempengaruhi fungsi situs kami.</p><h4>7. Pembaruan Kebijakan Privasi</h4><p>Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Setiap perubahan akan diposting di halaman ini dengan tanggal pembaruan. Kami mendorong Anda untuk meninjau kebijakan ini secara berkala.</p><h4>8. Hubungi Kami</h4><p>Jika Anda memiliki pertanyaan atau kekhawatiran tentang kebijakan privasi kami, silakan hubungi kami di:</p><ul><li><strong>Email:</strong> hamunghello@gmail.com</li><li><strong>Telepon:&nbsp;</strong> 0823-3897-9723</li><li><strong>Alamat: </strong>Jl.Mangga, RT.5/RW.3, Dusun. Silirsari, Desa.Kesilir Kec.Siliragung Kab.Banyuwangi Prov. Jawa Timur Kode Pos 68488</li></ul><p>Terima kasih telah mempercayakan Hamung Hello.com &nbsp;dengan informasi pribadi Anda. Kami berkomitmen untuk melindungi privasi Anda dan memberikan layanan terbaik.</p>', 'privacy-policy'),
(5, 'Syarat dan Ketentuan', '<h3>Syarat dan Ketentuan</h3><p><strong>Selamat datang di Hamung Hello.com!</strong> Dengan mengakses dan menggunakan situs web kami, Anda setuju untuk mematuhi dan terikat oleh syarat dan ketentuan berikut. Harap baca syarat dan ketentuan ini dengan cermat sebelum menggunakan layanan kami.</p><h4>1. Umum</h4><p>1.1. Dengan mengakses situs web kami dan melakukan pembelian, Anda setuju untuk terikat oleh syarat dan ketentuan ini serta kebijakan privasi kami.</p><p>1.2. Hamung Hello.com berhak mengubah syarat dan ketentuan ini kapan saja. Perubahan akan berlaku segera setelah diposting di situs web kami. Anda disarankan untuk memeriksa halaman ini secara berkala untuk mengetahui pembaruan terbaru.</p><h4>2. Penggunaan Situs</h4><p>2.1. Anda setuju untuk menggunakan situs kami hanya untuk tujuan yang sah dan tidak melanggar hukum yang berlaku.</p><p>2.2. Anda dilarang mengunggah, mendistribusikan, atau mempublikasikan konten apa pun yang mengandung virus, malware, atau kode berbahaya lainnya yang dapat merusak atau mengganggu operasi situs kami.</p><p>2.3. Anda bertanggung jawab atas informasi yang Anda berikan saat membuat akun dan melakukan pembelian. Informasi yang diberikan harus akurat, lengkap, dan terkini.</p><h4>3. Pembelian dan Pembayaran</h4><p>3.1. Semua harga yang tertera di situs kami adalah dalam mata uang lokal dan termasuk pajak kecuali dinyatakan lain.</p><p>3.2. Kami menerima berbagai metode pembayaran yang tertera di situs kami. Pembayaran harus diselesaikan sebelum pesanan diproses dan dikirim.</p><p>3.3. Setelah melakukan pemesanan, Anda akan menerima konfirmasi melalui email. Jika ada masalah dengan pesanan Anda, kami akan menghubungi Anda sesegera mungkin.</p><h4>4. Pengiriman</h4><p>4.1. Kami menyediakan berbagai opsi pengiriman, yang dapat dipilih saat checkout. Biaya dan waktu pengiriman akan bervariasi berdasarkan lokasi dan metode pengiriman yang dipilih.</p><p>4.2. Hamung Hello.com tidak bertanggung jawab atas keterlambatan pengiriman yang disebabkan oleh keadaan di luar kendali kami, termasuk tetapi tidak terbatas pada bencana alam, gangguan transportasi, atau kejadian tak terduga lainnya.</p><h4>5. Pengembalian dan Pengembalian Dana</h4><p>5.1. Kami menerima pengembalian barang dalam jangka waktu tertentu sesuai dengan kebijakan pengembalian kami. Barang harus dalam kondisi asli dan tidak terpakai.</p><p>5.2. Untuk mengajukan pengembalian, Anda harus menghubungi layanan pelanggan kami dan mengikuti prosedur yang diberikan.</p><p>5.3. Pengembalian dana akan diproses setelah kami menerima dan memeriksa barang yang dikembalikan. Pengembalian dana akan dikreditkan ke metode pembayaran asli Anda.</p><h4>6. Hak Kekayaan Intelektual</h4><p>6.1. Semua konten yang tersedia di situs kami, termasuk tetapi tidak terbatas pada teks, grafik, logo, dan gambar, adalah milik Hamung Hello.com atau pemasok konten kami dan dilindungi oleh undang-undang hak cipta.</p><p>6.2. Anda tidak diperbolehkan menggunakan, mereproduksi, atau mendistribusikan konten kami tanpa izin tertulis dari kami.</p><h4>7. Tanggung Jawab dan Pembatasan Tanggung Jawab</h4><p>7.1. Hamung Hello.com tidak bertanggung jawab atas kerugian atau kerusakan yang timbul dari penggunaan situs kami atau produk yang dibeli melalui situs kami, kecuali jika diwajibkan oleh hukum.</p><p>7.2. Kami berusaha untuk memastikan bahwa informasi di situs kami akurat dan terkini, tetapi kami tidak menjamin bahwa konten situs bebas dari kesalahan atau kelalaian.</p><h4>8. Hukum yang Berlaku</h4><p>8.1. Syarat dan ketentuan ini diatur oleh dan ditafsirkan sesuai dengan hukum negara tempat Hamung Hello.com beroperasi.</p><p>8.2. Setiap sengketa yang timbul dari atau terkait dengan penggunaan situs kami atau pembelian produk kami akan diselesaikan melalui pengadilan yang berwenang di yurisdiksi kami.</p><h4>9. Hubungi Kami</h4><p>Jika Anda memiliki pertanyaan tentang syarat dan ketentuan ini, silakan hubungi kami di:</p><ul><li><strong>Email:</strong> info@hamunghello.com</li><li><strong>Telepon:</strong> 0823-3897-9723</li><li><strong>Alamat:</strong> Jl. Mangga, RT.5/RW.3, Dusun. Silirsari, Desa.Kesilir Kec.Siliragung Kab.Banyuwangi Prov. Jawa Timur Kode Pos 68488</li></ul>', 'terms'),
(6, 'Cara Berbelanja', '<p>Anda bisa mengklik “Blanja sekarang” di blanja.com untuk membeli produk, atau Anda bisa menambahkan produk ke Favorit dahulu lalu menempatkan pesanan.</p><p><strong>1. Blanja sekarang</strong></p><p>1.1 Jika Anda ingin membeli produk langsung ketika Anda melihatnya di Product Detail Page (gambar di bawah), Anda bisa mengklik “Blanja sekarang” setelah Anda memilih atribut, jumlah, dll. dari produk tersebut.</p><p>&nbsp;</p><p>1.2 Setelah Anda mengkonfirmasi alamat pengiriman, informasi pesanan dan informasi lainnya, klik “Selanjutnya”.</p><p>&nbsp;</p><p>1.3 Anda bisa masuk ke “My blanja”-“Pesanan Saya” dan melihat pesanan yang telah ditempatkan. Jika Anda sudah mengkonfirmasi jumlah dari pesanan tersebut, klik “Bayar”.</p><p>&nbsp;</p><p>1.4 Masuk ke halaman pembayaran dan bayarkan pesanan. Status pemesanan akan berubah menjadi “Telah dibayar”, yang artinya barang sedang menunggu dikirim oleh penjual.</p><p>&nbsp;</p><p>1.5 Setelah penjual berhasil mengirimkan barang, status pemesanan akan berubah menjadi “Telah dikirim”. Ketika anda menerima barang dan mengkonfirmasi, mohon klik “Konfirmasi”.</p><p>&nbsp;</p><p>Anda harus memasukkan password Dompet Blanja sebelum mengklik “Konfirmasi”.</p><p>&nbsp;</p><p>1.6 Ketika status berubah ke \"Selesai\", maka berarti transaksi telah selesai</p><p>&nbsp;</p><p><strong>Checkout beberapa produk yang telah ditambahkan ke Troli blanja secara bersamaan</strong></p><p>Anda bisa menambahkan beberapa produk ke Troli blanja dan membelinya secara bersamaan, lalu menempatkan pesanan dan membayar sekali sekaligus. Prosesnya sama seperti proses “Blanja sekarang”.</p>', 'shopping-help'),
(7, 'Pengiriman Barang', '<ol><li>Pengiriman barang untuk setiap transaksi yang terjadi melalui Platform Hamung Hello.com &nbsp;menggunakan layanan jasa ekspedisi pengiriman barang resmi dan terpercaya.</li><li>Pengguna memahami dan menyetujui bahwa segala bentuk peraturan terkait dengan syarat dan ketentuan pengiriman barang sepenuhnya ditentukan oleh pihak jasa ekspedisi pengiriman barang dan sepenuhnya menjadi tanggung jawab pihak jasa ekspedisi pengiriman barang.</li><li>Hamung Hello.com hanya berperan sebagai media perantara antara Pengguna dengan pihak jasa ekspedisi pengiriman barang.</li><li>Pengguna wajib memahami, menyetujui, serta mengikuti ketentuan-ketentuan pengiriman barang yang telah dibuat oleh pihak jasa ekspedisi pengiriman barang.</li><li>Pengiriman barang atas transaksi melalui sistem jasa ekspedisi pengiriman barang ini dilakukan dengan tujuan agar barang dapat dipantau oleh pengguna secara langsung.</li><li>Kami akan bertanggung jawab penuh atas barang yang dikirimnya dengan syarat dan ketentuan yang telah di sepakati dan bukan kekeliruan dari pelayanan pihak kami</li><li>Pengguna memahami dan menyetujui bahwa segala bentuk kerugian yang dapat timbul akibat kerusakan ataupun kehilangan pada barang, baik pada saat pengiriman barang tengah berlangsung maupun pada saat pengiriman barang telah selesai, adalah sepenuhnya tanggung jawab pihak jasa ekspedisi pengiriman barang.</li><li>Dalam hal diperlukan untuk dilakukan proses pengembalian barang, maka Pembeli, diwajibkan untuk melakukan pengiriman barang langsung kepihak kami sebagai penjual dengan beban biaya pengiriman barang pengembalian tersebut di tanggung oleh pihak pembeli begitu juga dengan biaya pengembalian ulang ke pada pihak pembeli.</li></ol>', 'pengiriman-barang');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `condit` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `img` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `date_submit` datetime NOT NULL,
  `publish` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `transaction` int(11) NOT NULL,
  `promo_price` int(11) NOT NULL,
  `viewer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `title`, `price`, `stock`, `category`, `condit`, `weight`, `img`, `description`, `date_submit`, `publish`, `slug`, `transaction`, `promo_price`, `viewer`) VALUES
(19, 'Deo Shry Harum - Paper mint Essential Oil 100ml', 25000, 100, 28, 1, 100, '1721939423503.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Peppermint Essential Oil&nbsp;</strong></p><p>Mengandalkan Peppermint Essential Oil, Deo Shry Harum memberikan kesegaran yang menyegarkan sekaligus perlindungan efektif dari bau badan. Peppermint dikenal memiliki sifat antiinflamasi dan astringen alami yang membantu mengontrol produksi keringat dan menjaga kulit tetap segar. Produk ini juga menghadirkan aroma yang menyegarkan serta memberikan peremajaan kulit yang menyegarkan.</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben: </strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit: </strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan: </strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan: </strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif: </strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p><h4>&nbsp;</h4>', '2024-07-26 03:12:51', 1, 'deo-shry-harum-paper-mint-essential-oil-100ml', 0, 0, 206),
(20, 'Deo Shry Harum - Citronella Essential Oil 60ml', 15000, 100, 28, 1, 60, '1721938877031.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Citronella Essential Oil</strong></p><p>Dengan campuran unik dari Citronella Essential Oil, Deo Shry Harum menawarkan perlindungan yang kuat dan perawatan intensif untuk kulit Anda. Citronella dikenal memiliki sifat antibakteri, anti jamur, antioksidan, dan anti inflamasi yang membantu menjaga kebersihan dan kesehatan kulit. Ekstrak bengkuang yang terkandung dalam produk ini memberikan efek mencerahkan dan melembapkan secara alami, menjadikannya pilihan ideal untuk mereka yang mencari kesegaran alami dan perlindungan yang tahan lama.</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben: </strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit: </strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan: </strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan: </strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif: </strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 03:21:17', 1, 'deo-shry-harum-citronella-essential-oil-60ml', 0, 0, 192),
(21, 'Deo Shry Harum - Citronella Essential Oil 100ml', 25000, 100, 28, 1, 100, '1721939616929.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Citronella Essential Oil</strong></p><p>Dengan campuran unik dari Citronella Essential Oil, Deo Shry Harum menawarkan perlindungan yang kuat dan perawatan intensif untuk kulit Anda. Citronella dikenal memiliki sifat antibakteri, anti jamur, antioksidan, dan anti inflamasi yang membantu menjaga kebersihan dan kesehatan kulit. Ekstrak bengkuang yang terkandung dalam produk ini memberikan efek mencerahkan dan melembapkan secara alami, menjadikannya pilihan ideal untuk mereka yang mencari kesegaran alami dan perlindungan yang tahan lama.</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben: </strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit: </strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan: </strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan: </strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif: </strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 03:24:20', 1, 'deo-shry-harum-citronella-essential-oil-100ml', 0, 0, 200),
(22, 'Deo Shry Harum - Peppermint Essential Oil 60ml', 15000, 100, 28, 1, 60, '1721940315700.png', '<p><strong>Deo Shry Harum - Peppermint Essential Oil&nbsp;</strong></p><p>Mengandalkan Peppermint Essential Oil, Deo Shry Harum memberikan kesegaran yang menyegarkan sekaligus perlindungan efektif dari bau badan. Peppermint dikenal memiliki sifat anti inflamasi dan astringen alami yang membantu mengontrol produksi keringat dan menjaga kulit tetap segar. Produk ini juga menghadirkan aroma yang menyegarkan serta memberikan peremajaan kulit yang menyegarkan.</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 03:45:15', 1, 'deo-shry-harum-peppermint-essential-oil-60ml', 0, 0, 206),
(24, 'Deo Shry Harum - Blue Emotion 100ml', 30000, 100, 28, 1, 100, '1721940788536.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Blue Emotion: Kelembutan dan Aroma Menyegarkan</strong></p><p>Blue Emotion dari Deo Shry Harum menghadirkan kombinasi unik dari kesegaran dan aroma yang menyegarkan. Diformulasikan dengan bahan-bahan alami yang memberikan kesegaran sekaligus meremajakan kulit Anda, produk ini menghadirkan aroma yang sporty dan menyegarkan</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 03:53:08', 1, 'deo-shry-harum-blue-emotion-100ml', 0, 0, 191),
(25, 'Deo Shry Harum - Blue Emotion 60ml', 18000, 100, 28, 1, 60, '1721940945648.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Blue Emotion: Kelembutan dan Aroma Menyegarkan</strong></p><p>Blue Emotion dari Deo Shry Harum menghadirkan kombinasi unik dari kesegaran dan aroma yang menyegarkan. Diformulasikan dengan bahan-bahan alami yang memberikan kesegaran sekaligus meremajakan kulit Anda, produk ini menghadirkan aroma yang sporty dan menyegarkan</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 03:55:45', 1, 'deo-shry-harum-blue-emotion-60ml', 0, 0, 200),
(26, 'Deo Shry Harum - Baby Fragrance 100ml', 28000, 100, 28, 1, 100, '1721941240372.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Baby Fragrance “Kelembutan dan Aroma Lembut”</strong></p><p>Deo Shry Harum Baby Fragrance dirancang khusus untuk memberikan kelembutan ekstra pada kulit Anda. Dengan aroma yang lembut dan formulasi yang terjamin, produk ini menjaga ketiak tetap nyaman sepanjang hari<strong>.</strong></p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 04:00:40', 1, 'deo-shry-harum-baby-fragrance-100ml', 0, 0, 179),
(27, 'Deo Shry Harum - Baby Fragrance 60ml', 18000, 100, 28, 1, 60, '1721941451422.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Baby Fragrance “Kelembutan dan Aroma Lembut”</strong></p><p>Deo Shry Harum Baby Fragrance dirancang khusus untuk memberikan kelembutan ekstra pada kulit Anda. Dengan aroma yang lembut dan formulasi yang terjamin, produk ini menjaga ketiak tetap nyaman sepanjang hari<strong>.</strong></p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 04:04:11', 1, 'deo-shry-harum-baby-fragrance-60ml', 0, 0, 189),
(30, 'Deo Shry Harum - Lemon Mint 100ml', 30000, 100, 28, 1, 100, '1721941804455.png', '<p><strong>&nbsp;Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Lemon Mint “Kelembutan dan kesegaran Kulit”</strong></p><p>Lemon Mint dari Deo Shry Harum menggabungkan kelembutan lemon dengan kesegaran mint untuk memberikan perawatan kulit yang menyegarkan dan mencerahkan. Diformulasikan untuk memberikan kesegaran kulit.</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 04:10:04', 1, 'deo-shry-harum-lemon-mint-100ml', 0, 0, 202),
(31, 'Deo Shry Harum - Lemon Mint 60ml', 18000, 100, 28, 1, 60, '1721941963520.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Lemon Mint “Kelembutan dan kesegaran Kulit”</strong></p><p>Lemon Mint dari Deo Shry Harum menggabungkan kelembutan lemon dengan kesegaran mint untuk memberikan perawatan kulit yang menyegarkan dan mencerahkan. Diformulasikan untuk memberikan kesegaran kulit.</p><p><strong>Nilai Utama:</strong></p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 04:12:43', 1, 'deo-shry-harum-lemon-mint-60ml', 0, 0, 203),
(32, 'Deo Shry Harum - Floral Fragrance 100ml', 30000, 100, 28, 1, 100, '1721942224262.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Floral Fragrance “Aroma Bunga yang Menyegarkan”</strong></p><p>Floral Fragrance dari Deo Shry Harum memberikan sensasi menyegarkan dengan aroma bunga yang khas. Dengan campuran bahan alami, produk ini tidak hanya memberikan perlindungan terhadap bau badan tetapi juga memberikan aroma manis menyegarkan.Nilai Utama:</p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 04:17:04', 1, 'deo-shry-harum-floral-fragrance-100ml', 0, 0, 181),
(33, 'Deo Shry Harum - Floral Fragrance 60ml', 18000, 100, 28, 1, 100, '1721942330455.png', '<p><strong>Deo Shry Harum - Deodoran Alami dengan Rempah-rempah dan Ekstrak Bengkuang</strong></p><p>Tingkatkan kepercayaan diri Anda sepanjang hari dengan Deo Shry Harum, deodoran berbahan dasar tawas yang dihasilkan dari perpaduan rempah-rempah alami dan ekstrak bengkuang. Dirancang khusus tanpa kandungan alkohol dan paraben, produk ini tidak hanya efektif melawan bau badan tetapi juga aman untuk semua jenis kulit, termasuk kulit sensitif.</p><p><strong>Deo Shry Harum - Floral Fragrance “Aroma Bunga yang Menyegarkan”</strong></p><p>Floral Fragrance dari Deo Shry Harum memberikan sensasi menyegarkan dengan aroma bunga yang khas. Dengan campuran bahan alami, produk ini tidak hanya memberikan perlindungan terhadap bau badan tetapi juga memberikan aroma manis menyegarkan.Nilai Utama:</p><ol><li><strong>Formula Alami</strong>: Mengandung tawas, rempah-rempah, dan ekstrak bengkuang alami untuk memberikan perlindungan maksimal sepanjang hari.</li><li><strong>Tanpa Alkohol dan Paraben:&nbsp;</strong>Diproses tanpa bahan kimia berbahaya, menjaga kesehatan kulit Anda.</li><li><strong>Ketahanan 24 Jam:</strong> Menjamin kesegaran yang berlangsung lama, cocok untuk gaya hidup aktif dan sibuk.</li><li><strong>Aman untuk Semua Jenis Kulit:&nbsp;</strong>Diformulasikan untuk memberikan kenyamanan dan perlindungan tanpa mengiritasi kulit.</li></ol><p><strong>Kenapa Memilih Deo Shry Harum:</strong></p><ol><li><strong>Kelembutan dan Keamanan:&nbsp;</strong>Menggunakan bahan-bahan alami terbaik untuk melindungi kulit Anda dengan lembut.</li><li><strong>Aroma Rempah-rempah yang Menyegarkan:&nbsp;</strong>Memberikan aroma harum yang segar dan menenangkan sepanjang hari.</li><li><strong>Perlindungan Efektif:&nbsp;</strong>Teruji secara klinis untuk mengendalikan bau badan dengan efektif, tanpa meninggalkan residu berlebih.</li></ol><p><strong>Bergabunglah dengan ribuan orang yang memilih Deo Shry Harum untuk pengalaman kesegaran yang alami dan perlindungan maksimal tanpa kompromi. Dapatkan kepercayaan diri sepanjang hari dengan Deo Shry Harum!</strong></p>', '2024-07-26 04:18:50', 1, 'deo-shry-harum-floral-fragrance-60ml', 0, 0, 181);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekening`
--

CREATE TABLE `rekening` (
  `id` int(11) NOT NULL,
  `rekening` varchar(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `rekening`
--

INSERT INTO `rekening` (`id`, `rekening`, `name`, `number`) VALUES
(1, 'BRI', 'MUSTAQIM ', '0906 01 054486 535');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `promo` int(11) NOT NULL,
  `promo_time` varchar(40) NOT NULL,
  `short_desc` text NOT NULL,
  `address` varchar(100) NOT NULL,
  `regency_id` int(11) NOT NULL,
  `verify` int(11) NOT NULL,
  `logo` varchar(30) NOT NULL,
  `favicon` varchar(30) NOT NULL,
  `ongkir` int(1) NOT NULL,
  `default_ongkir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `promo`, `promo_time`, `short_desc`, `address`, `regency_id`, `verify`, `logo`, `favicon`, `ongkir`, `default_ongkir`) VALUES
(1, 0, '2022-06-10T12:26', 'HamungHello.com merupakan destinasi belanja online yang tidak hanya  menawarkan berbagai produk dan layanan yang memenuhi kebutuhan sehari-hari Anda, tetapi juga berpartisipasi dalam misi kami untuk mendukung UMKM, memperkaya pengetahuan, dan menciptakan dampak positif bagi masyarakat luas. Bergabunglah dengan kami dan rasakan manfaatnya!', 'Jl.Mangga Rt.05 Rw.03 Dusun Silirsari, Desa Kesilir Kec.Siliragung Kab.Banyuwangi - Jawa Timur ', 42, 1, '1719217972077.png', '1719225646757.png', 1, 20000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sosmed`
--

CREATE TABLE `sosmed` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `icon` varchar(20) NOT NULL,
  `link` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `sosmed`
--

INSERT INTO `sosmed` (`id`, `name`, `icon`, `link`) VALUES
(1, 'Facebook', 'facebook-f', 'https://www.facebook.com/share/wBYJwtk7NBeYbScY/?m'),
(4, 'Blog', 'blogger', 'https://www.blogger.com/?hl=en&tab=jj&authuser=0'),
(5, 'Instagram', 'instagram', 'https://www.instagram.com/hamunghello?igsh=dmE5cnl'),
(6, 'Youtube', 'youtube', 'https://youtube.com/@hamunghello?si=Xt8AwGq4J4DFgC'),
(7, 'Email', 'tiktok', 'https://www.tiktok.com/@hamung.hello?_t=8oNPvoYMIW');

-- --------------------------------------------------------

--
-- Struktur dari tabel `subscriber`
--

CREATE TABLE `subscriber` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `date_subs` datetime NOT NULL,
  `code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `subscriber`
--

INSERT INTO `subscriber` (`id`, `email`, `date_subs`, `code`) VALUES
(1, 'Semua Email', '2020-04-21 13:59:23', ''),
(2, '', '2021-06-17 04:13:00', '162387798079344206'),
(3, 'iqbalrahayuddin1@gmail.com', '2024-06-16 19:53:24', '17185424041428200780'),
(4, 'f4fh_generic_a88b7dcd_hamungheulloh.com@serviseant', '2024-08-05 20:43:20', '1722865400986802047'),
(5, 'vsfz_generic_a88b7dcd_hamungheulloh.com@serviseant', '2024-08-07 08:54:35', '1722995675806547948'),
(6, 'pEGg_generic_a88b7dcd_hamungheulloh.com@serviseant', '2024-08-07 10:22:00', '1723000920691295885'),
(7, 'aazjimseuz@solid-hamster.skin', '2024-08-16 09:15:04', '17237745041273351697'),
(8, 'aazjimszuz@solid-hamster.skin', '2024-08-16 09:15:04', '1723774504954258228'),
(9, 'aazjimseub@solid-hamster.skin', '2024-08-16 09:15:08', '1723774508856854475'),
(10, 'aazjimszub@solid-hamster.skin', '2024-08-16 09:15:10', '1723774510418282062'),
(11, 'aazjimseua@solid-hamster.skin', '2024-08-16 09:15:10', '17237745101665230211'),
(12, 'aazjimszua@solid-hamster.skin', '2024-08-16 09:15:12', '17237745121868719240'),
(13, 'aazjimseus@solid-hamster.skin', '2024-08-16 09:15:13', '172377451380716762'),
(14, 'aazjimszus@solid-hamster.skin', '2024-08-16 09:15:15', '17237745151114865777'),
(15, 'aazjimseul@solid-hamster.skin', '2024-08-16 09:15:16', '1723774516616845333'),
(16, 'aazjimseuzj@solid-hamster.skin', '2024-08-16 09:15:18', '1723774518315547940'),
(17, 'aazjimszul@solid-hamster.skin', '2024-08-16 09:15:19', '1723774519872316620'),
(18, 'aazjimseuze@solid-hamster.skin', '2024-08-16 09:15:21', '1723774521700560327'),
(19, 'aazjimszuzj@solid-hamster.skin', '2024-08-16 09:15:21', '1723774521284101017'),
(20, 'aazjimszuze@solid-hamster.skin', '2024-08-16 09:15:24', '1723774524706465017'),
(21, 'bmabbibibuz@dont-reply.me', '2024-09-25 08:56:35', '1727229395405790793'),
(22, 'bmabbibieuz@dont-reply.me', '2024-09-25 08:56:36', '1727229396415028361'),
(23, '5Usn_generic_a88b7dcd_hamungheulloh.com@serviseant', '2024-10-06 23:52:46', '17282335661729721975'),
(24, 'sarsrmbzluz@dont-reply.me', '2024-10-17 14:03:16', '17291485961594357548'),
(25, 'sarsrmbzmuz@dont-reply.me', '2024-10-17 14:03:16', '17291485961436856127'),
(26, 'sinderellacsa1980@gmail.com', '2024-10-19 16:56:00', '17293317601224452120'),
(27, 'prosperbrayjn4478@gmail.com', '2024-10-19 17:53:32', '1729335212216508286'),
(28, 'almirabeckerdu@gmail.com', '2024-10-23 20:39:35', '1729690775686678488'),
(29, 'zzzrzieesruz@dont-reply.me', '2024-10-26 00:13:01', '17298763811380872248'),
(30, 'zzzrzieesauz@dont-reply.me', '2024-10-26 00:13:01', '17298763811931382021'),
(31, 'denise_ozunajgea@outlook.com', '2024-10-31 13:54:07', '17303576471348337501'),
(32, 'zrsbasaebzuz@dont-reply.me', '2024-11-05 03:25:53', '17307519532092189610'),
(33, 'zrsbasaebjuz@dont-reply.me', '2024-11-05 03:25:53', '17307519531315591929'),
(34, 'fherebeorhtxu3304@gmail.com', '2024-11-07 03:53:59', '173092643921121284'),
(35, 'qqtwuiaiylthoqo@yahoo.com', '2024-11-07 04:15:45', '1730927745933897451'),
(36, 'hoodmimi33@gmail.com', '2024-11-08 14:54:32', '17310524721295739912'),
(37, 'middletonivorima@gmail.com', '2024-11-08 14:58:32', '1731052712718258189'),
(38, 'flecherkc2482@gmail.com', '2024-11-09 13:19:33', '17311331731571814384'),
(39, 'stormmb1984@gmail.com', '2024-11-10 07:09:30', '1731197370153809413'),
(40, 'gbndsfibbatyf@yahoo.com', '2024-11-10 07:11:40', '1731197500225984443'),
(41, 'baratspckard@yahoo.com', '2024-11-11 00:33:31', '1731260011346952902'),
(42, 'fwallerna692@gmail.com', '2024-11-11 00:34:11', '17312600511136144030'),
(43, 'paola.bucciarelli@yahoo.com', '2024-11-11 00:38:57', '173126033775660201'),
(44, 'harleiball836@gmail.com', '2024-11-11 00:42:22', '17312605421730317925'),
(45, 'fisherflerpz30@gmail.com', '2024-11-11 18:09:10', '17313233501259518046'),
(46, 'mytmuttart@yahoo.com', '2024-11-11 18:18:00', '1731323880447585207'),
(47, 'st.fiofab@yahoo.com', '2024-11-13 11:51:39', '17314734991717071318'),
(48, 'morristhm24@gmail.com', '2024-11-13 12:01:38', '17314740981792161552'),
(49, 'kgjt_a94534a0ebf0f8ede6434143fabf3738dda1661c@trus', '2024-11-14 03:05:53', '17315283532065325722'),
(50, 'zlbemllzliuz@dont-reply.me', '2024-11-14 07:55:29', '1731545729778853113'),
(51, 'zlbemllzlsuz@dont-reply.me', '2024-11-14 07:55:29', '17315457291035095642'),
(52, 'gaihuiab1986@gmail.com', '2024-11-14 09:42:05', '1731552125368792549'),
(53, 'kollilawsond@gmail.com', '2024-11-15 06:44:49', '17316278891145427834'),
(54, 'ooxeiav9xg755bq@yahoo.com', '2024-11-16 04:44:20', '1731707060548560078'),
(55, 'payneelsdonay@gmail.com', '2024-11-17 00:47:08', '1731779228127604201'),
(56, 'moussignacmcilhay@yahoo.com', '2024-11-17 00:50:38', '17317794381358645275'),
(57, 'dario.1955@yahoo.com', '2024-11-18 07:37:28', '1731890248124630277'),
(58, 'gklifford7290@gmail.com', '2024-11-18 07:49:47', '17318909871222247129'),
(59, 'olahannikuaien@yahoo.com', '2024-11-18 07:52:28', '1731891148331029508'),
(60, 'srgmhouy1hul9x@yahoo.com', '2024-11-19 21:08:02', '17320252821231304972'),
(61, 'ntcyevwud44uenke@yahoo.com', '2024-11-19 21:18:18', '17320258981468736534'),
(62, 'ezisamjrajuz@dont-reply.me', '2024-11-23 05:40:24', '17323152241183563712'),
(63, 'ezisilllajuz@dont-reply.me', '2024-11-23 05:45:16', '17323155161542577902'),
(64, 'kingheizel2002@gmail.com', '2024-11-23 07:15:10', '17323209107633222'),
(65, 'kimbrahensley@gmail.com', '2024-11-23 07:24:43', '1732321483694417191'),
(66, 'richobpu@gmail.com', '2024-11-24 12:42:52', '17324269722087950535'),
(67, 'xlvev0pduf5@yahoo.com', '2024-11-24 13:02:38', '1732428158493368441'),
(68, 'alestergalvano2006@gmail.com', '2024-11-25 09:19:27', '1732501167588596136'),
(69, 'prlatoekalich@yahoo.com', '2024-11-25 09:26:26', '1732501586668496823'),
(70, 'nooradeaalmario@yahoo.com', '2024-11-26 07:41:09', '1732581669974014465'),
(71, 'venonnaconwayd7774@gmail.com', '2024-11-26 07:43:22', '173258180286348845'),
(72, 'ugtaokpgarmwwix@yahoo.com', '2024-11-27 06:37:09', '17326642291219049629'),
(73, 'ehrlenbachabfaltr@yahoo.com', '2024-11-27 06:39:42', '1732664382781874101'),
(74, 'ebizjaaeeiuz@do-not-respond.me', '2024-11-28 01:52:33', '1732733553435637383'),
(75, 'vswgd0xonhyps@yahoo.com', '2024-11-28 04:50:03', '17327442031252946502'),
(76, 'k9vkwaaxv7@yahoo.com', '2024-11-28 04:59:20', '17327447601858341133'),
(77, 'burchdevnetd@gmail.com', '2024-11-29 01:23:13', '17328181931683208477'),
(78, 'guskininguaggto@yahoo.com', '2024-11-29 01:30:54', '17328186541121053406'),
(79, 'osgnuoiqtgi@yahoo.com', '2024-11-29 20:51:57', '1732888317620038999'),
(80, 'errssllazzuz@do-not-respond.me', '2024-11-30 02:46:24', '17329095841536678566'),
(81, 'errsllimzzuz@dont-reply.me', '2024-11-30 02:52:12', '1732909932633545316'),
(82, 'gpqbtc6nqou9g3slu@yahoo.com', '2024-11-30 15:49:27', '1732956567522389429'),
(83, 'atmazlli@yahoo.com', '2024-11-30 15:51:25', '17329566851425688622'),
(84, 'ddkninixdjlmx@yahoo.com', '2024-12-01 10:29:22', '17330237621766299841'),
(85, 'smemkkaehr@yahoo.com', '2024-12-01 11:13:40', '1733026420707505853'),
(86, 'oznekweinrib@yahoo.com', '2024-12-02 04:13:43', '17330876231561156402'),
(87, 'kendmayhp@gmail.com', '2024-12-02 20:06:13', '1733144773501074944'),
(88, 'ealmbllebruz@dont-reply.me', '2024-12-03 13:18:22', '17332067021670388021'),
(89, 'dqfdgngasj@yahoo.com', '2024-12-03 14:17:35', '1733210255392463742'),
(90, 'extlwjlgrpmylmxl@yahoo.com', '2024-12-03 14:23:20', '17332106001382698535'),
(91, 'nhejkyxnu@yahoo.com', '2024-12-04 08:37:26', '17332762461926678443'),
(92, 'muchxkdhrxjcphnch@yahoo.com', '2024-12-04 08:42:50', '17332765701761691666'),
(93, 'hmqqnywdnmgjuigfa@yahoo.com', '2024-12-04 23:40:39', '17333304391098678342'),
(94, 'gansrdefresne@yahoo.com', '2024-12-04 23:42:17', '17333305371815848629'),
(95, 'f8njlercwjyr@yahoo.com', '2024-12-05 18:22:16', '1733397736558757832'),
(96, 'ndastinq@gmail.com', '2024-12-05 18:38:36', '1733398716231185105'),
(97, 'icbjnxgnqeyys@yahoo.com', '2024-12-06 14:30:32', '1733470232656105210'),
(98, 'chlopkpapcu@yahoo.com', '2024-12-06 14:32:36', '1733470356887545787'),
(99, 'esamrbseijuz@dont-reply.me', '2024-12-07 10:48:41', '17335433211752705846'),
(100, 'lunarey40horizon@gmail.com', '2024-12-08 03:21:02', '17336028621126671257'),
(101, 'elzslmrljruz@dont-reply.me', '2024-12-08 17:35:02', '1733654102936809158'),
(102, 'delucific@yahoo.com', '2024-12-08 20:19:10', '1733663950906461052'),
(103, 'rydyardbradshawji5@gmail.com', '2024-12-08 20:36:00', '17336649601556642457'),
(104, 'bs8fid46xi@yahoo.com', '2024-12-09 19:09:13', '17337461531110739086'),
(105, 'opptalon@gmail.com', '2024-12-09 19:09:44', '1733746184918695338'),
(106, 'camillegnatek0@gmail.com', '2024-12-10 16:15:39', '1733822139336746563'),
(107, 'AnthonyTaylor63310@gmail.com', '2024-12-10 16:15:51', '1733822151408118510'),
(108, 'cunvdsc5nghdm@yahoo.com', '2024-12-11 19:04:08', '17339186481696354971'),
(109, 's3sqabdgdhpchsiz6@yahoo.com', '2024-12-11 19:19:49', '1733919589583150887'),
(110, 'emlsmrblbjuz@do-not-respond.me', '2024-12-12 19:04:16', '17340050561260557609'),
(111, 'nirbihwth2lebrpxe@yahoo.com', '2024-12-13 00:14:27', '1734023667196232691'),
(112, 'mkgaprvxxd@yahoo.com', '2024-12-13 00:27:08', '17340244281290558415'),
(113, 'tnylqi4bh0t@yahoo.com', '2024-12-14 03:45:12', '17341227122116840734'),
(114, 'dzborjassanchez@yahoo.com', '2024-12-14 03:48:46', '17341229261197167572'),
(115, 'bradleydjezebela1985@gmail.com', '2024-12-14 23:38:51', '17341943311916331182'),
(116, 'l2fgqz3xvw5@yahoo.com', '2024-12-15 19:02:41', '17342641612093902462'),
(117, 'yuuolqblgycpmo@yahoo.com', '2024-12-15 19:07:22', '1734264442141153903'),
(118, 'mgty1wkc266nll@yahoo.com', '2024-12-16 17:56:51', '17343466111077131566'),
(119, 'plourdjornlin@yahoo.com', '2024-12-16 18:12:48', '17343475681489992945'),
(120, 'bezirajrjjuz@dont-reply.me', '2024-12-17 21:31:06', '17344458661792494729'),
(121, 'enoweqiku80@gmail.com', '2024-12-18 05:47:21', '17344756412076675854'),
(122, 'vuditas479@gmail.com', '2024-12-18 06:04:33', '1734476673419230747'),
(123, 'besrmjsmrruz@dont-reply.me', '2024-12-19 04:07:41', '17345560611988004074'),
(124, 'ktxqiyv0mwfl@yahoo.com', '2024-12-19 05:41:01', '1734561661668947334'),
(125, 'jmnbinejaq@yahoo.com', '2024-12-20 06:16:25', '17346501851205920969'),
(126, 'mts1y95i8cs@yahoo.com', '2024-12-20 06:22:13', '17346505331971459001'),
(127, 'jowafodulodo14@gmail.com', '2024-12-21 05:22:23', '1734733343440866309'),
(128, 'tawancylrde@yahoo.com', '2024-12-21 05:28:52', '17347337321220959120'),
(129, 'nxlfreccif@yahoo.com', '2024-12-21 23:41:25', '17347992851612049013'),
(130, 'gtwr10s2x8t8v5p4p@yahoo.com', '2024-12-21 23:52:39', '17347999592030235385'),
(131, 'brrrmaimsjuz@dont-reply.me', '2024-12-22 17:36:43', '1734863803568798370'),
(132, 'mczealaguia@yahoo.com', '2024-12-22 18:33:25', '17348672051620488658'),
(133, 'xuwacacofa57@gmail.com', '2024-12-22 18:34:14', '1734867254839628102'),
(134, 'sdbbbsjcukxy@yahoo.com', '2024-12-23 12:44:43', '17349326831531985164'),
(135, 'bajbrzrazruz@do-not-respond.me', '2024-12-24 01:08:35', '17349773151920505389'),
(136, 'kepireheyiwi06@gmail.com', '2024-12-24 15:07:21', '17350276412122088952'),
(137, 'fi9f0om0c@yahoo.com', '2024-12-24 15:16:24', '1735028184377220361');

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimonial`
--

CREATE TABLE `testimonial` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `photo` varchar(30) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `testimonial`
--

INSERT INTO `testimonial` (`id`, `name`, `photo`, `content`) VALUES
(1, 'Aliyah Wati - Jakarta', '', 'Sist makasih barangnya udah sampe, bagus dan lucu2. Temenku aja pada ngiri. Semoga sukses selalu buat eveshopashopnya. Sory baru bisa kasih kabar.'),
(2, 'Een Enarsih - Banten', '', 'Sis barang ny dh sya trima,mkasih bnyak untuk layan’n ny sngat m’muaskan untuk sya,smu prtanya’n di jwab…\r\nRespon ny jga sngat baek,smoga usaha ny smakin brkembang'),
(3, 'Ayung Darma - Pekalongan', '', 'Oia mf sis,Nich brg nya brsan aja ampe, mksh ya\r\nBrg nya bgs banget, sesuai yg digambarnya, makasih ya'),
(4, 'Via Garolita - Cimahi', '', 'Sistaaaa……\r\nbaju nyaa udah smpee…\r\nbguss dechh…suka bgt…\r\nmaaksiih yaa'),
(5, 'Dewanti - Solo', '', 'Barang tidak mengecewakan.. cs nya fast respon, resi besoknya langsung di share tanpa kita tanya.. mantap tokohijabku'),
(6, 'Dina - Malang', '', 'Respon cs baik, tapi untuk pengirimannya agak lama, padahal pakai ekspedisi ”sicepat”\r\nharusnya bisa cepat sampainya.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `id_invoice` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `ket` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `transaction`
--

INSERT INTO `transaction` (`id`, `id_invoice`, `product_name`, `price`, `qty`, `slug`, `ket`) VALUES
(1, 899187, 'AMD Paket PC Desktop Gaming Rakitan || Terbatas', 4050000, 1, 'amd-paket-pc-desktop-gaming-rakitan-terbatas', ''),
(2, 299174, 'DEO SHRY HARUM', 14000, 2, 'deo-shry-harum', ''),
(3, 558740, 'DEO SHRY HARUM', 14000, 1, 'deo-shry-harum', ''),
(4, 235174, 'Natural Deodorant Spray \"DEO SHRY HARUM\" Varian Papermint', 20000, 1, 'natural-deodorant-spray-deo-shry-harum-varian-papermint', ''),
(5, 776743, 'Natural Deodorant Spray \"DEO SHRY HARUM\" Varian Lemon Mint ', 25000, 1, 'natural-deodorant-spray-deo-shry-harum-varian-lemon-mint', ''),
(6, 757362, 'DEO SHRY HARUM Varian Lemon Mint', 25000, 4, 'deo-shry-harum-varian-lemon-mint', ''),
(7, 804990, 'Deo Shry Harum - Citronella Essential Oil 100ml', 25000, 1, 'deo-shry-harum-citronella-essential-oil-100ml', ''),
(8, 504133, 'Deo Shry Harum - Citronella Essential Oil 100ml', 25000, 1, 'deo-shry-harum-citronella-essential-oil-100ml', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cod`
--
ALTER TABLE `cod`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cost_delivery`
--
ALTER TABLE `cost_delivery`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `email_send`
--
ALTER TABLE `email_send`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `footer`
--
ALTER TABLE `footer`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `general`
--
ALTER TABLE `general`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `img_product`
--
ALTER TABLE `img_product`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `package_product`
--
ALTER TABLE `package_product`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rekening`
--
ALTER TABLE `rekening`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sosmed`
--
ALTER TABLE `sosmed`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `subscriber`
--
ALTER TABLE `subscriber`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `testimonial`
--
ALTER TABLE `testimonial`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `cod`
--
ALTER TABLE `cod`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `cost_delivery`
--
ALTER TABLE `cost_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `email_send`
--
ALTER TABLE `email_send`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `footer`
--
ALTER TABLE `footer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `general`
--
ALTER TABLE `general`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `img_product`
--
ALTER TABLE `img_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `package`
--
ALTER TABLE `package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `package_product`
--
ALTER TABLE `package_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `rekening`
--
ALTER TABLE `rekening`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `sosmed`
--
ALTER TABLE `sosmed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `subscriber`
--
ALTER TABLE `subscriber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT untuk tabel `testimonial`
--
ALTER TABLE `testimonial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
