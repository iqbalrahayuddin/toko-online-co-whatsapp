<?php
require_once 'config.php';

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
$tripayApiUrl = '   https://tripay.co.id/api/transaction/create';

// --- [BARU] Menentukan URL Absolut untuk Meta Tags ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$base_url = "{$protocol}://{$host}{$script_path}";
$meta_image_url = $base_url . '/uploads/' . rawurlencode($app_logo);
$current_url = $protocol . '://' . $host . $_SERVER['REQUEST_URI'];
// --- Akhir Bagian Baru ---

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
// ---

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

// Action handler lain: add, update, remove cart
if ($action === 'add' || $action === 'buy_now') { $product_id = (int)$_POST['product_id']; if (isset($_SESSION['cart'][$product_id])) { $_SESSION['cart'][$product_id]['quantity']++; } else { $_SESSION['cart'][$product_id] = ['id' => $product_id, 'quantity' => 1]; } if ($action === 'buy_now') { header('Location: index.php?page=checkout'); exit(); } header('Location: index.php?status=added'); exit(); }
if ($action === 'update_cart') { $product_id = (int)$_POST['product_id']; $quantity = (int)$_POST['quantity']; if (isset($_SESSION['cart'][$product_id])) { if ($quantity > 0) { $_SESSION['cart'][$product_id]['quantity'] = $quantity; } else { unset($_SESSION['cart'][$product_id]); } } header('Location: index.php?page=cart'); exit(); }
if ($action === 'remove_from_cart') { $product_id = (int)$_GET['id']; if (isset($_SESSION['cart'][$product_id])) { unset($_SESSION['cart'][$product_id]); } header('Location: index.php?page=cart'); exit(); }

// Proses Checkout Utama
if ($action === 'process_checkout' && !empty($_SESSION['cart'])) {
    $nama = htmlspecialchars(trim($_POST['nama'])); $email = htmlspecialchars(trim($_POST['email'])); $whatsapp = htmlspecialchars(trim($_POST['whatsapp'])); $alamat = htmlspecialchars(trim($_POST['alamat']));
    $provinsi = htmlspecialchars(trim($_POST['provinsi_text'])); $kota = htmlspecialchars(trim($_POST['kota_text'])); $kecamatan = htmlspecialchars(trim($_POST['kecamatan_text'])); $kelurahan = htmlspecialchars(trim($_POST['kelurahan_text']));
    $payment_method = $_POST['payment_method'] ?? ''; $kodepos = htmlspecialchars(trim($_POST['kodepos'])); $shipping_details = htmlspecialchars(trim($_POST['shipping_details'])); $shipping_cost = (int)($_POST['shipping_cost'] ?? 0);

    if (empty($nama) || empty($email) || empty($whatsapp) || empty($alamat) || empty($payment_method) || empty($kodepos) || empty($shipping_details) || $shipping_cost <= 0) {
        header('Location: index.php?page=checkout&error=1'); exit();
    }
    
    $subtotal_produk = 0; $order_items = [];
    foreach ($_SESSION['cart'] as $item) { 
        $stmt_checkout = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
        $stmt_checkout->bind_param("i", $item['id']); $stmt_checkout->execute();
        $product = $stmt_checkout->get_result()->fetch_assoc(); $stmt_checkout->close();
        if($product) {
            $subtotal_produk += $product['price'] * $item['quantity']; 
            $order_items[] = ['sku' => 'P' . $item['id'], 'name' => $product['name'], 'price' => $product['price'], 'quantity' => $item['quantity']]; 
        }
    }
    $grand_total = $subtotal_produk + $shipping_cost;

    if ($payment_method === 'COD') {
        $pesan = "Halo *{$app_name}*, saya pesan untuk *BAYAR DI TEMPAT (COD)*:\n\n--- DETAIL PESANAN ---\n";
        foreach ($order_items as $order_item) { $pesan .= "Produk: *{$order_item['name']}* (x{$order_item['quantity']})\n"; }
        $pesan .= "\nSubtotal Produk: " . format_rupiah($subtotal_produk) . "\nPengiriman: {$shipping_details} (" . format_rupiah($shipping_cost) . ")\n";
        $pesan .= "--------------------------\n*TOTAL BAYAR: " . format_rupiah($grand_total) . "*\n--------------------------\n\n*Alamat Pengiriman:*\n{$nama}\n{$whatsapp}\n\n{$alamat}\nKel. {$kelurahan}, Kec. {$kecamatan}\n{$kota}, {$provinsi} {$kodepos}\n\nMohon segera diproses. Terima kasih.";
        $_SESSION['cart'] = []; header("Location: https://api.whatsapp.com/send?phone={$nomor_admin_wa}&text=" . urlencode($pesan)); exit();
    }

    if ($payment_method === 'TRIPAY') {
        $merchantRef = 'INV-' . time(); $order_items[] = ['sku' => 'ONGKIR', 'name' => 'Biaya Pengiriman', 'price' => $shipping_cost, 'quantity' => 1];
        $signature = hash_hmac('sha256', $tripayMerchantCode . $merchantRef . $grand_total, $tripayPrivateKey);
        $data = ['method' => 'BRIVA', 'merchant_ref' => $merchantRef, 'amount' => $grand_total, 'customer_name' => $nama, 'customer_email' => $email, 'customer_phone' => $whatsapp, 'order_items' => $order_items, 'return_url' => 'https://domainanda.com/redirect', 'expired_time' => (time() + (24 * 60 * 60)), 'signature' => $signature ];
        $curl = curl_init(); curl_setopt_array($curl, [ CURLOPT_URL => $tripayApiUrl, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tripayApiKey], CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($data) ]);
        $response = curl_exec($curl); curl_close($curl); $response_data = json_decode($response, true);
        if ($response_data && $response_data['success'] == true) { $_SESSION['cart'] = []; header('Location: ' . $response_data['data']['checkout_url']); } 
        else { header('Location: index.php?page=checkout&error=tripay&msg=' . urlencode($response_data['message'] ?? 'Gateway error.')); }
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
    <meta name="description" content="<?= htmlspecialchars($app_description) ?>">
    
    <meta property="og:title" content="<?= htmlspecialchars($app_name) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($app_description) ?>">
    <meta property="og:image" content="<?= $meta_image_url ?>">
    <meta property="og:url" content="<?= $current_url ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($app_name) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($app_name) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($app_description) ?>">
    <meta name="twitter:image" content="<?= $meta_image_url ?>">
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
        /* Style baru untuk shipping-options-container */
        #shipping-options-container {
            max-height: 220px; /* Perkiraan tinggi untuk 3 item, sesuaikan jika perlu */
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg sticky-top"><div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="uploads/<?= htmlspecialchars($app_logo) ?>" alt="Logo" height="30" class="me-2">
                <?= htmlspecialchars($app_name) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav ms-auto"><li class="nav-item"><a class="nav-link" href="index.php">Produk</a></li><li class="nav-item"><a class="nav-link cart-icon" href="index.php?page=cart"><i class="fa-solid fa-cart-shopping"></i> Keranjang<?php $cart_count = count($_SESSION['cart'] ?? []); if ($cart_count > 0): ?><span class="badge bg-danger cart-badge"><?= $cart_count ?></span><?php endif; ?></a></li></ul></div></div>
        </nav>
    </header>
    <main class="container my-5">
        <?php if ($page === 'home'): ?>
            <div class="row justify-content-center mb-5"><div class="col-md-8 col-lg-6"><form action="index.php" method="get" class="d-flex"><input type="hidden" name="page" value="home"><input type="text" class="form-control form-control-lg" name="q" placeholder="Cari produk..." value="<?= htmlspecialchars($search_query) ?>"><button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-magnifying-glass"></i></button></form></div></div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php if ($products_result->num_rows === 0): ?>
                    <div class="col-12"><div class="alert alert-warning text-center">Produk tidak ditemukan.</div></div>
                <?php else: while($product = $products_result->fetch_assoc()): ?>
                    <div class="col"><div class="card h-100 product-card"><img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"><div class="card-body d-flex flex-column"><h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5><p class="mt-auto fw-bold fs-5 text-primary"><?= format_rupiah($product['price']) ?></p><div class="d-grid gap-2 mt-2"><form action="index.php" method="post" class="d-grid"><input type="hidden" name="product_id" value="<?= $product['id'] ?>"><input type="hidden" name="action" value="buy_now"><button type="submit" class="btn btn-primary">Beli Sekarang</button></form><form action="index.php" method="post" class="d-grid"><input type="hidden" name="product_id" value="<?= $product['id'] ?>"><input type="hidden" name="action" value="add"><button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-cart-plus me-2"></i>Keranjang</button></form></div></div></div></div>
                <?php endwhile; endif; ?>
            </div>
        <?php elseif ($page === 'cart'): ?>
            <h2 class="mb-4">Keranjang Belanja</h2>
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="alert alert-info text-center"><h4 class="alert-heading">Keranjang Anda kosong!</h4> <p>Silakan kembali berbelanja.</p></div>
            <?php else: ?>
                <div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th class="ps-3">Produk</th><th>Harga</th><th class="text-center">Kuantitas</th><th class="text-end">Subtotal</th><th class="text-center">Aksi</th></tr></thead><tbody>
                <?php 
                $total_harga = 0; 
                foreach ($_SESSION['cart'] as $item): 
                    $stmt_cart = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt_cart->bind_param("i", $item['id']); $stmt_cart->execute();
                    $product = $stmt_cart->get_result()->fetch_assoc(); $stmt_cart->close();
                    if(!$product) continue;
                    $subtotal = $product['price'] * $item['quantity']; $total_harga += $subtotal;
                ?>
                <tr>
                    <td class="ps-3"><div class="d-flex align-items-center"><img src="uploads/<?= htmlspecialchars($product['image']) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;" alt="<?= htmlspecialchars($product['name']) ?>"><div class="ms-3 fw-bold"><?= htmlspecialchars($product['name']) ?></div></div></td>
                    <td><?= format_rupiah($product['price']) ?></td>
                    <td><form action="index.php" method="post" class="d-flex justify-content-center"><input type="hidden" name="action" value="update_cart"><input type="hidden" name="product_id" value="<?= $item['id'] ?>"><input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm" style="width:70px;" onchange="this.form.submit()"></form></td>
                    <td class="text-end fw-bold"><?= format_rupiah($subtotal) ?></td>
                    <td class="text-center"><a href="index.php?action=remove_from_cart&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash-can"></i></a></td>
                </tr>
                <?php endforeach; ?>
                </tbody><tfoot><tr class="table-light"><td colspan="3" class="text-end fw-bold ps-3">Total Belanja</td><td class="text-end fw-bold fs-5 text-primary"><?= format_rupiah($total_harga) ?></td><td></td></tr></tfoot></table></div></div>
                <div class="text-end mt-4"><a href="index.php?page=checkout" class="btn btn-primary btn-lg">Lanjutkan ke Checkout <i class="fa-solid fa-arrow-right ms-2"></i></a></div>
            <?php endif; ?>
        <?php elseif ($page === 'checkout'):
            if (empty($_SESSION['cart'])) { echo "<script>window.location.href = 'index.php?page=cart';</script>"; exit(); }
            $subtotal_produk_checkout = 0;
            foreach ($_SESSION['cart'] as $item) { 
                $stmt_checkout_sub = $mysqli->prepare("SELECT price FROM products WHERE id = ?");
                $stmt_checkout_sub->bind_param("i", $item['id']); $stmt_checkout_sub->execute();
                $product_price = $stmt_checkout_sub->get_result()->fetch_assoc()['price'] ?? 0;
                $subtotal_produk_checkout += $product_price * $item['quantity']; 
                $stmt_checkout_sub->close();
            }
        ?>
            <div class="row g-5">
                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="mb-3"><span class="text-primary">Ringkasan</span></h4>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between"><span>Subtotal Produk</span><strong><?= format_rupiah($subtotal_produk_checkout) ?></strong></li>
                        <li id="shipping-cost-summary" class="list-group-item d-flex justify-content-between d-none"><span>Ongkos Kirim</span><strong id="shipping-cost-text">-</strong></li>
                        <li class="list-group-item d-flex justify-content-between bg-light fs-5"><span class="fw-bold">Grand Total</span><strong id="grand-total-text" data-subtotal="<?= $subtotal_produk_checkout ?>"><?= format_rupiah($subtotal_produk_checkout) ?></strong></li>
                    </ul>
                </div>
                <div class="col-md-7 col-lg-8">
                    <h4 class="mb-3">Detail Pesanan</h4>
                    <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><strong>Error!</strong> <?php if ($_GET['error'] === 'tripay') { echo htmlspecialchars(urldecode($_GET['msg'])); } else { echo 'Harap lengkapi semua data, termasuk memilih opsi pengiriman.'; } ?></div><?php endif; ?>
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="process_checkout"><input type="hidden" name="provinsi_text" id="provinsi_text"><input type="hidden" name="kota_text" id="kota_text"><input type="hidden" name="kecamatan_text" id="kecamatan_text"><input type="hidden" name="kelurahan_text" id="kelurahan_text"><input type="hidden" name="kodepos" id="kodepos-input"><input type="hidden" name="shipping_cost" id="shipping_cost_input" value="0"><input type="hidden" name="shipping_details" id="shipping_details_input">
                        <div class="row g-3">
                            <div class="col-sm-6"><label for="nama" class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="nama" name="nama" required></div>
                            <div class="col-sm-6"><label for="whatsapp" class="form-label">Nomor WhatsApp</label><div class="input-group"><span class="input-group-text">+62</span><input type="tel" class="form-control" id="whatsapp" name="whatsapp" required></div></div>
                            <div class="col-12"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" required></div>
                            <div class="col-sm-6"><label for="provinsi" class="form-label">Provinsi</label><select class="form-select" id="provinsi" required><option value="">Memuat...</option></select></div>
                            <div class="col-sm-6"><label for="kota" class="form-label">Kota/Kabupaten</label><select class="form-select" id="kota" required disabled></select></div>
                            <div class="col-sm-6"><label for="kecamatan" class="form-label">Kecamatan</label><select class="form-select" id="kecamatan" required disabled></select></div>
                            <div class="col-sm-6"><label for="kelurahan" class="form-label">Kelurahan/Desa</label><select class="form-select" id="kelurahan" required disabled></select></div>
                            <div class="col-12"><label for="alamat" class="form-label">Alamat Lengkap</label><textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW" required></textarea></div>
                            <div id="kodepos-container" class="col-12 mt-2 d-none"><span class="fw-medium">Kode Pos:</span> <span id="kodepos-result" class="badge fs-6"></span></div>
                        </div>
                        <hr class="my-4">
                        <div id="shipping-section" class="d-none">
                            <h4 class="mb-3">Opsi Pengiriman</h4>
                            <div class="text-center" id="shipping-loader"><div class="spinner-border text-primary"></div><p>Mencari ongkir...</p></div>
                            <div id="shipping-options-container" class="vstack gap-2"></div>
                            <div id="shipping-error" class="alert alert-warning d-none"></div>
                        </div>
                        <hr class="my-4">
                        <h4 class="mb-3">Metode Pembayaran</h4>
                        <div class="vstack gap-2">
                            <div class="payment-option"><input id="cod" name="payment_method" type="radio" class="form-check-input" value="COD" required><label class="form-check-label w-100 ms-2" for="cod"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Bayar di Tempat (COD)</label></div>
                            <div class="payment-option"><input id="tripay" name="payment_method" type="radio" class="form-check-input" value="TRIPAY" required><label class="form-check-label w-100 ms-2" for="tripay"><i class="fa-solid fa-credit-card me-2"></i>Transfer Bank / E-Wallet</label></div>
                        </div>
                        <hr class="my-4">
                        <button class="w-100 btn btn-primary btn-lg" type="submit">Proses Pesanan</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer text-center"><p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($app_name) ?>.</p></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    if (document.getElementById('provinsi')) {
        const API_WILAYAH_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        const selectProvinsi = document.getElementById('provinsi'); const selectKota = document.getElementById('kota'); const selectKecamatan = document.getElementById('kecamatan'); const selectKelurahan = document.getElementById('kelurahan');

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
        
        selectKelurahan.addEventListener('change', function() {
            document.getElementById('kelurahan_text').value = this.options[this.selectedIndex].text;
            const kecamatanText = document.getElementById('kecamatan_text').value;
            const kelurahanText = this.options[this.selectedIndex].text;
            if (!kecamatanText || !kelurahanText) return;
            fetchPostalCode(kecamatanText, kelurahanText);
        });

        function fetchPostalCode(kecamatan, kelurahan) {
            const kodeposContainer = document.getElementById('kodepos-container'); const kodeposResult = document.getElementById('kodepos-result');
            kodeposContainer.classList.remove('d-none'); kodeposResult.innerHTML = '<div class="spinner-border spinner-border-sm"></div>';
            const formData = new FormData(); formData.append('action', 'get_kodepos'); formData.append('kecamatan', kecamatan); formData.append('kelurahan', kelurahan);
            fetch('index.php', { method: 'POST', body: formData }).then(r => r.json()).then(data => {
                if (data.success) {
                    kodeposResult.textContent = data.postal_code; kodeposResult.className = 'badge fs-6 bg-success';
                    document.getElementById('kodepos-input').value = data.postal_code;
                    fetchShippingOptions(data.postal_code);
                } else {
                    kodeposResult.textContent = 'Tidak ditemukan'; kodeposResult.className = 'badge fs-6 bg-danger';
                }
            });
        }

        function fetchShippingOptions(postalCode) {
            const shippingSection = document.getElementById('shipping-section'); const loader = document.getElementById('shipping-loader');
            const container = document.getElementById('shipping-options-container'); const errorContainer = document.getElementById('shipping-error');
            shippingSection.classList.remove('d-none'); loader.classList.remove('d-none'); container.innerHTML = ''; errorContainer.classList.add('d-none');
            const formData = new FormData(); formData.append('action', 'calculate_ongkir'); formData.append('destination', postalCode);
            fetch('index.php', { method: 'POST', body: formData }).then(r => r.json()).then(data => {
                loader.classList.add('d-none');
                if (data.meta.status === 'success' && data.data.length > 0) {
                    
                    // --- PERUBAHAN DIMULAI DI SINI ---
                    // 1. Urutkan data berdasarkan harga (cost) termurah
                    const sortedOptions = data.data.sort((a, b) => a.cost - b.cost);
                    
                    sortedOptions.forEach(option => {
                        const costValue = option.cost; const etd = option.etd; const id = `${option.code}-${option.service.replace(/\s+/g, '-')}`;
                        const details = `${option.name} - ${option.service}`;
                        container.innerHTML += `<div class="shipping-option"><input type="radio" class="form-check-input" name="shipping_option" id="${id}" value="${costValue}" data-details="${details}" required><label class="form-check-label w-100 ms-2" for="${id}">${details} (${etd}) - <strong>${new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(costValue)}</strong></label></div>`;
                    });
                    // --- PERUBAHAN SELESAI DI SINI ---
                    
                    addShippingEventListeners();
                } else {
                    errorContainer.innerText = data.message || 'Tidak ada opsi pengiriman ke tujuan ini.';
                    errorContainer.classList.remove('d-none');
                }
            });
        }

        function addShippingEventListeners() {
            document.querySelectorAll('input[name="shipping_option"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const subtotal = parseFloat(document.getElementById('grand-total-text').dataset.subtotal);
                    const shippingCost = parseFloat(this.value);
                    const grandTotal = subtotal + shippingCost;
                    const shippingDetails = this.dataset.details;
                    document.getElementById('shipping-cost-summary').classList.remove('d-none');
                    document.getElementById('shipping-cost-text').innerText = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(shippingCost);
                    document.getElementById('grand-total-text').innerText = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(grandTotal);
                    document.getElementById('shipping_cost_input').value = shippingCost;
                    document.getElementById('shipping_details_input').value = shippingDetails;
                });
            });
        }
    }
    </script>
</body>
</html>