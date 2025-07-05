<?php
// Sertakan file config
require_once "config.php";

// Fungsi untuk menghapus file gambar
function deleteImage($filename) {
    if (file_exists('uploads/' . $filename) && is_writable('uploads/' . $filename)) {
        unlink('uploads/' . $filename);
    }
}

// Logika untuk memproses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tambah Kategori
    if (isset($_POST['add_category'])) {
        $category_name = mysqli_real_escape_string($link, $_POST['category_name']);
        if (!empty($category_name)) {
            $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
            mysqli_query($link, $sql);
        }
    }

    // Hapus Kategori
    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        // Hapus produk terkait dulu
        $sql_select_products = "SELECT image FROM products WHERE category_id = $category_id";
        $result = mysqli_query($link, $sql_select_products);
        while ($row = mysqli_fetch_assoc($result)) {
            deleteImage($row['image']);
        }
        $sql_delete_products = "DELETE FROM products WHERE category_id = $category_id";
        mysqli_query($link, $sql_delete_products);
        
        // Hapus kategori
        $sql = "DELETE FROM categories WHERE id = $category_id";
        mysqli_query($link, $sql);
    }

    // Tambah Produk
    if (isset($_POST['add_product'])) {
        $product_name = mysqli_real_escape_string($link, $_POST['product_name']);
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        
        // Logika Upload Gambar
        $image_name = 'default.png'; // Gambar default jika tidak ada upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $target_dir = "uploads/";
            $image_name = basename($_FILES["product_image"]["name"]);
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
        }

        if (!empty($product_name) && !empty($category_id) && !empty($price)) {
            $sql = "INSERT INTO products (name, category_id, price, image) VALUES ('$product_name', '$category_id', '$price', '$image_name')";
            mysqli_query($link, $sql);
        }
    }

    // Hapus Produk
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        // Dapatkan nama file gambar untuk dihapus
        $sql_select = "SELECT image FROM products WHERE id = $product_id";
        $result = mysqli_query($link, $sql_select);
        if ($row = mysqli_fetch_assoc($result)) {
            deleteImage($row['image']);
        }
        $sql = "DELETE FROM products WHERE id = $product_id";
        mysqli_query($link, $sql);
    }
    
    // Redirect untuk menghindari resubmit form
    header("Location: admin.php");
    exit;
}

// Ambil data dari database untuk ditampilkan
$categories = mysqli_query($link, "SELECT * FROM categories ORDER BY name ASC");
$products = mysqli_query($link, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.name ASC");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $storeConfig['storeName']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-100">
    <div class="container mx-auto max-w-4xl p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Halaman Admin</h1>

        <!-- Manajemen Kategori -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold text-slate-700 mb-4">Manajemen Kategori</h2>
            <!-- Form Tambah Kategori -->
            <form action="admin.php" method="post" class="mb-6 flex gap-4 items-end">
                <div class="flex-grow">
                    <label for="category_name" class="block text-sm font-medium text-slate-600">Nama Kategori Baru</label>
                    <input type="text" name="category_name" id="category_name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                </div>
                <button type="submit" name="add_category" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Tambah</button>
            </form>

            <!-- Daftar Kategori -->
            <h3 class="text-lg font-medium text-slate-600 mb-2">Daftar Kategori</h3>
            <ul class="space-y-2">
                <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                <li class="flex justify-between items-center p-2 bg-slate-50 rounded-md">
                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                    <form action="admin.php" method="post" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua produk di dalamnya juga akan terhapus.');">
                        <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                        <button type="submit" name="delete_category" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                    </form>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Manajemen Produk -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-slate-700 mb-4">Manajemen Produk</h2>
            <!-- Form Tambah Produk -->
            <form action="admin.php" method="post" enctype="multipart/form-data" class="space-y-4 mb-6">
                <div>
                    <label for="product_name" class="block text-sm font-medium text-slate-600">Nama Produk</label>
                    <input type="text" name="product_name" id="product_name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-600">Kategori</label>
                    <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                        <option value="">Pilih Kategori</option>
                        <?php 
                        mysqli_data_seek($categories, 0); // Reset pointer
                        while($cat = mysqli_fetch_assoc($categories)): 
                        ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-slate-600">Harga (contoh: 50000)</label>
                    <input type="number" name="price" id="price" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                </div>
                <div>
                    <label for="product_image" class="block text-sm font-medium text-slate-600">Gambar Produk</label>
                    <input type="file" name="product_image" id="product_image" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                </div>
                <button type="submit" name="add_product" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Tambah Produk</button>
            </form>

            <!-- Daftar Produk -->
            <h3 class="text-lg font-medium text-slate-600 mb-2">Daftar Produk</h3>
            <div class="space-y-3">
                <?php while($prod = mysqli_fetch_assoc($products)): ?>
                <div class="flex items-center justify-between p-2 bg-slate-50 rounded-md">
                    <div class="flex items-center gap-4">
                        <img src="uploads/<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="w-12 h-12 object-cover rounded-md">
                        <div>
                            <p class="font-semibold"><?php echo htmlspecialchars($prod['name']); ?></p>
                            <p class="text-sm text-slate-500"><?php echo htmlspecialchars($prod['category_name']); ?> - Rp <?php echo number_format($prod['price']); ?></p>
                        </div>
                    </div>
                    <form action="admin.php" method="post" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                        <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                        <button type="submit" name="delete_product" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                    </form>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
