<?php
// Sertakan file config
require_once "config.php";

// Fungsi untuk menghapus file gambar
function deleteImage($filename) {
    $filepath = 'uploads/' . $filename;
    // Jangan hapus gambar default
    if ($filename != 'default.png' && file_exists($filepath) && is_writable($filepath)) {
        unlink($filepath);
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
        $sql_select_products = "SELECT image FROM products WHERE category_id = $category_id";
        $result = mysqli_query($link, $sql_select_products);
        while ($row = mysqli_fetch_assoc($result)) {
            deleteImage($row['image']);
        }
        $sql_delete_products = "DELETE FROM products WHERE category_id = $category_id";
        mysqli_query($link, $sql_delete_products);
        
        $sql = "DELETE FROM categories WHERE id = $category_id";
        mysqli_query($link, $sql);
    }

    // Tambah Produk
    if (isset($_POST['add_product'])) {
        $product_name = mysqli_real_escape_string($link, $_POST['product_name']);
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $weight = $_POST['weight'];
        
        $image_name = 'default.png';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $target_dir = "uploads/";
            // Buat nama file unik untuk menghindari tumpang tindih
            $image_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
            $image_name = uniqid('prod_') . '.' . $image_extension;
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
        }

        if (!empty($product_name) && !empty($category_id) && !empty($price)) {
            $sql = "INSERT INTO products (name, category_id, price, stock, weight, image) VALUES ('$product_name', '$category_id', '$price', '$stock', '$weight', '$image_name')";
            mysqli_query($link, $sql);
        }
    }

    // Hapus Produk
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $sql_select = "SELECT image FROM products WHERE id = $product_id";
        $result = mysqli_query($link, $sql_select);
        if ($row = mysqli_fetch_assoc($result)) {
            deleteImage($row['image']);
        }
        $sql = "DELETE FROM products WHERE id = $product_id";
        mysqli_query($link, $sql);
    }
    
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
    <title>Admin Panel - <?php echo $storeConfig['storeName']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; } 
        .card { background-color: white; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); padding: 1.5rem; }
    </style>
</head>
<body>
    <div class="container mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Admin Panel</h1>
            <p class="text-slate-500">Selamat datang di panel admin <?php echo $storeConfig['storeName']; ?></p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Daftar Produk -->
            <div class="lg:col-span-2 space-y-8">
                <div class="card">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 border-b pb-2">Manajemen Produk</h2>
                    <div class="space-y-3 max-h-[40rem] overflow-y-auto pr-2">
                        <?php mysqli_data_seek($products, 0); while($prod = mysqli_fetch_assoc($products)): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-4">
                                <img src="uploads/<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="w-16 h-16 object-cover rounded-md border">
                                <div>
                                    <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($prod['name']); ?></p>
                                    <p class="text-sm text-slate-500"><?php echo htmlspecialchars($prod['category_name']); ?> - Rp <?php echo number_format($prod['price']); ?></p>
                                    <p class="text-xs text-slate-400">Stok: <?php echo $prod['stock']; ?> | Berat: <?php echo $prod['weight']; ?> gr</p>
                                </div>
                            </div>
                            <form action="admin.php" method="post" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                <button type="submit" name="delete_product" class="text-red-500 hover:text-red-700 font-semibold text-sm">Hapus</button>
                            </form>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Form Tambah -->
            <div class="space-y-8">
                <div class="card">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 border-b pb-2">Tambah Produk Baru</h2>
                    <form action="admin.php" method="post" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label for="product_name" class="block text-sm font-medium text-slate-600 mb-1">Nama Produk</label>
                            <input type="text" name="product_name" id="product_name" class="block w-full rounded-md border-slate-300 shadow-sm" required>
                        </div>
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-slate-600 mb-1">Kategori</label>
                            <select name="category_id" id="category_id" class="block w-full rounded-md border-slate-300 shadow-sm" required>
                                <option value="">Pilih Kategori</option>
                                <?php mysqli_data_seek($categories, 0); while($cat = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-slate-600 mb-1">Harga</label>
                                <input type="number" name="price" id="price" class="block w-full rounded-md border-slate-300 shadow-sm" placeholder="50000" required>
                            </div>
                            <div>
                                <label for="stock" class="block text-sm font-medium text-slate-600 mb-1">Stok</label>
                                <input type="number" name="stock" id="stock" class="block w-full rounded-md border-slate-300 shadow-sm" placeholder="10" required>
                            </div>
                        </div>
                        <div>
                             <label for="weight" class="block text-sm font-medium text-slate-600 mb-1">Berat (gram)</label>
                             <input type="number" name="weight" id="weight" class="block w-full rounded-md border-slate-300 shadow-sm" placeholder="100" required>
                        </div>
                        <div>
                            <label for="product_image" class="block text-sm font-medium text-slate-600 mb-1">Gambar Produk</label>
                            <input type="file" name="product_image" id="product_image" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <button type="submit" name="add_product" class="w-full bg-blue-600 text-white font-bold px-4 py-3 rounded-md hover:bg-blue-700 transition-colors">Tambah Produk</button>
                    </form>
                </div>

                <div class="card">
                    <h2 class="text-xl font-semibold text-slate-700 mb-4 border-b pb-2">Manajemen Kategori</h2>
                    <form action="admin.php" method="post" class="mb-6">
                        <label for="category_name" class="block text-sm font-medium text-slate-600 mb-1">Nama Kategori Baru</label>
                        <div class="flex gap-2">
                            <input type="text" name="category_name" id="category_name" class="flex-grow w-full rounded-md border-slate-300 shadow-sm" required>
                            <button type="submit" name="add_category" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-semibold">Add</button>
                        </div>
                    </form>
                    <h3 class="text-lg font-medium text-slate-600 mb-2">Daftar Kategori</h3>
                    <ul class="space-y-2 max-h-48 overflow-y-auto pr-2">
                        <?php mysqli_data_seek($categories, 0); while($cat = mysqli_fetch_assoc($categories)): ?>
                        <li class="flex justify-between items-center p-2 bg-slate-50 rounded-md">
                            <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            <form action="admin.php" method="post" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua produk di dalamnya juga akan terhapus.');">
                                <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" name="delete_category" class="text-red-500 hover:text-red-700 text-sm font-semibold">Hapus</button>
                            </form>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
