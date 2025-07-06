<?php
$categories = get_categories($db);
$selected_cat = isset($_GET['category']) ? $_GET['category'] : null;
$products = get_products($db, $selected_cat);
?>
<div class="flex flex-col md:flex-row gap-8">
    <aside class="w-full md:w-1/4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Kategori</h3>
            <ul class="space-y-2">
                <li><a href="index.php" class="block text-gray-700 hover:text-indigo-600 <?php echo !$selected_cat ? 'font-bold text-indigo-600' : ''; ?>">Semua Produk</a></li>
                <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                <li>
                    <a href="index.php?category=<?php echo $cat['id']; ?>" class="block text-gray-700 hover:text-indigo-600 <?php echo ($selected_cat == $cat['id']) ? 'font-bold text-indigo-600' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </aside>
    <div class="w-full md:w-3/4">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Produk Kami</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if(mysqli_num_rows($products) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($products)): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <a href="index.php?page=detail&id=<?php echo $product['id']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($product['image'] ? $product['image'] : 'placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-56 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/600x400/e2e8f0/333?text=Gambar+Produk';">
                        <div class="p-4">
                            <h4 class="text-sm text-gray-500"><?php echo htmlspecialchars($product['category_name']); ?></h4>
                            <h3 class="text-lg font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-xl font-bold text-indigo-600 mt-2"><?php echo format_rupiah($product['price']); ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="col-span-full text-center text-gray-500">Tidak ada produk untuk ditampilkan.</p>
            <?php endif; ?>
        </div>
    </div>
</div>