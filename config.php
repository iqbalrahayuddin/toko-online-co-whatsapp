<?php
// Mencegah error reporting untuk notice
error_reporting(E_ALL & ~E_NOTICE);

// Mulai session
session_start();

// --- PENGATURAN DATABASE (WAJIB DIUBAH!) ---
define('DB_HOST', 'srv1153.hstgr.io');
define('DB_USER', 'u907350938_shop'); // Ganti dengan username database Anda
define('DB_PASS', 'Powel123.,');     // Ganti dengan password database Anda
define('DB_NAME', 'u907350938_shop'); // Ganti dengan nama database Anda

// --- KONEKSI DATABASE ---
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

// --- FUNGSI HELPER ---

// Array untuk menyimpan cache pengaturan agar tidak query berulang kali
$settings_cache = [];

function get_setting($mysqli, $setting_name) {
    global $settings_cache;

    // Jika sudah ada di cache, langsung kembalikan
    if (isset($settings_cache[$setting_name])) {
        return $settings_cache[$setting_name];
    }

    // Jika belum ada, query ke database
    $stmt = $mysqli->prepare("SELECT setting_value FROM settings WHERE setting_name = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
    }
    $stmt->bind_param("s", $setting_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        // Simpan ke cache
        $settings_cache[$setting_name] = $row['setting_value'];
        return $row['setting_value'];
    }
    $stmt->close();
    return null; // Kembalikan null jika tidak ditemukan
}

function format_rupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>