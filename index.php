<?php
//======================================================================
// BAGIAN 1: LOGIKA PHP & MANAJEMEN KERANJANG
//======================================================================
session_start();

// --- Konfigurasi Dasar ---
$nomor_admin = '6281234567890'; // Ganti dengan nomor WhatsApp Admin (format 62)
$nama_toko = 'TokoKita';

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
        // Mencari di nama produk (case-insensitive)
        return stripos($product['name'], $search_query) !== false;
    });
}


// --- Fungsi Bantuan ---
function format_rupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// --- Logika Penanganan Aksi (Action Handler) ---
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ... (Sisa dari logika action handler tetap sama seperti sebelumnya)

// 1. Aksi Tambah ke Keranjang atau Beli Sekarang
if ($action === 'add' || $action === 'buy_now') {
    $product_id = (int)$_POST['product_id'];
    if (isset($products_all[$product_id])) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'quantity' => 1
            ];
        }
    }
    if ($action === 'buy_now') {
        header('Location: index.php?page=checkout');
        exit();
    }
    header('Location: index.php?status=added');
    exit();
}

// 2. Aksi Update Kuantitas di Keranjang
if ($action === 'update_cart') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: index.php?page=cart');
    exit();
}

// 3. Aksi Hapus Item dari Keranjang
if ($action === 'remove_from_cart') {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: index.php?page=cart');
    exit();
}

// 4. Aksi Proses Checkout
if ($action === 'process_checkout' && !empty($_SESSION['cart'])) {
    // Validasi data input
    $nama = htmlspecialchars(trim($_POST['nama']));
    $whatsapp = htmlspecialchars(trim($_POST['whatsapp']));
    $provinsi = htmlspecialchars(trim($_POST['provinsi_text']));
    $kota = htmlspecialchars(trim($_POST['kota_text']));
    $kecamatan = htmlspecialchars(trim($_POST['kecamatan_text']));
    $kelurahan = htmlspecialchars(trim($_POST['kelurahan_text']));
    $alamat = htmlspecialchars(trim($_POST['alamat']));

    if (empty($nama) || empty($whatsapp) || empty($provinsi) || empty($kota) || empty($kecamatan) || empty($kelurahan) || empty($alamat)) {
        header('Location: index.php?page=checkout&error=1');
        exit();
    }
    
    // Format pesan WhatsApp
    $pesan = "Halo *{$nama_toko}*, saya ingin memesan:\n\n";
    $total_harga = 0;

    foreach ($_SESSION['cart'] as $item) {
        $product = $products_all[$item['id']];
        $subtotal = $product['price'] * $item['quantity'];
        $total_harga += $subtotal;
        $pesan .= "Produk: *{$product['name']}*\n";
        $pesan .= "Jumlah: {$item['quantity']}\n";
        $pesan .= "Subtotal: " . format_rupiah($subtotal) . "\n\n";
    }

    $pesan .= "--------------------------\n";
    $pesan .= "Total Pesanan: *" . format_rupiah($total_harga) . "*\n\n";
    $pesan .= "--------------------------\n";
    $pesan .= "*Data Pemesan:*\n";
    $pesan .= "Nama: {$nama}\n";
    $pesan .= "No. WhatsApp: {$whatsapp}\n";
    $pesan .= "Alamat: {$alamat}\n";
    $pesan .= "Kel/Desa: {$kelurahan}\n";
    $pesan .= "Kecamatan: {$kecamatan}\n";
    $pesan .= "Kota/Kab: {$kota}\n";
    $pesan .= "Provinsi: {$provinsi}\n\n";
    $pesan .= "Mohon informasikan total biaya beserta ongkos kirim dan nomor rekening untuk pembayaran. Terima kasih.";

    $_SESSION['cart'] = [];
    $encoded_pesan = urlencode($pesan);
    header("Location: https://api.whatsapp.com/send?phone={$nomor_admin}&text={$encoded_pesan}");
    exit();
}

// --- Ambil halaman saat ini ---
$page = $_GET['page'] ?? 'home';

?>
<!DOCTYPE html>
<html lang="id">
<head>
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
        .nav-link { font-weight: 500; }
        .product-card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,.05); transition: transform .3s ease, box-shadow .3s ease; overflow: hidden; border: none; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,.1); }
        .product-card img { aspect-ratio: 4 / 3; object-fit: cover; }
        .product-card .card-body { padding: 1.25rem; }
        .product-title { font-weight: 600; color: #333; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-price { font-weight: 700; color: #0d6efd; font-size: 1.2rem; }
        .rating-stars { color: #ffc107; }
        .btn-custom-primary { background-color: #0d6efd; border-color: #0d6efd; font-weight: 500; transition: background-color .2s ease; }
        .btn-custom-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
        .footer { background-color: #343a40; color: white; padding: 2rem 0; margin-top: 4rem; }
        .cart-icon { position: relative; }
        .cart-badge { position: absolute; top: -5px; right: -10px; padding: 0.25em 0.5em; font-size: 0.7rem; font-weight: bold; border-radius: 50rem; }
        .cart-table img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); border-color: #86b7fe; }
        .spinner-border { width: 1rem; height: 1rem; }
        .search-form .form-control { border-top-right-radius: 0; border-bottom-right-radius: 0; }
        .search-form .btn { border-top-left-radius: 0; border-bottom-left-radius: 0; }
    </style>
</head>
<body>
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
        
        // ----------------- HALAMAN UTAMA (PRODUK) -----------------
        if ($page === 'home'):
        ?>
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">Selamat Datang di <?= htmlspecialchars($nama_toko) ?></h1>
                <p class="lead text-muted">Temukan produk favorit Anda di sini.</p>
            </div>

            <div class="row justify-content-center mb-5">
                <div class="col-md-8 col-lg-6">
                    <form action="index.php" method="get" class="d-flex search-form">
                        <input type="hidden" name="page" value="home">
                        <input type="text" class="form-control form-control-lg" name="q" placeholder="Cari nama produk..." value="<?= htmlspecialchars($search_query) ?>">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php if (empty($products)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="fa-solid fa-box-open fa-2x mb-3"></i>
                            <h4 class="alert-heading">Produk tidak ditemukan</h4>
                            <p>Produk dengan kata kunci "<?= htmlspecialchars($search_query) ?>" tidak dapat ditemukan. Coba kata kunci lain atau <a href="index.php" class="alert-link">lihat semua produk</a>.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $id => $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card">
                            <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title product-title" title="<?= htmlspecialchars($product['name']) ?>"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-muted small"><?= htmlspecialchars($product['category']) ?></p>
                                <div class="rating-stars mb-2">
                                    <?php for ($i = 0; $i < 5; $i++): ?><i class="fa-solid fa-star<?= $i < $product['rating'] ? '' : '-regular' ?>"></i><?php endfor; ?>
                                </div>
                                <p class="product-price mt-auto"><?= format_rupiah($product['price']) ?></p>
                                <div class="d-grid gap-2 mt-2">
                                    <form action="index.php" method="post" class="d-grid">
                                        <input type="hidden" name="product_id" value="<?= $id ?>"><input type="hidden" name="action" value="buy_now">
                                        <button type="submit" class="btn btn-custom-primary">Beli Sekarang</button>
                                    </form>
                                    <form action="index.php" method="post" class="d-grid">
                                        <input type="hidden" name="product_id" value="<?= $id ?>"><input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-cart-plus me-2"></i>Keranjang</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php
        
        // ----------------- HALAMAN KERANJANG -----------------
        elseif ($page === 'cart'):
        ?>
            <h2 class="mb-4">Keranjang Belanja Anda</h2>
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="alert alert-info text-center"><i class="fa-solid fa-cart-arrow-down fa-3x mb-3"></i><h4 class="alert-heading">Keranjang Anda kosong!</h4><p>Silakan kembali ke halaman produk untuk mulai berbelanja.</p><a href="index.php" class="btn btn-primary">Lihat Produk</a></div>
            <?php else: ?>
                <div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0 cart-table"><thead class="table-light"><tr><th class="ps-3">Produk</th><th>Harga</th><th class="text-center">Kuantitas</th><th class="text-end">Subtotal</th><th class="text-center">Aksi</th></tr></thead><tbody>
                <?php $total_harga = 0; foreach ($_SESSION['cart'] as $item): $product = $products_all[$item['id']]; $subtotal = $product['price'] * $item['quantity']; $total_harga += $subtotal; ?>
                <tr>
                    <td class="ps-3"><div class="d-flex align-items-center"><img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"><div class="ms-3"><div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div><div class="text-muted small"><?= htmlspecialchars($product['category']) ?></div></div></div></td>
                    <td><?= format_rupiah($product['price']) ?></td>
                    <td><form action="index.php" method="post" class="d-flex justify-content-center"><input type="hidden" name="action" value="update_cart"><input type="hidden" name="product_id" value="<?= $item['id'] ?>"><input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm" style="width: 70px;" min="1" onchange="this.form.submit()"></form></td>
                    <td class="text-end fw-bold"><?= format_rupiah($subtotal) ?></td>
                    <td class="text-center"><a href="index.php?action=remove_from_cart&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus item"><i class="fa-solid fa-trash-can"></i></a></td>
                </tr>
                <?php endforeach; ?>
                </tbody><tfoot><tr class="table-light"><td colspan="3" class="text-end fw-bold ps-3">Total Belanja</td><td class="text-end fw-bold fs-5 text-primary"><?= format_rupiah($total_harga) ?></td><td></td></tr></tfoot></table></div></div>
                <div class="text-end mt-4"><a href="index.php?page=checkout" class="btn btn-custom-primary btn-lg">Lanjutkan ke Checkout <i class="fa-solid fa-arrow-right ms-2"></i></a></div>
            <?php endif; ?>
        <?php
        
        // ----------------- HALAMAN CHECKOUT -----------------
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
                        <li class="list-group-item d-flex justify-content-between bg-light"><span class="fw-bold">Total (Belum Ongkir)</span><strong class="text-primary"><?= format_rupiah($total_harga_checkout) ?></strong></li>
                    </ul>
                </div>
                <div class="col-md-7 col-lg-8">
                    <h4 class="mb-3">Alamat Pengiriman</h4>
                    <?php if (isset($_GET['error'])): ?><div class="alert alert-danger">Semua field wajib diisi. Mohon lengkapi data Anda.</div><?php endif; ?>
                    <form action="index.php" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="action" value="process_checkout">
                        <input type="hidden" name="provinsi_text" id="provinsi_text"><input type="hidden" name="kota_text" id="kota_text"><input type="hidden" name="kecamatan_text" id="kecamatan_text"><input type="hidden" name="kelurahan_text" id="kelurahan_text">
                        
                        <div class="row g-3">
                            <div class="col-12"><label for="nama" class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Anda" required></div>
                            <div class="col-12"><label for="whatsapp" class="form-label">Nomor WhatsApp</label><div class="input-group"><span class="input-group-text">+62</span><input type="tel" class="form-control" id="whatsapp" name="whatsapp" placeholder="81234567890" required></div></div>
                            <div class="col-12"><label for="provinsi" class="form-label">Provinsi</label><select class="form-select" id="provinsi" required><option value="">Memuat provinsi...</option></select></div>
                            <div class="col-12"><label for="kota" class="form-label">Kota/Kabupaten</label><select class="form-select" id="kota" required disabled><option value="">Pilih provinsi dulu</option></select></div>
                            <div class="col-12"><label for="kecamatan" class="form-label">Kecamatan</label><select class="form-select" id="kecamatan" required disabled><option value="">Pilih kota/kabupaten dulu</option></select></div>
                            <div class="col-12"><label for="kelurahan" class="form-label">Kelurahan/Desa</label><select class="form-select" id="kelurahan" required disabled><option value="">Pilih kecamatan dulu</option></select></div>
                            <div class="col-12"><label for="alamat" class="form-label">Alamat Lengkap</label><textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW, dan patokan" required></textarea></div>
                        </div>
                        <hr class="my-4">
                        <button class="w-100 btn btn-success btn-lg" type="submit"><i class="fa-brands fa-whatsapp me-2"></i> Checkout via WhatsApp</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer text-center">
        <div class="container"><p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($nama_toko) ?>. Dibuat dengan PHP Native & Bootstrap 5.</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    selectElement.innerHTML = `<option value="">-- ${defaultOptionText} --</option>`;
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        selectElement.appendChild(option);
                    });
                    selectElement.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    selectElement.innerHTML = `<option value="">Gagal memuat data</option>`;
                });
        }
        
        // 1. Ambil data Provinsi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            fetchAndPopulate(`${API_BASE_URL}/provinces.json`, selectProvinsi, 'Pilih Provinsi');
        });

        // 2. Event listener untuk Provinsi -> memuat Kota
        selectProvinsi.addEventListener('change', () => {
            hiddenProvinsi.value = selectProvinsi.options[selectProvinsi.selectedIndex].text;
            selectKota.innerHTML = '<option value="">Pilih provinsi dulu</option>';
            selectKecamatan.innerHTML = '<option value="">Pilih kota dulu</option>';
            selectKelurahan.innerHTML = '<option value="">Pilih kecamatan dulu</option>';
            selectKota.disabled = true;
            selectKecamatan.disabled = true;
            selectKelurahan.disabled = true;

            if (selectProvinsi.value) {
                fetchAndPopulate(`${API_BASE_URL}/regencies/${selectProvinsi.value}.json`, selectKota, 'Pilih Kota/Kabupaten');
            }
        });

        // 3. Event listener untuk Kota -> memuat Kecamatan
        selectKota.addEventListener('change', () => {
            hiddenKota.value = selectKota.options[selectKota.selectedIndex].text;
            selectKecamatan.innerHTML = '<option value="">Pilih kota dulu</option>';
            selectKelurahan.innerHTML = '<option value="">Pilih kecamatan dulu</option>';
            selectKecamatan.disabled = true;
            selectKelurahan.disabled = true;

            if (selectKota.value) {
                fetchAndPopulate(`${API_BASE_URL}/districts/${selectKota.value}.json`, selectKecamatan, 'Pilih Kecamatan');
            }
        });
        
        // 4. Event listener untuk Kecamatan -> memuat Kelurahan
        selectKecamatan.addEventListener('change', () => {
            hiddenKecamatan.value = selectKecamatan.options[selectKecamatan.selectedIndex].text;
            selectKelurahan.innerHTML = '<option value="">Pilih kecamatan dulu</option>';
            selectKelurahan.disabled = true;

            if (selectKecamatan.value) {
                fetchAndPopulate(`${API_BASE_URL}/villages/${selectKecamatan.value}.json`, selectKelurahan, 'Pilih Kelurahan/Desa');
            }
        });
        
        // 5. Simpan nama Kelurahan/Desa saat dipilih
        selectKelurahan.addEventListener('change', () => {
             hiddenKelurahan.value = selectKelurahan.options[selectKelurahan.selectedIndex].text;
        });
    }
    </script>
</body>
</html>