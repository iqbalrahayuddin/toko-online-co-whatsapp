<?php
require_once 'config.php';

// --- LOGIKA LOGIN SEDERHANA ---
$admin_user = get_setting($mysqli, 'admin_user');
$admin_pass = get_setting($mysqli, 'admin_password');

if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
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
    <!DOCTYPE html><html lang="id"><head><title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body {display: flex; align-items: center; justify-content: center; min-height: 100vh; background-color: #f8f9fa;}</style>
    </head><body><div class="card shadow" style="width: 22rem;"><div class="card-body p-4"><h3 class="card-title text-center mb-4">Admin Login</h3>
    <?php if (isset($login_error)) echo "<div class='alert alert-danger'>$login_error</div>"; ?>
    <form method="post"><div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
    <div class="d-grid"><button type="submit" name="login" class="btn btn-primary">Login</button></div></form></div></div></body></html>
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
            if ($key !== 'update_settings') {
                $stmt = $mysqli->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->bind_param("ss", $value, $key);
                $stmt->execute();
                $stmt->close();
            }
        }
        // Handle file upload untuk logo & icon
        $upload_dir = 'uploads/';
        foreach(['app_logo', 'app_icon'] as $file_key){
            if(isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0){
                $filename = basename($_FILES[$file_key]['name']);
                move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $filename);
                $stmt = $mysqli->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->bind_param("ss", $filename, $file_key);
                $stmt->execute();
                $stmt->close();
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
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image_name);
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
    <title>Admin Panel - <?= htmlspecialchars(get_setting($mysqli, 'app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin.php">Admin Panel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link <?= $tab === 'umum' ? 'active' : '' ?>" href="?tab=umum">Pengaturan Umum</a></li>
                    <li class="nav-item"><a class="nav-link <?= $tab === 'integrasi' ? 'active' : '' ?>" href="?tab=integrasi">Integrasi</a></li>
                    <li class="nav-item"><a class="nav-link <?= $tab === 'products' ? 'active' : '' ?>" href="?tab=products">Manajemen Produk</a></li>
                </ul>
                <a href="?logout=true" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <?php if ($pesan_sukses): ?>
            <div class="alert alert-success"><?= $pesan_sukses ?></div>
        <?php endif; ?>

        <?php if ($tab === 'umum'): ?>
            <h3><i class="fa-solid fa-gear me-2"></i>Pengaturan Umum</h3><hr>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label">Nama Aplikasi</label><input type="text" name="app_name" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'app_name')) ?>"></div>
                <div class="mb-3"><label class="form-label">Deskripsi Aplikasi (untuk SEO)</label><textarea name="app_description" class="form-control"><?= htmlspecialchars(get_setting($mysqli, 'app_description')) ?></textarea></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Logo Aplikasi</label><input type="file" name="app_logo" class="form-control"><small class="text-muted">Logo saat ini: <?= htmlspecialchars(get_setting($mysqli, 'app_logo')) ?></small></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Icon Aplikasi (Favicon)</label><input type="file" name="app_icon" class="form-control"><small class="text-muted">Icon saat ini: <?= htmlspecialchars(get_setting($mysqli, 'app_icon')) ?></small></div>
                </div>
                <hr><h5 class="mt-4">Pengaturan Akun Admin</h5>
                <div class="row">
                     <div class="col-md-6 mb-3"><label class="form-label">Username Admin</label><input type="text" name="admin_user" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'admin_user')) ?>"></div>
                     <div class="col-md-6 mb-3"><label class="form-label">Password Admin</label><input type="text" name="admin_password" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'admin_password')) ?>"></div>
                </div>
                <button type="submit" name="update_settings" class="btn btn-primary">Simpan Pengaturan</button>
            </form>

        <?php elseif ($tab === 'integrasi'): ?>
            <h3><i class="fa-solid fa-code-branch me-2"></i>Pengaturan Integrasi</h3><hr>
            <form method="post">
                <h5 class="mt-4">WhatsApp</h5>
                <div class="mb-3"><label class="form-label">Nomor WhatsApp Admin</label><input type="text" name="nomor_admin_wa" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'nomor_admin_wa')) ?>"><small class="text-muted">Gunakan format 62, contoh: 628123456789</small></div>
                <hr><h5 class="mt-4">RajaOngkir / Komerce</h5>
                <div class="mb-3"><label class="form-label">API Key</label><input type="text" name="rajaongkir_api_key" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'rajaongkir_api_key')) ?>"></div>
                <div class="mb-3"><label class="form-label">Origin ID</label><input type="text" name="rajaongkir_origin_id" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'rajaongkir_origin_id')) ?>"><small class="text-muted">ID Kecamatan/Subdistrict lokasi toko Anda.</small></div>
                <hr><h5 class="mt-4">Tripay Payment Gateway</h5>
                <div class="mb-3"><label class="form-label">Kode Merchant</label><input type="text" name="tripay_merchant_code" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'tripay_merchant_code')) ?>"></div>
                <div class="mb-3"><label class="form-label">API Key</label><input type="text" name="tripay_api_key" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'tripay_api_key')) ?>"></div>
                <div class="mb-3"><label class="form-label">Private Key</label><input type="text" name="tripay_private_key" class="form-control" value="<?= htmlspecialchars(get_setting($mysqli, 'tripay_private_key')) ?>"></div>
                <button type="submit" name="update_settings" class="btn btn-primary">Simpan Pengaturan</button>
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
                        <div class="mb-3"><label class="form-label">Nama Produk</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>" required></div>
                        <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control"><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Harga</label><input type="number" name="price" class="form-control" value="<?= $edit_product['price'] ?? '' ?>" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Berat (gram)</label><input type="number" name="weight" class="form-control" value="<?= $edit_product['weight'] ?? '' ?>" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Kategori</label><input type="text" name="category" class="form-control" value="<?= htmlspecialchars($edit_product['category'] ?? '') ?>"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Gambar Produk</label><input type="file" name="image" class="form-control"></div>
                        </div>
                        <button type="submit" name="save_product" class="btn btn-primary">Simpan Produk</button>
                        <?php if ($edit_product): ?><a href="?tab=products" class="btn btn-secondary">Batal Edit</a><?php endif; ?>
                    </form>
                </div>
            </div>

            <h4>Daftar Produk</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Gambar</th><th>Nama</th><th>Harga</th><th>Berat</th><th>Kategori</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php
                        $result = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
                        while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="" width="60"></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= format_rupiah($row['price']) ?></td>
                            <td><?= $row['weight'] ?> gr</td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td>
                                <a href="?tab=products&edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="?tab=products&delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>