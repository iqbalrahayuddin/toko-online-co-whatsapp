<?php
// Pastikan session dimulai di paling atas
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// --- LOGIKA LOGIN SEDERHANA ---
$admin_user = get_setting($mysqli, 'admin_user');
$admin_pass = get_setting($mysqli, 'admin_password');

if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_user && password_verify($_POST['password'], $admin_pass)) { // Gunakan password_verify
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $login_error = "Username atau password salah!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Jika belum login, tampilkan form login
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background-color: #f8f9fa;
                padding: 1rem;
            }
        </style>
    </head>
    <body>
        <div class="card shadow" style="width: 100%; max-width: 22rem;">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Admin Login</h3>
                <?php if (isset($login_error)) echo "<div class='alert alert-danger'>$login_error</div>"; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}
// --- END OF LOGIN LOGIC ---


// --- LOGIKA FORM HANDLING ---
$pesan_sukses = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Update Pengaturan Umum & Integrasi
    if (isset($_POST['update_settings'])) {
        foreach ($_POST as $key => $value) {
            if ($key !== 'update_settings' && $key !== 'admin_password_new') {
                 // Khusus untuk password, hash sebelum disimpan jika ada password baru
                if ($key === 'admin_password' && !empty($value)) {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                // Jangan update password jika kosong
                if ($key === 'admin_password' && empty($value)) {
                    continue;
                }
                $stmt = $mysqli->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->bind_param("ss", $value, $key);
                $stmt->execute();
                $stmt->close();
            }
        }
        // Handle file upload untuk logo & icon
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        foreach(['app_logo', 'app_icon'] as $file_key){
            if(isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0){
                $filename = time() . '_' . basename($_FILES[$file_key]['name']);
                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $filename)) {
                    $stmt = $mysqli->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
                    $stmt->bind_param("ss", $filename, $file_key);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
        $pesan_sukses = "Pengaturan berhasil diperbarui!";
    }

    // 2. Tambah / Update Produk
    if (isset($_POST['save_product'])) {
        $id = $_POST['product_id'];
        $name = $_POST['name']; $description = $_POST['description']; $price = $_POST['price'];
        $weight = $_POST['weight']; $category = $_POST['category']; $current_image = $_POST['current_image'];
        $image_name = $current_image;

        // Handle upload gambar baru
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }

        if (empty($id)) { // Tambah produk baru
            $stmt = $mysqli->prepare("INSERT INTO products (name, description, price, weight, category, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiss", $name, $description, $price, $weight, $category, $image_name);
            $pesan_sukses = "Produk berhasil ditambahkan!";
        } else { // Update produk
            $stmt = $mysqli->prepare("UPDATE products SET name=?, description=?, price=?, weight=?, category=?, image=? WHERE id=?");
            $stmt->bind_param("ssiissi", $name, $description, $price, $weight, $category, $image_name, $id);
            $pesan_sukses = "Produk berhasil diperbarui!";
        }
        $stmt->execute();
        $stmt->close();
    }
}

// 3. Hapus Produk
if (isset($_GET['delete'])) {
    $id_to_delete = (int)$_GET['delete'];
    // Optional: Hapus file gambar dari server
    $stmt_img = $mysqli->prepare("SELECT image FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    if($row_img = $result_img->fetch_assoc()){
        $file_path = 'uploads/' . $row_img['image'];
        if (file_exists($file_path) && !empty($row_img['image'])) {
            unlink($file_path);
        }
    }
    $stmt_img->close();

    $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php?tab=products&status=deleted");
    exit();
}
// --- END OF FORM HANDLING ---

$tab = $_GET['tab'] ?? 'umum';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= htmlspecialchars(get_setting($mysqli, 'app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Tambahkan style untuk menjaga tombol aksi tetap dalam satu baris di mobile */
        .action-buttons {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.5rem;
        }
        /* Style tambahan agar tampilan lebih rapi di mobile */
        .table-responsive .table {
            min-width: 700px; /* Atur lebar minimum tabel */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link <?= $tab === 'umum' ? 'active' : '' ?>" href="?tab=umum">Pengaturan Umum</a></li>
                    <li class="nav-item"><a class="nav-link <?= $tab === 'integrasi' ? 'active' : '' ?>" href="?tab=integrasi">Integrasi</a></li>
                    <li class="nav-item"><a class="nav-link <?= $tab === 'products' ? 'active' : '' ?>" href="?tab=products">Manajemen Produk</a></li>
                </ul>
                <a href="?logout=true" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <?php if ($pesan_sukses): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $pesan_sukses ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
             <div class="alert alert-info alert-dismissible fade show" role="alert">
                Produk berhasil dihapus.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>


        <?php if ($tab === 'umum'): ?>
            <h3><i class="fa-solid fa-gear me-2"></i>Pengaturan Umum</h3><hr>
            <form method="post" enctype="multipart/form-data" class="card card-body">
                <div class="mb-3"><label for="app_name" class="form-label">Nama Aplikasi</label><input type="text" id="app_name" name="app_name" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'app_name')) ?>"></div>
                <div class="mb-3"><label for="app_description" class="form-label">Deskripsi Aplikasi (untuk SEO)</label><textarea id="app_description" name="app_description" class="form-control" rows="3"><?= htmlspecialchars(get_setting($mysqli, 'app_description')) ?></textarea></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="app_logo" class="form-label">Logo Aplikasi</label><input type="file" id="app_logo" name="app_logo" class="form-control"><small class="text-muted">Logo saat ini: <?= htmlspecialchars(get_setting($mysqli, 'app_logo')) ?></small></div>
                    <div class="col-md-6 mb-3"><label for="app_icon" class="form-label">Icon Aplikasi (Favicon)</label><input type="file" id="app_icon" name="app_icon" class="form-control"><small class="text-muted">Icon saat ini: <?= htmlspecialchars(get_setting($mysqli, 'app_icon')) ?></small></div>
                </div>
                <hr><h5 class="mt-4">Pengaturan Akun Admin</h5>
                <div class="row">
                     <div class="col-md-6 mb-3"><label for="admin_user" class="form-label">Username Admin</label><input type="text" id="admin_user" name="admin_user" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'admin_user')) ?>"></div>
                     <div class="col-md-6 mb-3"><label for="admin_password" class="form-label">Password Admin Baru</label><input type="password" id="admin_password" name="admin_password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah"><small class="text-muted">Ganti password lama. Password akan di-hash.</small></div>
                </div>
                <button type="submit" name="update_settings" class="btn btn-primary mt-3">Simpan Pengaturan</button>
            </form>

        <?php elseif ($tab === 'integrasi'): ?>
            <h3><i class="fa-solid fa-code-branch me-2"></i>Pengaturan Integrasi</h3><hr>
            <form method="post" class="card card-body">
                <h5 class="mt-2">WhatsApp</h5>
                <div class="mb-3"><label for="nomor_admin_wa" class="form-label">Nomor WhatsApp Admin</label><input type="text" id="nomor_admin_wa" name="nomor_admin_wa" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'nomor_admin_wa')) ?>"><small class="text-muted">Gunakan format 62, contoh: 628123456789</small></div>
                <hr><h5 class="mt-4">RajaOngkir / Komerce</h5>
                <div class="mb-3"><label for="rajaongkir_api_key" class="form-label">API Key</label><input type="text" id="rajaongkir_api_key" name="rajaongkir_api_key" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'rajaongkir_api_key')) ?>"></div>
                <div class="mb-3"><label for="rajaongkir_origin_id" class="form-label">Origin ID</label><input type="text" id="rajaongkir_origin_id" name="rajaongkir_origin_id" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'rajaongkir_origin_id')) ?>"><small class="text-muted">ID Kecamatan/Subdistrict lokasi toko Anda.</small></div>
                <hr><h5 class="mt-4">Tripay Payment Gateway</h5>
                <div class="mb-3"><label for="tripay_merchant_code" class="form-label">Kode Merchant</label><input type="text" id="tripay_merchant_code" name="tripay_merchant_code" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'tripay_merchant_code')) ?>"></div>
                <div class="mb-3"><label for="tripay_api_key" class="form-label">API Key</label><input type="text" id="tripay_api_key" name="tripay_api_key" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'tripay_api_key')) ?>"></div>
                <div class="mb-3"><label for="tripay_private_key" class="form-label">Private Key</label><input type="text" id="tripay_private_key" name="tripay_private_key" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'tripay_private_key')) ?>"></div>
                <button type="submit" name="update_settings" class="btn btn-primary mt-3">Simpan Pengaturan</button>
            </form>
            
        <?php elseif ($tab === 'products'): ?>
            <h3><i class="fa-solid fa-box-archive me-2"></i>Manajemen Produk</h3><hr>
            <?php
            $edit_product = null;
            if (isset($_GET['edit'])) {
                $id_to_edit = (int)$_GET['edit'];
                $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->bind_param("i", $id_to_edit);
                $stmt->execute();
                $result = $stmt->get_result();
                $edit_product = $result->fetch_assoc();
                $stmt->close();
            }
            ?>
            <div class="card mb-5">
                <div class="card-header">
                    <h5><?= $edit_product ? 'Edit Produk' : 'Tambah Produk Baru' ?></h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= $edit_product['id'] ?? '' ?>">
                        <input type="hidden" name="current_image" value="<?= $edit_product['image'] ?? '' ?>">
                        <div class="mb-3"><label for="name" class="form-label">Nama Produk</label><input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>" required></div>
                        <div class="mb-3"><label for="description" class="form-label">Deskripsi</label><textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="price" class="form-label">Harga</label><div class="input-group"><span class="input-group-text">Rp</span><input type="number" id="price" name="price" class="form-control" value="<?= $edit_product['price'] ?? '' ?>" required></div></div>
                            <div class="col-md-6 mb-3"><label for="weight" class="form-label">Berat</label><div class="input-group"><input type="number" id="weight" name="weight" class="form-control" value="<?= $edit_product['weight'] ?? '' ?>" required><span class="input-group-text">gram</span></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="category" class="form-label">Kategori</label><input type="text" id="category" name="category" class="form-control" value="<?= htmlspecialchars($edit_product['category'] ?? '') ?>"></div>
                            <div class="col-md-6 mb-3"><label for="image" class="form-label">Gambar Produk</label><input type="file" id="image" name="image" class="form-control">
                            <?php if(!empty($edit_product['image'])): ?>
                                <small class="text-muted">Gambar saat ini: <?= htmlspecialchars($edit_product['image']) ?></small>
                            <?php endif; ?>
                            </div>
                        </div>
                        <button type="submit" name="save_product" class="btn btn-primary">Simpan Produk</button>
                        <?php if ($edit_product): ?><a href="?tab=products" class="btn btn-secondary">Batal Edit</a><?php endif; ?>
                    </form>
                </div>
            </div>

            <h4>Daftar Produk</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Berat</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
                        if ($result->num_rows > 0):
                            while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <?php if(!empty($row['image']) && file_exists('uploads/' . $row['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" width="80" class="img-thumbnail">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/80" alt="No Image" width="80" class="img-thumbnail">
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="align-middle"><?= function_exists('format_rupiah') ? format_rupiah($row['price']) : 'Rp ' . number_format($row['price'], 0, ',', '.') ?></td>
                                <td class="align-middle"><?= $row['weight'] ?> gr</td>
                                <td class="align-middle"><?= htmlspecialchars($row['category']) ?></td>
                                <td class="align-middle">
                                    <div class="action-buttons">
                                        <a href="?tab=products&edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="?tab=products&delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus produk ini?')"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada produk.</td>
                            </tr>
                        <?php endif; ?>
                        <?php $result->close(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>