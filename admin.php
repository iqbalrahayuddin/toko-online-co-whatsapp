<?php
require_once 'config.php';

// --- LOGIC LOGIN ---
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['loggedin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $login_error = "Username atau password salah!";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Jika belum login, tampilkan form login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Admin</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: 'Poppins', sans-serif; }</style>
    </head>
    <body class="bg-gray-100 flex items-center justify-center h-screen">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Login</h2>
            <?php if (isset($login_error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $login_error; ?></span>
                </div>
            <?php endif; ?>
            <form method="POST" action="admin.php">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" id="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" name="login" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit; // Hentikan eksekusi skrip selanjutnya
}

// --- LOGIC CRUD ---
$page = isset($_GET['page']) ? $_GET['page'] : 'settings';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- PENGATURAN ---
    if (isset($_POST['update_settings'])) {
        $store_name_post = mysqli_real_escape_string($db, $_POST['store_name']);
        $whatsapp_number_post = mysqli_real_escape_string($db, $_POST['whatsapp_number']);
        $store_address_post = mysqli_real_escape_string($db, $_POST['store_address']);
        $shipping_cost_post = mysqli_real_escape_string($db, $_POST['shipping_cost_per_kg']);

        mysqli_query($db, "UPDATE settings SET setting_value = '$store_name_post' WHERE setting_key = 'store_name'");
        mysqli_query($db, "UPDATE settings SET setting_value = '$whatsapp_number_post' WHERE setting_key = 'whatsapp_number'");
        mysqli_query($db, "UPDATE settings SET setting_value = '$store_address_post' WHERE setting_key = 'store_address'");
        mysqli_query($db, "UPDATE settings SET setting_value = '$shipping_cost_post' WHERE setting_key = 'shipping_cost_per_kg'");
        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Pengaturan berhasil diperbarui.</div>';
        // Refresh settings variable
        $settings_query = mysqli_query($db, "SELECT * FROM settings");
        while($row = mysqli_fetch_assoc($settings_query)){ $settings[$row['setting_key']] = $row['setting_value']; }
    }

    // --- KATEGORI ---
    if (isset($_POST['save_category'])) {
        $name = mysqli_real_escape_string($db, $_POST['name']);
        if ($id) { // Update
            mysqli_query($db, "UPDATE categories SET name='$name' WHERE id=$id");
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Kategori berhasil diperbarui.</div>';
        } else { // Insert
            mysqli_query($db, "INSERT INTO categories (name) VALUES ('$name')");
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Kategori berhasil ditambahkan.</div>';
        }
        $page = 'categories'; $action = 'view';
    }

    // --- PRODUK ---
    if (isset($_POST['save_product'])) {
        $name = mysqli_real_escape_string($db, $_POST['name']);
        $category_id = (int)$_POST['category_id'];
        $price = (int)$_POST['price'];
        $weight = (int)$_POST['weight'];
        $stock = (int)$_POST['stock'];
        $description = mysqli_real_escape_string($db, $_POST['description']);
        $image_name = $_POST['current_image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "uploads/";
            $image_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        if ($id) { // Update
            $sql = "UPDATE products SET name='$name', category_id=$category_id, price=$price, weight=$weight, stock=$stock, description='$description', image='$image_name' WHERE id=$id";
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Produk berhasil diperbarui.</div>';
        } else { // Insert
            $sql = "INSERT INTO products (name, category_id, price, weight, stock, description, image) VALUES ('$name', $category_id, $price, $weight, $stock, '$description', '$image_name')";
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Produk berhasil ditambahkan.</div>';
        }
        mysqli_query($db, $sql);
        $page = 'products'; $action = 'view';
    }
}

// Handle Delete Actions
if ($action === 'delete') {
    if ($page === 'categories' && $id) {
        mysqli_query($db, "DELETE FROM categories WHERE id=$id");
        $message = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">Kategori berhasil dihapus.</div>';
        $action = 'view';
    }
    if ($page === 'products' && $id) {
        // Optional: delete image file from server
        $res = mysqli_query($db, "SELECT image FROM products WHERE id=$id");
        $row = mysqli_fetch_assoc($res);
        if ($row['image'] && file_exists('uploads/' . $row['image'])) {
            unlink('uploads/' . $row['image']);
        }
        mysqli_query($db, "DELETE FROM products WHERE id=$id");
        $message = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">Produk berhasil dihapus.</div>';
        $action = 'view';
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo htmlspecialchars($store_name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen bg-gray-200">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-xl font-bold">Admin Panel</h2>
            <span class="text-sm text-gray-400"><?php echo htmlspecialchars($store_name); ?></span>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="?page=settings" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700 <?php echo $page == 'settings' ? 'bg-gray-900' : ''; ?>">Pengaturan</a>
            <a href="?page=categories" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700 <?php echo $page == 'categories' ? 'bg-gray-900' : ''; ?>">Kategori</a>
            <a href="?page=products" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700 <?php echo $page == 'products' ? 'bg-gray-900' : ''; ?>">Produk</a>
        </nav>
        <div class="px-6 py-4 border-t border-gray-700">
            <a href="?action=logout" class="w-full text-left flex items-center px-4 py-2 rounded-md hover:bg-red-500">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-md px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <?php 
                if($page == 'settings') echo 'Pengaturan Aplikasi';
                if($page == 'categories') echo 'Manajemen Kategori';
                if($page == 'products') echo 'Manajemen Produk';
                ?>
            </h1>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <?php echo $message; ?>
            <?php
            // Simple Router for Admin Pages
            if ($page === 'settings') {
                // --- HALAMAN PENGATURAN ---
                ?>
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <form method="POST">
                        <div class="space-y-6">
                            <div>
                                <label for="store_name" class="block text-sm font-medium text-gray-700">Nama Toko</label>
                                <input type="text" name="store_name" id="store_name" value="<?php echo htmlspecialchars($settings['store_name']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                                <input type="text" name="whatsapp_number" id="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" placeholder="Gunakan format 628...">
                                <p class="text-xs text-gray-500 mt-1">Wajib diawali dengan 62 (contoh: 6281234567890)</p>
                            </div>
                            <div>
                                <label for="store_address" class="block text-sm font-medium text-gray-700">Alamat Toko (Asal Pengiriman)</label>
                                <textarea name="store_address" id="store_address" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($settings['store_address']); ?></textarea>
                            </div>
                             <div>
                                <label for="shipping_cost_per_kg" class="block text-sm font-medium text-gray-700">Tarif Ongkir per KG (Simulasi)</label>
                                <input type="number" name="shipping_cost_per_kg" id="shipping_cost_per_kg" value="<?php echo htmlspecialchars($settings['shipping_cost_per_kg']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                <p class="text-xs text-gray-500 mt-1">Ini adalah tarif dasar per kg untuk simulasi ongkos kirim.</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" name="update_settings" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>
                <?php
            } elseif ($page === 'categories') {
                if ($action === 'view') {
                    // --- TAMPILAN KATEGORI ---
                    ?>
                    <div class="mb-6">
                        <a href="?page=categories&action=add" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Tambah Kategori Baru</a>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $cats = mysqli_query($db, "SELECT * FROM categories ORDER BY id DESC");
                                while($cat = mysqli_fetch_assoc($cats)):
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $cat['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="?page=categories&action=edit&id=<?php echo $cat['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <a href="?page=categories&action=delete&id=<?php echo $cat['id']; ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } elseif ($action === 'add' || $action === 'edit') {
                    // --- FORM KATEGORI ---
                    $cat_data = ['name' => ''];
                    if ($id) {
                        $res = mysqli_query($db, "SELECT * FROM categories WHERE id=$id");
                        $cat_data = mysqli_fetch_assoc($res);
                    }
                    ?>
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4"><?php echo $id ? 'Edit' : 'Tambah'; ?> Kategori</h3>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($cat_data['name']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mt-6">
                                <button type="submit" name="save_category" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                                <a href="?page=categories" class="ml-4 text-gray-600">Batal</a>
                            </div>
                        </form>
                    </div>
                    <?php
                }
            } elseif ($page === 'products') {
                if ($action === 'view') {
                    // --- TAMPILAN PRODUK ---
                    ?>
                     <div class="mb-6">
                        <a href="?page=products&action=add" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Tambah Produk Baru</a>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $prods = mysqli_query($db, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
                                while($prod = mysqli_fetch_assoc($prods)):
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img src="uploads/<?php echo htmlspecialchars($prod['image'] ? $prod['image'] : 'placeholder.png'); ?>" alt="" class="w-16 h-16 object-cover rounded" onerror="this.onerror=null;this.src='https://placehold.co/100x100/e2e8f0/333?text=N/A';">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900"><?php echo htmlspecialchars($prod['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500"><?php echo htmlspecialchars($prod['category_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500"><?php echo format_rupiah($prod['price']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500"><?php echo $prod['stock']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="?page=products&action=edit&id=<?php echo $prod['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <a href="?page=products&action=delete&id=<?php echo $prod['id']; ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } elseif ($action === 'add' || $action === 'edit') {
                    // --- FORM PRODUK ---
                    $prod_data = ['name' => '', 'category_id' => '', 'price' => '', 'weight' => '', 'stock' => '', 'description' => '', 'image' => ''];
                    if ($id) {
                        $res = mysqli_query($db, "SELECT * FROM products WHERE id=$id");
                        $prod_data = mysqli_fetch_assoc($res);
                    }
                    $cats = mysqli_query($db, "SELECT * FROM categories");
                    ?>
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4"><?php echo $id ? 'Edit' : 'Tambah'; ?> Produk</h3>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($prod_data['name']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                                </div>
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select name="category_id" id="category_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                                        <?php while($cat = mysqli_fetch_assoc($cats)): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $prod_data['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                                    <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($prod_data['price']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                                </div>
                                <div>
                                    <label for="weight" class="block text-sm font-medium text-gray-700">Berat (gram)</label>
                                    <input type="number" name="weight" id="weight" value="<?php echo htmlspecialchars($prod_data['weight']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                                </div>
                                 <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                                    <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($prod_data['stock']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <textarea name="description" id="description" rows="5" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($prod_data['description']); ?></textarea>
                                </div>
                                <div>
                                    <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                                    <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($prod_data['image']); ?>">
                                    <?php if($id && $prod_data['image']): ?>
                                    <div class="mt-2">
                                        <img src="uploads/<?php echo htmlspecialchars($prod_data['image']); ?>" class="w-32 h-32 object-cover rounded">
                                        <p class="text-xs text-gray-500">Gambar saat ini. Upload baru untuk mengganti.</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" name="save_product" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan Produk</button>
                                <a href="?page=products" class="ml-4 text-gray-600">Batal</a>
                            </div>
                        </form>
                    </div>
                    <?php
                }
            }
            ?>
        </main>
    </div>
</div>

</body>
</html>
