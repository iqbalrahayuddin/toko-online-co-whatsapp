<?php
// Mulai session di semua halaman
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set timezone default
date_default_timezone_set('Asia/Jakarta');

// --- PENGATURAN DATABASE ---
// Sesuaikan dengan detail database Anda
define('DB_HOST', 'srv1153.hstgr.io');
define('DB_USER', 'u907350938_shop');
define('DB_PASS', 'Powel123.,');
define('DB_NAME', 'u907350938_shop');

// --- PENGATURAN ADMIN ---
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'password123'); // Ganti dengan password yang lebih aman

// --- KONEKSI DATABASE ---
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// --- FUNGSI GLOBAL ---

/**
 * Fungsi untuk mengambil pengaturan dari database
 * @param string $key Kunci pengaturan yang ingin diambil
 * @param mysqli $db Koneksi database
 * @return string Nilai dari pengaturan
 */
function get_setting($key, $db) {
    $query = mysqli_query($db, "SELECT setting_value FROM settings WHERE setting_key = '$key'");
    if(mysqli_num_rows($query) > 0){
        $result = mysqli_fetch_assoc($query);
        return $result['setting_value'];
    }
    return ''; // Kembalikan string kosong jika tidak ditemukan
}

/**
 * Fungsi untuk memformat angka menjadi format Rupiah
 * @param int $number Angka yang akan diformat
 * @return string Angka dalam format Rupiah (Rp xxx.xxx)
 */
function format_rupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Ambil semua pengaturan untuk digunakan di seluruh situs
$settings = [];
$settings_query = mysqli_query($db, "SELECT * FROM settings");
while($row = mysqli_fetch_assoc($settings_query)){
    $settings[$row['setting_key']] = $row['setting_value'];
}
$store_name = $settings['store_name'] ?? 'Toko Online';
$whatsapp_number = $settings['whatsapp_number'] ?? '';
$store_address = $settings['store_address'] ?? '';
$shipping_cost_per_kg = $settings['shipping_cost_per_kg'] ?? 10000;

?>
