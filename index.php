<?php
require_once 'config.php';

// --- *** FUNGSI HELPER BARU *** ---
define('FONNTE_TOKEN', 'aXr6Ean3oiCBDUGYGx2y');

/**
 * Memformat nomor telepon ke standar internasional (62xxx).
 * Disesuaikan untuk input yang diawali dengan '8'.
 * @param string $number Nomor telepon awal.
 * @return string Nomor telepon yang sudah diformat.
 */
function format_whatsapp_number($number) {
    // 1. Hapus semua karakter non-numerik
    $number = preg_replace('/[^0-9]/', '', $number);
    // 2. Jika nomor diawali dengan '8' (format input baru yang diharapkan)
    if (substr($number, 0, 1) == '8') {
        return '62' . $number;
    }
    // 3. Fallback jika pengguna masih memasukkan '0' di depan
    if (substr($number, 0, 1) == '0') {
        return '62' . substr($number, 1);
    }
    // 4. Jika pengguna sudah memasukkan '62'
    if (substr($number, 0, 2) == '62') {
        return $number;
    }
    // Default: jika format tidak terduga, coba tambahkan 62
    return '62' . $number;
}

/**
 * Mengirim notifikasi WhatsApp menggunakan Fonnte.
 * @param string $target Nomor tujuan yang sudah diformat.
 * @param string $message Isi pesan.
 */
function send_fonnte_notification($target, $message) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('target' => $target, 'message' => $message),
        CURLOPT_HTTPHEADER => array('Authorization: ' . FONNTE_TOKEN),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
}


// --- *** BAGIAN CALLBACK HANDLER *** ---
$actionForCallback = $_GET['action'] ?? '';
if ($actionForCallback === 'tripay_callback') {
    $privateKey = get_setting($mysqli, 'tripay_private_key');
    $callbackSignature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';
    $json = file_get_contents('php://input');

    $signature = hash_hmac('sha256', $json, $privateKey);
    if ($callbackSignature !== $signature) {
        exit(json_encode(['success' => false, 'message' => 'Invalid Signature']));
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        exit(json_encode(['success' => false, 'message' => 'Invalid JSON']));
    }

    if (isset($data['status']) && $data['status'] == 'PAID') {
        $merchantRef = $data['merchant_ref'];
        if (!is_dir('paid_invoices')) {
            mkdir('paid_invoices', 0775, true);
        }
        file_put_contents('paid_invoices/' . $merchantRef . '.json', $json);
    }
    
    exit(json_encode(['success' => true]));
}


// --- Mengambil semua pengaturan dari DB ---
$app_name = get_setting($mysqli, 'app_name');
$app_description = get_setting($mysqli, 'app_description');
$app_logo = get_setting($mysqli, 'app_logo');
$app_icon = get_setting($mysqli, 'app_icon');
$nomor_admin_wa = get_setting($mysqli, 'nomor_admin_wa');
$rajaongkirApiKey = get_setting($mysqli, 'rajaongkir_api_key');
$rajaongkirOriginId = get_setting($mysqli, 'rajaongkir_origin_id');
$rajaongkirApiUrl_Ongkir = 'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost';
$rajaongkirApiUrl_KodePos = 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination';
$tripayApiKey = get_setting($mysqli, 'tripay_api_key');
$tripayPrivateKey = get_setting($mysqli, 'tripay_private_key');
$tripayMerchantCode = get_setting($mysqli, 'tripay_merchant_code');
$tripayApiUrl = 'https://tripay.co.id/api/transaction/create';
$tripayApiUrl_Channels = 'https://tripay.co.id/api/merchant/payment-channel';


// --- Menentukan URL Absolut untuk Meta Tags ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$base_url = "{$protocol}://{$host}{$script_path}";
$meta_image_url = $base_url . '/uploads/' . rawurlencode($app_logo);
$current_url = $protocol . '://' . $host . $_SERVER['REQUEST_URI'];

// --- Inisialisasi Keranjang ---
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- Logika Pencarian Produk dari DB ---
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql_products = "SELECT * FROM products";
$params = [];
$types = '';
if (!empty($search_query)) {
    $sql_products .= " WHERE name LIKE ?";
    $search_param = "%" . $search_query . "%";
    $params[] = &$search_param;
    $types .= 's';
}
$sql_products .= " ORDER BY id DESC";
$stmt_products = $mysqli->prepare($sql_products);
if ($params) {
    $stmt_products->bind_param($types, ...$params);
}
$stmt_products->execute();
$products_result = $stmt_products->get_result();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Mini-API untuk Pencarian Kode Pos
if ($action === 'get_kodepos') {
    header('Content-Type: application/json');
    $kecamatan = trim($_POST['kecamatan'] ?? ''); $kelurahan = trim($_POST['kelurahan'] ?? '');
    if (empty($kecamatan) || empty($kelurahan)) { exit(json_encode(['success' => false])); }
    $keyword_to_search = $kelurahan . ', ' . $kecamatan;
    $url_to_fetch = $rajaongkirApiUrl_KodePos . '?search=' . urlencode($keyword_to_search) . '&limit=1';
    $curl = curl_init(); curl_setopt_array($curl, [CURLOPT_URL => $url_to_fetch, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["key: " . $rajaongkirApiKey]]);
    $response = curl_exec($curl); curl_close($curl);
    $data = json_decode($response, true);
    if (isset($data['data'][0]['zip_code'])) { echo json_encode(['success' => true, 'postal_code' => $data['data'][0]['zip_code']]); }
    else { echo json_encode(['success' => false]); }
    exit();
}
// Mini-API untuk Cek Ongkir
if ($action === 'calculate_ongkir') {
    header('Content-Type: application/json');
    $destination = $_POST['destination'] ?? '0';
    if (empty($_SESSION['cart'])) { exit(json_encode(['success' => false, 'message' => 'Keranjang kosong.'])); }
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $rajaongkirApiUrl_Ongkir, CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query(['origin' => $rajaongkirOriginId, 'destination' => $destination, 'weight' => 1000, 'courier' => 'jne:sicepat:jnt:ninja:anteraja:pos']),
        CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded", "key: " . $rajaongkirApiKey],
    ]);
    $response = curl_exec($curl); curl_close($curl); echo $response;
    exit();
}

// ** Mini-API untuk Channel Pembayaran Tripay **
if ($action === 'get_tripay_channels') {
    header('Content-Type: application/json');
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_FRESH_CONNECT  => true, CURLOPT_URL => $tripayApiUrl_Channels, CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => false, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tripayApiKey],
      CURLOPT_FAILONERROR => false, CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
    ]);
    $response = curl_exec($curl); curl_close($curl);
    $response_data = json_decode($response, true);
    if (isset($response_data['success']) && $response_data['success'] == true) {
        $filtered_channels = array_filter($response_data['data'], function($channel) { return $channel['code'] !== 'QRISC'; });
        echo json_encode(['success' => true, 'data' => array_values($filtered_channels)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil channel pembayaran.']);
    }
    exit();
}

// *** Mini-API untuk Cek Status Pembayaran ***
if ($action === 'check_payment_status') {
    header('Content-Type: application/json');
    if (isset($_SESSION['pending_payment']['merchant_ref'])) {
        $merchantRef = $_SESSION['pending_payment']['merchant_ref'];
        $filepath = 'paid_invoices/' . $merchantRef . '.json';
        if (file_exists($filepath)) {
            // Pembayaran Berhasil
            $orderData = $_SESSION['pending_payment'];
            
            // --- KIRIM NOTIFIKASI FONNTE KE PELANGGAN (DENGAN DETAIL LENGKAP) ---
            $customer_number = format_whatsapp_number($orderData['whatsapp']);
            $customer_message = "Terima kasih *{$orderData['nama']}*! Pembayaran Anda telah berhasil kami verifikasi (LUNAS).\n\n";
            $customer_message .= "*Pesanan Anda akan segera kami proses.*\n\n";
            $customer_message .= "--- DETAIL PESANAN ---\n";
            $customer_message .= "No. Pesanan: *{$orderData['merchant_ref']}*\n\n";
            foreach ($orderData['order_items_details'] as $order_item) { $customer_message .= "Produk: *{$order_item['name']}* (x{$order_item['quantity']})\n"; }
            $customer_message .= "\nSubtotal Produk: " . format_rupiah($orderData['subtotal_produk']) . "\n";
            $customer_message .= "Pengiriman: {$orderData['shipping_details']} (" . format_rupiah($orderData['shipping_cost']) . ")\n";
            $customer_message .= "--------------------------\n";
            $customer_message .= "*TOTAL BAYAR: " . format_rupiah($orderData['grand_total']) . "*\n";
            $customer_message .= "--------------------------\n\n";
            $customer_message .= "*Alamat Pengiriman:*\n{$orderData['nama']}\n" . '0' . substr($customer_number, 2) . "\n\n{$orderData['alamat']}\nKel. {$orderData['kelurahan']}, Kec. {$orderData['kecamatan']}\n{$orderData['kota']}, {$orderData['provinsi']} {$orderData['kodepos']}";
            send_fonnte_notification($customer_number, $customer_message);
            // --- AKHIR BLOK NOTIFIKASI ---
            
            // Buat pesan untuk Admin (tidak berubah)
            $admin_message = "Halo *{$app_name}*, pembayaran untuk pesanan *{$orderData['merchant_ref']}* telah berhasil diverifikasi (LUNAS).\n\n--- DETAIL PESANAN ---\n";
            foreach ($orderData['order_items_details'] as $order_item) { $admin_message .= "Produk: *{$order_item['name']}* (x{$order_item['quantity']})\n"; }
            $admin_message .= "\nSubtotal Produk: " . format_rupiah($orderData['subtotal_produk']) . "\nPengiriman: {$orderData['shipping_details']} (" . format_rupiah($orderData['shipping_cost']) . ")\n";
            $admin_message .= "--------------------------\n*TOTAL BAYAR: " . format_rupiah($orderData['grand_total']) . "*\n--------------------------\n\n*Alamat Pengiriman:*\n{$orderData['nama']}\n" . '0' . substr($customer_number, 2) . "\n\n{$orderData['alamat']}\nKel. {$orderData['kelurahan']}, Kec. {$orderData['kecamatan']}\n{$orderData['kota']}, {$orderData['provinsi']} {$orderData['kodepos']}\n\nMohon segera diproses. Terima kasih.";

            // Bersihkan sesi dan file status
            unset($_SESSION['pending_payment']);
            @unlink($filepath);

            echo json_encode(['status' => 'PAID', 'whatsapp_url' => "https://api.whatsapp.com/send?phone={$nomor_admin_wa}&text=" . urlencode($admin_message)]);
        } else {
            echo json_encode(['status' => 'UNPAID']);
        }
    } else {
        echo json_encode(['status' => 'NO_TRANSACTION']);
    }
    exit();
}


// Action handler lain: add, update, remove cart
if ($action === 'add' || $action === 'buy_now') { $product_id = (int)$_POST['product_id']; if (isset($_SESSION['cart'][$product_id])) { $_SESSION['cart'][$product_id]['quantity']++; } else { $_SESSION['cart'][$product_id] = ['id' => $product_id, 'quantity' => 1]; } if ($action === 'buy_now') { header('Location: index.php?page=checkout'); exit(); } header('Location: index.php?status=added'); exit(); }
if ($action === 'update_cart') { $product_id = (int)$_POST['product_id']; $quantity = (int)$_POST['quantity']; if (isset($_SESSION['cart'][$product_id])) { if ($quantity > 0) { $_SESSION['cart'][$product_id]['quantity'] = $quantity; } else { unset($_SESSION['cart'][$product_id]); } } header('Location: index.php?page=cart'); exit(); }
if ($action === 'remove_from_cart') { $product_id = (int)$_GET['id']; if (isset($_SESSION['cart'][$product_id])) { unset($_SESSION['cart'][$product_id]); } header('Location: index.php?page=cart'); exit(); }

// --- *** PROSES CHECKOUT UTAMA (DIUBAH UNTUK AJAX) *** ---
if ($action === 'process_checkout' && !empty($_SESSION['cart'])) {
    header('Content-Type: application/json');

    $nama = htmlspecialchars(trim($_POST['nama'])); $email = htmlspecialchars(trim($_POST['email'])); $whatsapp = htmlspecialchars(trim($_POST['whatsapp'])); $alamat = htmlspecialchars(trim($_POST['alamat']));
    $provinsi = htmlspecialchars(trim($_POST['provinsi_text'])); $kota = htmlspecialchars(trim($_POST['kota_text'])); $kecamatan = htmlspecialchars(trim($_POST['kecamatan_text'])); $kelurahan = htmlspecialchars(trim($_POST['kelurahan_text']));
    $payment_method = $_POST['payment_method'] ?? ''; $kodepos = htmlspecialchars(trim($_POST['kodepos'])); $shipping_details = htmlspecialchars(trim($_POST['shipping_details'])); $shipping_cost = (int)($_POST['shipping_cost'] ?? 0);
    
    if (empty($nama) || empty($email) || empty($whatsapp) || empty($alamat) || empty($payment_method) || empty($kodepos) || empty($shipping_details) || $shipping_cost <= 0) {
        echo json_encode(['success' => false, 'message' => 'Harap lengkapi semua data, termasuk memilih opsi pengiriman.']);
        exit();
    }

    $subtotal_produk = 0; $order_items_details = [];
    foreach ($_SESSION['cart'] as $item) {
        $stmt_checkout = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
        $stmt_checkout->bind_param("i", $item['id']); $stmt_checkout->execute();
        $product = $stmt_checkout->get_result()->fetch_assoc(); $stmt_checkout->close();
        if($product) {
            $subtotal_produk += $product['price'] * $item['quantity'];
            $order_items_details[] = ['sku' => 'P' . $product['id'], 'name' => $product['name'], 'price' => $product['price'], 'quantity' => $item['quantity'], 'product_url' => $base_url . '/index.php?page=product&id=' . $product['id'], 'image_url' => $base_url . '/uploads/' . rawurlencode($product['image']),];
        }
    }
    $grand_total = $subtotal_produk + $shipping_cost;

    if ($payment_method === 'COD') {
        $pesan = "Halo *{$app_name}*, saya pesan untuk *BAYAR DI TEMPAT (COD)*:\n\n--- DETAIL PESANAN ---\n";
        foreach ($order_items_details as $order_item) { $pesan .= "Produk: *{$order_item['name']}* (x{$order_item['quantity']})\n"; }
        $pesan .= "\nSubtotal Produk: " . format_rupiah($subtotal_produk) . "\nPengiriman: {$shipping_details} (" . format_rupiah($shipping_cost) . ")\n";
        $pesan .= "--------------------------\n*TOTAL BAYAR: " . format_rupiah($grand_total) . "*\n--------------------------\n\n*Alamat Pengiriman:*\n{$nama}\n0{$whatsapp}\n\n{$alamat}\nKel. {$kelurahan}, Kec. {$kecamatan}\n{$kota}, {$provinsi} {$kodepos}\n\nMohon segera diproses. Terima kasih.";
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true, 'redirect_url' => "https://api.whatsapp.com/send?phone={$nomor_admin_wa}&text=" . urlencode($pesan)]);
        exit();
    }

    if ($payment_method === 'TRIPAY') {
        $tripay_method = $_POST['tripay_method'] ?? '';
        if (empty($tripay_method)) { echo json_encode(['success' => false, 'message' => 'Harap pilih channel pembayaran terlebih dahulu.']); exit(); }

        $order_items_details[] = ['sku' => 'ONGKIR', 'name' => 'Biaya Pengiriman (' . $shipping_details . ')', 'price' => $shipping_cost, 'quantity' => 1];
        $merchantRef = 'INV-' . time();
        $signature = hash_hmac('sha256', $tripayMerchantCode . $merchantRef . $grand_total, $tripayPrivateKey);

        $payload = [
            'method' => $tripay_method, 'merchant_ref' => $merchantRef, 'amount' => $grand_total,
            'customer_name' => $nama, 'customer_email' => $email, 'customer_phone' => '0'.$whatsapp,
            'order_items' => $order_items_details, 'return_url' => $base_url . '/index.php?page=thankyou',
            'expired_time' => (time() + (24 * 60 * 60)), 'signature' => $signature
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true, CURLOPT_URL => $tripayApiUrl, CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tripayApiKey], CURLOPT_FAILONERROR => false, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload), CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ]);
        $response = curl_exec($curl); curl_close($curl);
        $response_data = json_decode($response, true);

        if (isset($response_data['success']) && $response_data['success'] == true) {
            $_SESSION['cart'] = [];
            $_SESSION['pending_payment'] = [
                'merchant_ref' => $merchantRef, 'nama' => $nama, 'whatsapp' => $whatsapp, 'alamat' => $alamat,
                'provinsi' => $provinsi, 'kota' => $kota, 'kecamatan' => $kecamatan, 'kelurahan' => $kelurahan, 'kodepos' => $kodepos,
                'shipping_details' => $shipping_details, 'shipping_cost' => $shipping_cost, 'subtotal_produk' => $subtotal_produk,
                'grand_total' => $grand_total, 'order_items_details' => $order_items_details
            ];
            echo json_encode(['success' => true, 'data' => $response_data['data']]);
        } else {
            $errorMessage = $response_data['message'] ?? 'Gagal membuat transaksi.';
            echo json_encode(['success' => false, 'message' => $errorMessage, 'response' => $response_data]);
        }
        exit();
    }
}

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($app_name) ?></title>
    <meta name="description" content="<?= htmlspecialchars($app_description) ?>"><meta property="og:title" content="<?= htmlspecialchars($app_name) ?>"><meta property="og:description" content="<?= htmlspecialchars($app_description) ?>"><meta property="og:image" content="<?= $meta_image_url ?>"><meta property="og:url" content="<?= $current_url ?>"><meta property="og:type" content="website"><meta property="og:site_name" content="<?= htmlspecialchars($app_name) ?>"><meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="<?= htmlspecialchars($app_name) ?>"><meta name="twitter:description" content="<?= htmlspecialchars($app_description) ?>"><meta name="twitter:image" content="<?= $meta_image_url ?>">
    <link rel="icon" href="uploads/<?= htmlspecialchars($app_icon) ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.1); background-color: #ffffff; }
        .product-card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,.05); transition: transform .3s ease, box-shadow .3s ease; border: none; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,.1); }
        .product-card img { aspect-ratio: 4 / 3; object-fit: cover; }
        .footer { background-color: #343a40; color: white; padding: 2rem 0; margin-top: 4rem; }
        .cart-icon { position: relative; }
        .cart-badge { position: absolute; top: -5px; right: -10px; padding: 0.25em 0.5em; font-size: 0.7rem; font-weight: bold; border-radius: 50rem; }
        .payment-option, .shipping-option { border: 1px solid #dee2e6; border-radius: .375rem; padding: 1rem; transition: all .15s ease-in-out; cursor:pointer; }
        .payment-option:has(.form-check-input:checked), .shipping-option:has(.form-check-input:checked) { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25); }
        #shipping-options-container, #tripay-channels-container { max-height: 250px; overflow-y: auto; }
        .channel-option { border: 1px solid #dee2e6; border-radius: .375rem; padding: 0.75rem 1rem; transition: all .15s ease-in-out; cursor:pointer; }
        .channel-option:has(.form-check-input:checked) { border-color: #0d6efd; background-color: #e7f1ff; }
        .channel-option img { max-height: 25px; max-width: 60px; margin-right: 15px; }
        .va-number-container { background-color: #e9ecef; border: 1px dashed #ced4da; }
    </style>
</head>
<body>
    <header><nav class="navbar navbar-expand-lg sticky-top"><div class="container"><a class="navbar-brand d-flex align-items-center" href="index.php"><img src="uploads/<?= htmlspecialchars($app_logo) ?>" alt="Logo" height="30" class="me-2"><?= htmlspecialchars($app_name) ?></a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav ms-auto"><li class="nav-item"><a class="nav-link" href="index.php">Produk</a></li><li class="nav-item"><a class="nav-link cart-icon" href="index.php?page=cart"><i class="fa-solid fa-cart-shopping"></i> Keranjang<?php $cart_count = count($_SESSION['cart'] ?? []); if ($cart_count > 0): ?><span class="badge bg-danger cart-badge"><?= $cart_count ?></span><?php endif; ?></a></li></ul></div></div></nav></header>
    <main class="container my-5">
        <?php if ($page === 'home'): ?>
            <div class="row justify-content-center mb-5"><div class="col-md-8 col-lg-6"><form action="index.php" method="get" class="d-flex"><input type="hidden" name="page" value="home"><input type="text" class="form-control form-control-lg" name="q" placeholder="Cari produk..." value="<?= htmlspecialchars($search_query) ?>"><button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-magnifying-glass"></i></button></form></div></div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4"><?php if ($products_result->num_rows === 0): ?><div class="col-12"><div class="alert alert-warning text-center">Produk tidak ditemukan.</div></div><?php else: while($product = $products_result->fetch_assoc()): ?><div class="col"><div class="card h-100 product-card"><img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"><div class="card-body d-flex flex-column"><h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5><p class="mt-auto fw-bold fs-5 text-primary"><?= format_rupiah($product['price']) ?></p><div class="d-grid gap-2 mt-2"><form action="index.php" method="post" class="d-grid"><input type="hidden" name="product_id" value="<?= $product['id'] ?>"><input type="hidden" name="action" value="buy_now"><button type="submit" class="btn btn-primary">Beli Sekarang</button></form><form action="index.php" method="post" class="d-grid"><input type="hidden" name="product_id" value="<?= $product['id'] ?>"><input type="hidden" name="action" value="add"><button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-cart-plus me-2"></i>Keranjang</button></form></div></div></div></div><?php endwhile; endif; ?></div>
        <?php elseif ($page === 'cart'): ?>
            <h2 class="mb-4">Keranjang Belanja</h2><?php if (empty($_SESSION['cart'])): ?><div class="alert alert-info text-center"><h4 class="alert-heading">Keranjang Anda kosong!</h4> <p>Silakan kembali berbelanja.</p></div><?php else: ?><div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th class="ps-3">Produk</th><th>Harga</th><th class="text-center">Kuantitas</th><th class="text-end">Subtotal</th><th class="text-center">Aksi</th></tr></thead><tbody><?php $total_harga = 0; foreach ($_SESSION['cart'] as $item): $stmt_cart = $mysqli->prepare("SELECT * FROM products WHERE id = ?");$stmt_cart->bind_param("i", $item['id']); $stmt_cart->execute();$product = $stmt_cart->get_result()->fetch_assoc(); $stmt_cart->close();if(!$product) continue;$subtotal = $product['price'] * $item['quantity']; $total_harga += $subtotal; ?><tr><td class="ps-3"><div class="d-flex align-items-center"><img src="uploads/<?= htmlspecialchars($product['image']) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;" alt="<?= htmlspecialchars($product['name']) ?>"><div class="ms-3 fw-bold"><?= htmlspecialchars($product['name']) ?></div></div></td><td><?= format_rupiah($product['price']) ?></td><td><form action="index.php" method="post" class="d-flex justify-content-center"><input type="hidden" name="action" value="update_cart"><input type="hidden" name="product_id" value="<?= $item['id'] ?>"><input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm" style="width:70px;" onchange="this.form.submit()"></form></td><td class="text-end fw-bold"><?= format_rupiah($subtotal) ?></td><td class="text-center"><a href="index.php?action=remove_from_cart&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash-can"></i></a></td></tr><?php endforeach; ?></tbody><tfoot><tr class="table-light"><td colspan="3" class="text-end fw-bold ps-3">Total Belanja</td><td class="text-end fw-bold fs-5 text-primary"><?= format_rupiah($total_harga) ?></td><td></td></tr></tfoot></table></div></div><div class="text-end mt-4"><a href="index.php?page=checkout" class="btn btn-primary btn-lg">Lanjutkan ke Checkout <i class="fa-solid fa-arrow-right ms-2"></i></a></div><?php endif; ?>
        <?php elseif ($page === 'checkout'):
            if (empty($_SESSION['cart'])) { echo "<script>window.location.href = 'index.php?page=cart';</script>"; exit(); }
            $subtotal_produk_checkout = 0;
            foreach ($_SESSION['cart'] as $item) { $stmt_checkout_sub = $mysqli->prepare("SELECT price FROM products WHERE id = ?"); $stmt_checkout_sub->bind_param("i", $item['id']); $stmt_checkout_sub->execute(); $product_price = $stmt_checkout_sub->get_result()->fetch_assoc()['price'] ?? 0; $subtotal_produk_checkout += $product_price * $item['quantity']; $stmt_checkout_sub->close(); } ?>
            <div class="row g-5"><div class="col-md-5 col-lg-4 order-md-last"><h4 class="mb-3"><span class="text-primary">Ringkasan</span></h4><ul class="list-group mb-3"><li class="list-group-item d-flex justify-content-between"><span>Subtotal Produk</span><strong><?= format_rupiah($subtotal_produk_checkout) ?></strong></li><li id="shipping-cost-summary" class="list-group-item d-flex justify-content-between d-none"><span>Ongkos Kirim</span><strong id="shipping-cost-text">-</strong></li><li class="list-group-item d-flex justify-content-between bg-light fs-5"><span class="fw-bold">Grand Total</span><strong id="grand-total-text" data-subtotal="<?= $subtotal_produk_checkout ?>"><?= format_rupiah($subtotal_produk_checkout) ?></strong></li></ul></div><div class="col-md-7 col-lg-8"><h4 class="mb-3">Detail Pesanan</h4><div id="checkout-error" class="alert alert-danger d-none"></div>
                <form id="checkout-form" action="index.php" method="post">
                    <input type="hidden" name="action" value="process_checkout"><input type="hidden" name="provinsi_text" id="provinsi_text"><input type="hidden" name="kota_text" id="kota_text"><input type="hidden" name="kecamatan_text" id="kecamatan_text"><input type="hidden" name="kelurahan_text" id="kelurahan_text"><input type="hidden" name="kodepos" id="kodepos-input"><input type="hidden" name="shipping_cost" id="shipping_cost_input" value="0"><input type="hidden" name="shipping_details" id="shipping_details_input"><input type="hidden" name="tripay_method" id="tripay_method_input">
                    <div class="row g-3">
                        <div class="col-sm-6"><label for="nama" class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="nama" name="nama" required></div>
                        <div class="col-sm-6"><label for="whatsapp" class="form-label">Nomor WhatsApp</label><div class="input-group"><span class="input-group-text">+62</span><input type="tel" class="form-control" id="whatsapp" name="whatsapp" placeholder="8123456789" pattern="8[0-9]{8,15}" title="Masukkan nomor WhatsApp valid diawali dengan angka 8." required></div></div>
                        <div class="col-12"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" required></div>
                        <div class="col-sm-6"><label for="provinsi" class="form-label">Provinsi</label><select class="form-select" id="provinsi" required><option value="">Memuat...</option></select></div>
                        <div class="col-sm-6"><label for="kota" class="form-label">Kota/Kabupaten</label><select class="form-select" id="kota" required disabled></select></div>
                        <div class="col-sm-6"><label for="kecamatan" class="form-label">Kecamatan</label><select class="form-select" id="kecamatan" required disabled></select></div>
                        <div class="col-sm-6"><label for="kelurahan" class="form-label">Kelurahan/Desa</label><select class="form-select" id="kelurahan" required disabled></select></div>
                        <div class="col-12"><label for="alamat" class="form-label">Alamat Lengkap</label><textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW" required></textarea></div>
                        <div id="kodepos-container" class="col-12 mt-2 d-none"><span class="fw-medium">Kode Pos:</span> <span id="kodepos-result" class="badge fs-6"></span></div>
                    </div><hr class="my-4">
                    <div id="shipping-section" class="d-none"><h4 class="mb-3">Opsi Pengiriman</h4><div class="text-center" id="shipping-loader"><div class="spinner-border text-primary"></div><p>Mencari ongkir...</p></div><div id="shipping-options-container" class="vstack gap-2"></div><div id="shipping-error" class="alert alert-warning d-none"></div></div><hr class="my-4">
                    <h4 class="mb-3">Metode Pembayaran</h4><div class="vstack gap-2"><div class="payment-option"><input id="cod" name="payment_method" type="radio" class="form-check-input" value="COD" required><label class="form-check-label w-100 ms-2" for="cod"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Bayar di Tempat (COD)</label></div><div class="payment-option"><input id="tripay" name="payment_method" type="radio" class="form-check-input" value="TRIPAY" required><label class="form-check-label w-100 ms-2" for="tripay"><i class="fa-solid fa-credit-card me-2"></i>Transfer Bank / E-Wallet</label></div></div><hr class="my-4">
                    <button class="w-100 btn btn-primary btn-lg" type="submit" id="process-order-btn"><span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Proses Pesanan</button>
                </form>
            </div></div>
        <?php endif; ?>
    </main>
    
    <div class="modal fade" id="tripayChannelModal" tabindex="-1" aria-labelledby="tripayChannelModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h1 class="modal-title fs-5" id="tripayChannelModalLabel">Pilih Channel Pembayaran</h1><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div id="tripay-channels-loader" class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat channel...</p></div><div id="tripay-channels-error" class="alert alert-danger d-none"></div><div id="tripay-channels-container" class="vstack gap-2"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" id="confirm-tripay-channel" disabled>Lanjutkan</button></div></div></div></div>
    <div class="modal fade" id="paymentDetailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="paymentDetailModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h1 class="modal-title fs-5" id="paymentDetailModalLabel">Detail Pembayaran</h1></div><div class="modal-body" id="payment-modal-body"><div id="payment-waiting-view" class="text-center my-4"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div><h5 class="mt-3">Menunggu Pembayaran...</h5><p class="text-muted small">Jangan tutup halaman ini. Pembayaran akan dicek secara otomatis.</p></div><div id="payment-details-view" class="d-none"><p class="text-center">Selesaikan pembayaran Anda sebelum waktu kedaluwarsa.</p><div class="text-center mb-3"><h5 class="mb-1">Total Tagihan</h5><h3 class="fw-bold text-primary" id="payment-amount"></h3></div><div id="qris-display" class="text-center d-none"><img id="qris-image" src="" alt="QR Code" class="img-fluid rounded" style="max-width: 250px;"><p class="mt-2 small text-muted">Scan QR Code menggunakan aplikasi E-Wallet Anda.</p></div><div id="va-display" class="d-none"><p class="mb-2" id="va-payment-name"></p><div class="input-group mb-3 va-number-container p-2 rounded"><span class="input-group-text bg-transparent border-0 fw-bold fs-5" id="va-number"></span><button class="btn btn-outline-primary ms-auto" type="button" id="copy-va-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Salin Kode"><i class="fa-regular fa-copy"></i></button></div></div><hr><div id="payment-instructions"></div></div>
            <div id="payment-success-view" class="text-center my-4 d-none">
                <i class="fa-solid fa-circle-check fa-4x text-success"></i>
                <h5 class="mt-3">Pembayaran Berhasil!</h5>
                <p class="text-muted small">Notifikasi otomatis telah dikirim ke WhatsApp Anda. Anda juga dapat mengirim pesan konfirmasi tambahan ke admin.</p>
                <div id="success-action-container" class="mt-4"></div>
            </div>
        </div></div></div></div>

    <footer class="footer text-center"><p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($app_name) ?>.</p></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    if (document.getElementById('checkout-form')) {
        const API_WILAYAH_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        const selectProvinsi = document.getElementById('provinsi'), selectKota = document.getElementById('kota'), selectKecamatan = document.getElementById('kecamatan'), selectKelurahan = document.getElementById('kelurahan'), checkoutForm = document.getElementById('checkout-form');
        const tripayChannelModal = new bootstrap.Modal(document.getElementById('tripayChannelModal'));
        const paymentDetailModal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
        const copyBtnTooltip = new bootstrap.Tooltip(document.getElementById('copy-va-btn'));
        let paymentCheckInterval;

        function fetchWilayah(url, selectElement) {
            selectElement.disabled = true; selectElement.innerHTML = '<option value="">Memuat...</option>';
            fetch(url).then(r => r.json()).then(data => {
                selectElement.innerHTML = '<option value="">-- Pilih --</option>';
                data.forEach(item => selectElement.add(new Option(item.name, item.id)));
                selectElement.disabled = false;
            }).catch(() => { selectElement.innerHTML = '<option value="">Gagal</option>'; });
        }

        document.addEventListener('DOMContentLoaded', () => fetchWilayah(`${API_WILAYAH_URL}/provinces.json`, selectProvinsi));
        selectProvinsi.addEventListener('change', () => { document.getElementById('provinsi_text').value = selectProvinsi.options[selectProvinsi.selectedIndex].text; fetchWilayah(`${API_WILAYAH_URL}/regencies/${selectProvinsi.value}.json`, selectKota); });
        selectKota.addEventListener('change', () => { document.getElementById('kota_text').value = selectKota.options[selectKota.selectedIndex].text; fetchWilayah(`${API_WILAYAH_URL}/districts/${selectKota.value}.json`, selectKecamatan); });
        selectKecamatan.addEventListener('change', () => { document.getElementById('kecamatan_text').value = selectKecamatan.options[selectKecamatan.selectedIndex].text; fetchWilayah(`${API_WILAYAH_URL}/villages/${selectKecamatan.value}.json`, selectKelurahan); });
        selectKelurahan.addEventListener('change', function() { document.getElementById('kelurahan_text').value = this.options[this.selectedIndex].text; const kecamatanText = document.getElementById('kecamatan_text').value, kelurahanText = this.options[this.selectedIndex].text; if (!kecamatanText || !kelurahanText) return; fetchPostalCode(kecamatanText, kelurahanText); });

        function fetchPostalCode(kecamatan, kelurahan) {
            const kodeposContainer = document.getElementById('kodepos-container'), kodeposResult = document.getElementById('kodepos-result');
            kodeposContainer.classList.remove('d-none'); kodeposResult.innerHTML = '<div class="spinner-border spinner-border-sm"></div>';
            const formData = new FormData(); formData.append('action', 'get_kodepos'); formData.append('kecamatan', kecamatan); formData.append('kelurahan', kelurahan);
            fetch('index.php', { method: 'POST', body: formData }).then(r => r.json()).then(data => {
                if (data.success) { kodeposResult.textContent = data.postal_code; kodeposResult.className = 'badge fs-6 bg-success'; document.getElementById('kodepos-input').value = data.postal_code; fetchShippingOptions(data.postal_code); } else { kodeposResult.textContent = 'Tidak ditemukan'; kodeposResult.className = 'badge fs-6 bg-danger'; }
            });
        }

        function fetchShippingOptions(postalCode) {
            const shippingSection = document.getElementById('shipping-section'), loader = document.getElementById('shipping-loader'), container = document.getElementById('shipping-options-container'), errorContainer = document.getElementById('shipping-error');
            shippingSection.classList.remove('d-none'); loader.classList.remove('d-none'); container.innerHTML = ''; errorContainer.classList.add('d-none');
            const formData = new FormData(); formData.append('action', 'calculate_ongkir'); formData.append('destination', postalCode);
            fetch('index.php', { method: 'POST', body: formData }).then(r => r.json()).then(data => {
                loader.classList.add('d-none');
                if (data.meta.status === 'success' && data.data.length > 0) {
                    const sortedOptions = data.data.sort((a, b) => a.cost - b.cost);
                    sortedOptions.forEach(option => { const costValue = option.cost, etd = option.etd, id = `${option.code}-${option.service.replace(/\s+/g, '-')}`, details = `${option.name} - ${option.service}`; container.innerHTML += `<div class="shipping-option"><input type="radio" class="form-check-input" name="shipping_option" id="${id}" value="${costValue}" data-details="${details}" required><label class="form-check-label w-100 ms-2" for="${id}">${details} (${etd}) - <strong>${new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR', minimumFractionDigits: 0}).format(costValue)}</strong></label></div>`; });
                    addShippingEventListeners();
                } else { errorContainer.innerText = data.message || 'Tidak ada opsi pengiriman ke tujuan ini.'; errorContainer.classList.remove('d-none'); }
            });
        }

        function addShippingEventListeners() { document.querySelectorAll('input[name="shipping_option"]').forEach(radio => { radio.addEventListener('change', function() { const subtotal = parseFloat(document.getElementById('grand-total-text').dataset.subtotal), shippingCost = parseFloat(this.value), grandTotal = subtotal + shippingCost, shippingDetails = this.dataset.details; document.getElementById('shipping-cost-summary').classList.remove('d-none'); document.getElementById('shipping-cost-text').innerText = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR', minimumFractionDigits: 0}).format(shippingCost); document.getElementById('grand-total-text').innerText = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR', minimumFractionDigits: 0}).format(grandTotal); document.getElementById('shipping_cost_input').value = shippingCost; document.getElementById('shipping_details_input').value = shippingDetails; }); }); }
        
        checkoutForm.addEventListener('submit', function(event) {
            event.preventDefault();
            if (!this.checkValidity()) { this.classList.add('was-validated'); return; }
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (paymentMethod && paymentMethod.value === 'TRIPAY') { showTripayChannelModal(); } else { processOrder(); }
        });

        function showTripayChannelModal() {
            tripayChannelModal.show();
            const loader = document.getElementById('tripay-channels-loader'), container = document.getElementById('tripay-channels-container'), errorContainer = document.getElementById('tripay-channels-error'), confirmBtn = document.getElementById('confirm-tripay-channel');
            loader.classList.remove('d-none'); container.innerHTML = ''; errorContainer.classList.add('d-none'); confirmBtn.disabled = true;
            fetch('index.php', { method: 'POST', body: new URLSearchParams("action=get_tripay_channels") }).then(r => r.json()).then(data => {
                loader.classList.add('d-none');
                if (data.success && data.data.length > 0) {
                    data.data.forEach(channel => { container.innerHTML += `<div class="channel-option"><input type="radio" class="form-check-input" name="tripay_channel_option" id="channel-${channel.code}" value="${channel.code}" required><label class="form-check-label w-100 ms-2 d-flex align-items-center" for="channel-${channel.code}"><img src="${channel.icon_url}" alt="${channel.name}"><span class="fw-medium">${channel.name}</span></label></div>`; });
                    document.querySelectorAll('input[name="tripay_channel_option"]').forEach(radio => radio.addEventListener('change', () => { confirmBtn.disabled = false; }));
                } else { errorContainer.textContent = data.message || 'Gagal memuat channel.'; errorContainer.classList.remove('d-none'); }
            }).catch(() => { loader.classList.add('d-none'); errorContainer.textContent = 'Terjadi kesalahan jaringan.'; errorContainer.classList.remove('d-none'); });
        }
        
        document.getElementById('confirm-tripay-channel').addEventListener('click', function() {
            const selectedChannel = document.querySelector('input[name="tripay_channel_option"]:checked');
            if (selectedChannel) { document.getElementById('tripay_method_input').value = selectedChannel.value; tripayChannelModal.hide(); processOrder(); }
        });

        function processOrder() {
            const btn = document.getElementById('process-order-btn'), btnSpinner = btn.querySelector('.spinner-border'), errorDiv = document.getElementById('checkout-error');
            btn.disabled = true; btnSpinner.classList.remove('d-none'); errorDiv.classList.add('d-none');
            fetch('index.php', { method: 'POST', body: new FormData(checkoutForm) }).then(response => response.json()).then(data => {
                if (data.success) {
                    if (data.redirect_url) { window.location.href = data.redirect_url; } 
                    else if (data.data) { displayPaymentDetails(data.data); }
                } else { errorDiv.textContent = data.message || 'Terjadi kesalahan.'; errorDiv.classList.remove('d-none'); btn.disabled = false; btnSpinner.classList.add('d-none'); }
            }).catch(error => { errorDiv.textContent = 'Terjadi kesalahan. Silakan coba lagi.'; errorDiv.classList.remove('d-none'); btn.disabled = false; btnSpinner.classList.add('d-none'); });
        }

        function displayPaymentDetails(data) {
            document.getElementById('payment-details-view').classList.add('d-none');
            document.getElementById('payment-waiting-view').classList.remove('d-none');
            document.getElementById('payment-success-view').classList.add('d-none');
            paymentDetailModal.show();
            setTimeout(() => {
                const qrisDisplay = document.getElementById('qris-display'), vaDisplay = document.getElementById('va-display');
                qrisDisplay.classList.add('d-none'); vaDisplay.classList.add('d-none');
                document.getElementById('payment-amount').textContent = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR', minimumFractionDigits: 0}).format(data.amount);
                if (data.qr_string) { qrisDisplay.classList.remove('d-none'); document.getElementById('qris-image').src = data.qr_url; } 
                else if (data.pay_code) { vaDisplay.classList.remove('d-none'); document.getElementById('va-payment-name').textContent = `Nomor ${data.payment_name}`; document.getElementById('va-number').textContent = data.pay_code; }
                const instructionsContainer = document.getElementById('payment-instructions');
                instructionsContainer.innerHTML = '<h6>Cara Pembayaran</h6>';
                const instructionsList = document.createElement('ol');
                instructionsList.classList.add('list-group', 'list-group-numbered');
                if(data.instructions && data.instructions.length > 0) {
                    data.instructions.forEach(instruction => { let stepsHtml = '<ol class="ps-3">'; instruction.steps.forEach(step => { stepsHtml += `<li>${step.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</li>`; }); stepsHtml += '</ol>'; instructionsList.innerHTML += `<li class="list-group-item"><strong>${instruction.title}</strong>${stepsHtml}</li>`; });
                    instructionsContainer.appendChild(instructionsList);
                }
                document.getElementById('payment-waiting-view').classList.add('d-none');
                document.getElementById('payment-details-view').classList.remove('d-none');
                startPaymentPolling();
            }, 1000);
        }
        
        function startPaymentPolling() {
            if (paymentCheckInterval) clearInterval(paymentCheckInterval);
            paymentCheckInterval = setInterval(() => {
                fetch('index.php?action=check_payment_status').then(r => r.json()).then(data => {
                    if (data.status === 'PAID') {
                        clearInterval(paymentCheckInterval);
                        document.getElementById('payment-details-view').classList.add('d-none');
                        document.getElementById('payment-waiting-view').classList.add('d-none');
                        document.getElementById('payment-success-view').classList.remove('d-none');

                        // Ambil kontainer untuk menaruh tombol
                        const successActionContainer = document.getElementById('success-action-container');
                        successActionContainer.innerHTML = ''; // Kosongkan dulu

                        // Buat tombol "Kirim Pesan ke Admin"
                        const adminButton = document.createElement('a');
                        adminButton.href = data.whatsapp_url;
                        adminButton.target = '_blank';
                        adminButton.rel = 'noopener noreferrer';
                        adminButton.className = 'btn btn-success btn-lg';
                        adminButton.innerHTML = '<i class="fa-brands fa-whatsapp me-2"></i> Kirim Pesan ke Admin';
                        
                        // Buat tombol "Tutup" untuk menutup modal
                        const closeModalButton = document.createElement('button');
                        closeModalButton.type = 'button';
                        closeModalButton.className = 'btn btn-secondary btn-lg ms-2';
                        closeModalButton.textContent = 'Tutup';
                        closeModalButton.onclick = function() {
                            paymentDetailModal.hide();
                             // Arahkan ke halaman utama setelah modal ditutup
                            window.location.href = 'index.php';
                        };
                        
                        // Tambahkan kedua tombol ke dalam kontainer
                        successActionContainer.appendChild(adminButton);
                        successActionContainer.appendChild(closeModalButton);
                    }
                });
            }, 3000);
        }
        
        document.getElementById('copy-va-btn').addEventListener('click', function() {
            const vaNumber = document.getElementById('va-number').textContent;
            navigator.clipboard.writeText(vaNumber).then(() => {
                copyBtnTooltip.setContent({ '.tooltip-inner': 'Disalin!' });
                setTimeout(() => { copyBtnTooltip.setContent({ '.tooltip-inner': 'Salin Kode' }); }, 2000);
            });
        });
    }
    </script>
</body>
</html>