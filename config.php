<?php
// Pengaturan Database
define('DB_SERVER', 'srv1153.hstgr.io');
define('DB_USERNAME', 'u907350938_shop'); // Ganti dengan username database Anda
define('DB_PASSWORD', 'Powel123.,'); // Ganti dengan password database Anda
define('DB_NAME', 'u907350938_shop'); // Ganti dengan nama database Anda

// Membuat koneksi ke database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Pengaturan Toko
$storeConfig = [
    'storeName' => "Nandra Digital Shop", 
    'whatsappNumber' => "6281234567890", // GANTI DENGAN NOMOR WA ANDA
    'paymentInfo' => [
        'bank' => "BRI",
        'accountNumber' => "1234567890"
    ]
];
?>
