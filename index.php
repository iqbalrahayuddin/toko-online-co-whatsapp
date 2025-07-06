<?php
//======================================================================
// BAGIAN 1: LOGIKA PHP & MANAJEMEN KERANJANG
//======================================================================
session_start();

// --- Konfigurasi Dasar ---
$nama_toko = 'TokoKita';
$nomor_admin_wa = '6281234567890'; // Untuk notifikasi COD via WhatsApp

// --- KONFIGURASI TRIPAY (WAJIB DIISI!) ---
// Ganti dengan kredensial Tripay Anda. Dapatkan dari dashboard Tripay.
$tripayApiKey       = 'YJYwaY2JU9i307C6jLoUR1cJL3JkhnLdWGkCMSX3'; // Ganti dengan API Key Anda
$tripayPrivateKey   = '3MZsL-DmOmf-wLKuB-GcIw9-2hU3x'; // Ganti dengan Private Key Anda
$tripayMerchantCode = 'T37380';            // Ganti dengan Kode Merchant Anda
$tripayApiUrl       = 'https://tripay.co.id/api/transaction/create'; // URL Sandbox

// --- Inisialisasi Keranjang ---
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- Data Produk Dummy ---
$products_all = [
    1 => ['name' => 'Kamera Mirrorless Alpha', 'price' => 9500000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=Kamera', 'category' => 'Elektronik', 'rating' => 5],
    2 => ['name' => 'Laptop Gaming Legion', 'price' => 18200000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=Laptop', 'category' => 'Elektronik', 'rating' => 5],
    3 => ['name' => 'Sepatu Lari Ultraboost', 'price' => 2800000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=Sepatu', 'category' => 'Fashion', 'rating' => 4],
    4 => ['name' => 'T-Shirt Basic Cotton', 'price' => 150000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=T-Shirt', 'category' => 'Fashion', 'rating' => 4],
    5 => ['name' => 'Jam Tangan Elegan', 'price' => 1200000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=Jam', 'category' => 'Aksesoris', 'rating' => 5],
    6 => ['name' => 'Buku "Seni Berpikir"', 'price' => 98000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=Buku', 'category' => 'Buku', 'rating' => 5],
    7 => ['name' => 'Meja Kerja Minimalis', 'price' => 850000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=Meja', 'category' => 'Furnitur', 'rating' => 4],
    8 => ['name' => 'Lampu Belajar LED', 'price' => 250000, 'image' => 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=Lampu', 'category' => 'Furnitur', 'rating' => 4],
];

// --- Logika Pencarian ---
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = $products_all;
if (!empty($search_query)) {
    $products = array_filter($products_all, function($product) use ($search_query) {
        return stripos($product['name'], $search_query) !== false;
    });
}

// --- Fungsi Bantuan ---
function format_rupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// --- Logika Penanganan Aksi (Action Handler) ---
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ... (Logika add, update, remove cart tetap sama)
if ($action === 'add' || $action === 'buy_now') { $product_id = (int)$_POST['product_id']; if (isset($products_all[$product_id])) { if (isset($_SESSION['cart'][$product_id])) { $_SESSION['cart'][$product_id]['quantity']++; } else { $_SESSION['cart'][$product_id] = ['id' => $product_id, 'quantity' => 1]; } } if ($action === 'buy_now') { header('Location: index.php?page=checkout'); exit(); } header('Location: index.php?status=added'); exit(); }
if ($action === 'update_cart') { $product_id = (int)$_POST['product_id']; $quantity = (int)$_POST['quantity']; if (isset($_SESSION['cart'][$product_id])) { if ($quantity > 0) { $_SESSION['cart'][$product_id]['quantity'] = $quantity; } else { unset($_SESSION['cart'][$product_id]); } } header('Location: index.php?page=cart'); exit(); }
if ($action === 'remove_from_cart') { $product_id = (int)$_GET['id']; if (isset($_SESSION['cart'][$product_id])) { unset($_SESSION['cart'][$product_id]); } header('Location: index.php?page=cart'); exit(); }

// 4. Aksi Proses Checkout
if ($action === 'process_checkout' && !empty($_SESSION['cart'])) {
    // Validasi data umum
    $nama = htmlspecialchars(trim($_POST['nama']));
    $email = htmlspecialchars(trim($_POST['email']));
    $whatsapp = htmlspecialchars(trim($_POST['whatsapp']));
    $provinsi = htmlspecialchars(trim($_POST['provinsi_text']));
    $kota = htmlspecialchars(trim($_POST['kota_text']));
    $kecamatan = htmlspecialchars(trim($_POST['kecamatan_text']));
    $kelurahan = htmlspecialchars(trim($_POST['kelurahan_text']));
    $alamat = htmlspecialchars(trim($_POST['alamat']));
    $payment_method = $_POST['payment_method'] ?? '';

    // Validasi field
    if (empty($nama) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($whatsapp) || empty($provinsi) || empty($kota) || empty($kecamatan) || empty($kelurahan) || empty($alamat) || empty($payment_method)) {
        header('Location: index.php?page=checkout&error=1');
        exit();
    }
    
    // Hitung total harga dan siapkan item pesanan
    $total_harga = 0;
    $order_items = [];
    foreach ($_SESSION['cart'] as $item) {
        $product = $products_all[$item['id']];
        $subtotal = $product['price'] * $item['quantity'];
        $total_harga += $subtotal;
        $order_items[] = [
            'sku'       => 'P' . $item['id'],
            'name'      => $product['name'],
            'price'     => $product['price'],
            'quantity'  => $item['quantity']
        ];
    }

    // --- LOGIKA BERDASARKAN METODE PEMBAYARAN ---

    // A. Jika Bayar di Tempat (COD)
    if ($payment_method === 'COD') {
        $pesan = "Halo *{$nama_toko}*, saya pesan untuk *BAYAR DI TEMPAT (COD)*:\n\n";
        foreach ($_SESSION['cart'] as $item) {
            $product = $products_all[$item['id']];
            $pesan .= "Produk: *{$product['name']}*\n";
            $pesan .= "Jumlah: {$item['quantity']}\n\n";
        }
        $pesan .= "--------------------------\n";
        $pesan .= "Total Pesanan: *" . format_rupiah($total_harga) . "*\n";
        $pesan .= "--------------------------\n\n";
        $pesan .= "*Data Pemesan:*\n";
        $pesan .= "Nama: {$nama}\nNo. WA: {$whatsapp}\nEmail: {$email}\n\n";
        $pesan .= "*Alamat Pengiriman:*\n{$alamat}\nKel/Desa: {$kelurahan}\nKec: {$kecamatan}\nKota/Kab: {$kota}\nProv: {$provinsi}\n\n";
        $pesan .= "Mohon segera diproses. Terima kasih.";

        $_SESSION['cart'] = []; // Kosongkan keranjang
        $encoded_pesan = urlencode($pesan);
        header("Location: https://api.whatsapp.com/send?phone={$nomor_admin_wa}&text={$encoded_pesan}");
        exit();
    }

    // B. Jika Transfer Online (Tripay)
    if ($payment_method === 'TRIPAY') {
        $merchantRef = 'INV-' . time(); // Referensi unik untuk transaksi
        
        // Membuat signature
        $signature = hash_hmac('sha256', $tripayMerchantCode . $merchantRef . $total_harga, $tripayPrivateKey);

        $data = [
            'method'         => 'BRIVA', // Contoh: bisa diganti dengan channel lain
            'merchant_ref'   => $merchantRef,
            'amount'         => $total_harga,
            'customer_name'  => $nama,
            'customer_email' => $email,
            'customer_phone' => $whatsapp,
            'order_items'    => $order_items,
            'callback_url'   => 'https://domainanda.com/callback', // Ganti dengan URL callback Anda
            'return_url'     => 'https://domainanda.com/redirect',   // Ganti dengan URL redirect Anda
            'expired_time'   => (time() + (24 * 60 * 60)), // 24 jam
            'signature'      => $signature
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $tripayApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tripayApiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data)
        ]);
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $response_data = json_decode($response, true);

        if ($response_data && $response_data['success'] == true) {
            $_SESSION['cart'] = []; // Kosongkan keranjang
            // Redirect ke halaman pembayaran Tripay
            header('Location: ' . $response_data['data']['checkout_url']);
            exit();
        } else {
            // Gagal membuat transaksi Tripay, kembali ke checkout dengan pesan error
            $error_message = $response_data['message'] ?? 'Gagal menghubungi payment gateway.';
            header('Location: index.php?page=checkout&error=tripay&msg=' . urlencode($error_message));
            exit();
        }
    }
}

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!--======================================================================-->
    <!-- BAGIAN 2: HTML HEAD & STYLING                                      -->
    <!--======================================================================-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nama_toko) ?> - Toko Online Sederhana</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.1); background-color: #ffffff; }
        .navbar-brand { font-weight: 700; color: #333 !important; }
        .product-card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,.05); transition: transform .3s ease, box-shadow .3s ease; overflow: hidden; border: none; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,.1); }
        .product-card img { aspect-ratio: 4 / 3; object-fit: cover; }
        .product-title { font-weight: 600; color: #333; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-price { font-weight: 700; color: #0d6efd; font-size: 1.2rem; }
        .footer { background-color: #343a40; color: white; padding: 2rem 0; margin-top: 4rem; }
        .cart-icon { position: relative; }
        .cart-badge { position: absolute; top: -5px; right: -10px; padding: 0.25em 0.5em; font-size: 0.7rem; font-weight: bold; border-radius: 50rem; }
        .form-check-input:checked { background-color: #0d6efd; border-color: #0d6efd; }
        .form-check-label { cursor: pointer; }
        .payment-option { border: 1px solid #dee2e6; border-radius: .375rem; padding: 1rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; }
        .payment-option:has(.form-check-input:checked) { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25); }
    </style>
</head>
<body>
    <!--======================================================================-->
    <!-- BAGIAN 3: BODY & HEADER                                            -->
    <!--======================================================================-->
    <header>
        <nav class="navbar navbar-expand-lg sticky-top">
            <div class="container">
                <a class="navbar-brand" href="index.php"><i class="fa-solid fa-store me-2"></i><?= htmlspecialchars($nama_toko) ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link <?= ($page == 'home') ? 'active' : '' ?>" href="index.php">Produk</a></li>
                        <li class="nav-item">
                            <a class="nav-link cart-icon" href="index.php?page=cart">
                                <i class="fa-solid fa-cart-shopping"></i> Keranjang
                                <?php $cart_count = count($_SESSION['cart']); if ($cart_count > 0): ?>
                                    <span class="badge bg-danger cart-badge"><?= $cart_count ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'added'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">Produk berhasil ditambahkan ke keranjang!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <?php
        //======================================================================
        // BAGIAN 4: "ROUTER" UNTUK MENAMPILKAN KONTEN HALAMAN
        //======================================================================
        
        if ($page === 'home'):
        ?>
            <!-- FORM PENCARIAN -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-8 col-lg-6">
                    <form action="index.php" method="get" class="d-flex">
                        <input type="hidden" name="page" value="home">
                        <input type="text" class="form-control form-control-lg" name="q" placeholder="Cari nama produk..." value="<?= htmlspecialchars($search_query) ?>">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>
            <!-- DAFTAR PRODUK -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php if (empty($products)): ?>
                    <div class="col-12"><div class="alert alert-warning text-center">Produk "<?= htmlspecialchars($search_query) ?>" tidak ditemukan.</div></div>
                <?php else: foreach ($products as $id => $product): ?>
                    <div class="col"><div class="card h-100 product-card"><img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>"><div class="card-body d-flex flex-column"><h5 class="card-title product-title" title="<?= htmlspecialchars($product['name']) ?>"><?= htmlspecialchars($product['name']) ?></h5><p class="card-text text-muted small"><?= htmlspecialchars($product['category']) ?></p><div class="rating-stars mb-2"><?php for ($i = 0; $i < 5; $i++): ?><i class="fa-solid fa-star<?= $i < $product['rating'] ? '' : '-regular' ?>"></i><?php endfor; ?></div><p class="product-price mt-auto"><?= format_rupiah($product['price']) ?></p><div class="d-grid gap-2 mt-2"><form action="index.php" method="post" class="d-grid"><input type="hidden" name="product_id" value="<?= $id ?>"><input type="hidden" name="action" value="buy_now"><button type="submit" class="btn btn-primary">Beli Sekarang</button></form><form action="index.php" method="post" class="d-grid"><input type="hidden" name="product_id" value="<?= $id ?>"><input type="hidden" name="action" value="add"><button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-cart-plus me-2"></i>Keranjang</button></form></div></div></div></div>
                <?php endforeach; endif; ?>
            </div>
        <?php
        elseif ($page === 'cart'):
            // KODE HALAMAN KERANJANG (TIDAK BERUBAH)
            include 'pages/cart.php';
        ?>
            <h2 class="mb-4">Keranjang Belanja Anda</h2>
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="alert alert-info text-center"><i class="fa-solid fa-cart-arrow-down fa-3x mb-3"></i><h4 class="alert-heading">Keranjang Anda kosong!</h4><p>Silakan kembali ke halaman produk untuk mulai berbelanja.</p><a href="index.php" class="btn btn-primary">Lihat Produk</a></div>
            <?php else: ?>
                <div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="ps-3">Produk</th><th>Harga</th><th class="text-center">Kuantitas</th><th class="text-end">Subtotal</th><th class="text-center">Aksi</th></tr></thead><tbody>
                <?php $total_harga = 0; foreach ($_SESSION['cart'] as $item): $product = $products_all[$item['id']]; $subtotal = $product['price'] * $item['quantity']; $total_harga += $subtotal; ?>
                <tr><td class="ps-3"><div class="d-flex align-items-center"><img src="<?= htmlspecialchars($product['image']) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;" alt="<?= htmlspecialchars($product['name']) ?>"><div class="ms-3"><div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div></div></div></td><td><?= format_rupiah($product['price']) ?></td><td><form action="index.php" method="post" class="d-flex justify-content-center"><input type="hidden" name="action" value="update_cart"><input type="hidden" name="product_id" value="<?= $item['id'] ?>"><input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm" style="width: 70px;" min="1" onchange="this.form.submit()"></form></td><td class="text-end fw-bold"><?= format_rupiah($subtotal) ?></td><td class="text-center"><a href="index.php?action=remove_from_cart&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus item"><i class="fa-solid fa-trash-can"></i></a></td></tr>
                <?php endforeach; ?>
                </tbody><tfoot><tr class="table-light"><td colspan="3" class="text-end fw-bold ps-3">Total Belanja</td><td class="text-end fw-bold fs-5 text-primary"><?= format_rupiah($total_harga) ?></td><td></td></tr></tfoot></table></div></div>
                <div class="text-end mt-4"><a href="index.php?page=checkout" class="btn btn-primary btn-lg">Lanjutkan ke Checkout <i class="fa-solid fa-arrow-right ms-2"></i></a></div>
            <?php endif; ?>
        <?php
        elseif ($page === 'checkout'):
            if (empty($_SESSION['cart'])) { echo "<script>window.location.href = 'index.php?page=cart';</script>"; exit(); }
        ?>
            <div class="row g-5">
                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3"><span class="text-primary">Ringkasan Pesanan</span><span class="badge bg-primary rounded-pill"><?= count($_SESSION['cart']) ?></span></h4>
                    <ul class="list-group mb-3">
                        <?php $total_harga_checkout = 0; foreach ($_SESSION['cart'] as $item): $product = $products_all[$item['id']]; $subtotal_checkout = $product['price'] * $item['quantity']; $total_harga_checkout += $subtotal_checkout; ?>
                        <li class="list-group-item d-flex justify-content-between lh-sm"><div><h6 class="my-0"><?= htmlspecialchars($product['name']) ?></h6><small class="text-muted">Jumlah: <?= $item['quantity'] ?></small></div><span class="text-muted"><?= format_rupiah($subtotal_checkout) ?></span></li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between bg-light"><span class="fw-bold">Total</span><strong class="text-primary"><?= format_rupiah($total_harga_checkout) ?></strong></li>
                    </ul>
                </div>
                <div class="col-md-7 col-lg-8">
                    <h4 class="mb-3">Detail Pengiriman & Pembayaran</h4>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php if ($_GET['error'] === 'tripay'): ?>
                                <strong>Transaksi Gagal!</strong> <?= htmlspecialchars(urldecode($_GET['msg'] ?? 'Silakan coba lagi.')) ?>
                            <?php else: ?>
                                Semua field wajib diisi dengan benar. Mohon periksa kembali data Anda.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="process_checkout">
                        <input type="hidden" name="provinsi_text" id="provinsi_text"><input type="hidden" name="kota_text" id="kota_text"><input type="hidden" name="kecamatan_text" id="kecamatan_text"><input type="hidden" name="kelurahan_text" id="kelurahan_text">
                        
                        <div class="row g-3">
                            <div class="col-sm-6"><label for="nama" class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="nama" name="nama" required></div>
                            <div class="col-sm-6"><label for="whatsapp" class="form-label">Nomor WhatsApp</label><div class="input-group"><span class="input-group-text">+62</span><input type="tel" class="form-control" id="whatsapp" name="whatsapp" placeholder="81234567890" required></div></div>
                            <div class="col-12"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required></div>
                            <div class="col-sm-6"><label for="provinsi" class="form-label">Provinsi</label><select class="form-select" id="provinsi" required><option value="">Memuat...</option></select></div>
                            <div class="col-sm-6"><label for="kota" class="form-label">Kota/Kabupaten</label><select class="form-select" id="kota" required disabled><option value="">Pilih provinsi</option></select></div>
                            <div class="col-sm-6"><label for="kecamatan" class="form-label">Kecamatan</label><select class="form-select" id="kecamatan" required disabled><option value="">Pilih kota/kab</option></select></div>
                            <div class="col-sm-6"><label for="kelurahan" class="form-label">Kelurahan/Desa</label><select class="form-select" id="kelurahan" required disabled><option value="">Pilih kecamatan</option></select></div>
                            <div class="col-12"><label for="alamat" class="form-label">Alamat Lengkap</label><textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW" required></textarea></div>
                        </div>

                        <hr class="my-4">
                        <h4 class="mb-3">Metode Pembayaran</h4>
                        <div class="my-3">
                            <div class="form-check payment-option mb-2">
                                <input id="cod" name="payment_method" type="radio" class="form-check-input" value="COD" required>
                                <label class="form-check-label w-100" for="cod">
                                    <i class="fa-solid fa-hand-holding-dollar me-2"></i>Bayar di Tempat (COD)
                                    <div class="small text-muted">Pesanan akan dikonfirmasi via WhatsApp.</div>
                                </label>
                            </div>
                            <div class="form-check payment-option">
                                <input id="tripay" name="payment_method" type="radio" class="form-check-input" value="TRIPAY" required>
                                <label class="form-check-label w-100" for="tripay">
                                    <i class="fa-solid fa-credit-card me-2"></i>Transfer Bank / E-Wallet
                                    <div class="small text-muted">Pembayaran online aman via Tripay.</div>
                                </label>
                            </div>
                        </div>
                        <hr class="my-4">
                        <button class="w-100 btn btn-primary btn-lg" type="submit" id="checkout-button">Pilih Metode Pembayaran</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer text-center">
        <div class="container"><p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($nama_toko) ?>.</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Skrip untuk alamat dinamis
    if (document.getElementById('provinsi')) {
        const API_BASE_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        const selectProvinsi = document.getElementById('provinsi');
        const selectKota = document.getElementById('kota');
        const selectKecamatan = document.getElementById('kecamatan');
        const selectKelurahan = document.getElementById('kelurahan');
        const hiddenProvinsi = document.getElementById('provinsi_text');
        const hiddenKota = document.getElementById('kota_text');
        const hiddenKecamatan = document.getElementById('kecamatan_text');
        const hiddenKelurahan = document.getElementById('kelurahan_text');

        function fetchAndPopulate(url, selectElement, defaultOptionText) {
            selectElement.disabled = true;
            selectElement.innerHTML = `<option value="">Memuat...</option>`;
            fetch(url).then(response => response.json()).then(data => {
                selectElement.innerHTML = `<option value="">-- ${defaultOptionText} --</option>`;
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    selectElement.appendChild(option);
                });
                selectElement.disabled = false;
            }).catch(error => { selectElement.innerHTML = `<option value="">Gagal</option>`; });
        }
        
        document.addEventListener('DOMContentLoaded', () => fetchAndPopulate(`${API_BASE_URL}/provinces.json`, selectProvinsi, 'Pilih Provinsi'));
        selectProvinsi.addEventListener('change', () => {
            hiddenProvinsi.value = selectProvinsi.options[selectProvinsi.selectedIndex].text;
            selectKota.disabled = selectKecamatan.disabled = selectKelurahan.disabled = true;
            if (selectProvinsi.value) fetchAndPopulate(`${API_BASE_URL}/regencies/${selectProvinsi.value}.json`, selectKota, 'Pilih Kota/Kab');
        });
        selectKota.addEventListener('change', () => {
            hiddenKota.value = selectKota.options[selectKota.selectedIndex].text;
            selectKecamatan.disabled = selectKelurahan.disabled = true;
            if (selectKota.value) fetchAndPopulate(`${API_BASE_URL}/districts/${selectKota.value}.json`, selectKecamatan, 'Pilih Kecamatan');
        });
        selectKecamatan.addEventListener('change', () => {
            hiddenKecamatan.value = selectKecamatan.options[selectKecamatan.selectedIndex].text;
            selectKelurahan.disabled = true;
            if (selectKecamatan.value) fetchAndPopulate(`${API_BASE_URL}/villages/${selectKecamatan.value}.json`, selectKelurahan, 'Pilih Kelurahan/Desa');
        });
        selectKelurahan.addEventListener('change', () => { hiddenKelurahan.value = selectKelurahan.options[selectKelurahan.selectedIndex].text; });
        
        // Skrip untuk mengubah teks tombol checkout
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const checkoutButton = document.getElementById('checkout-button');
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'COD') {
                    checkoutButton.innerHTML = '<i class="fa-brands fa-whatsapp me-2"></i> Pesan via WhatsApp (COD)';
                } else if (this.value === 'TRIPAY') {
                    checkoutButton.innerHTML = '<i class="fa-solid fa-shield-halved me-2"></i> Lanjutkan ke Pembayaran Online';
                }
            });
        });
    }
    </script>
</body>
</html>