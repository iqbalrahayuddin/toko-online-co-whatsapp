<?php
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id == 0) {
    echo "<p class='text-center text-red-500'>Produk tidak ditemukan.</p>";
    return;
}
$product = get_product_detail($db, $product_id);
if (!$product) {
    echo "<p class='text-center text-red-500'>Produk tidak ditemukan.</p>";
    return;
}
?>
<div class="bg-white p-8 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <img src="uploads/<?php echo htmlspecialchars($product['image'] ? $product['image'] : 'placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-auto rounded-lg shadow-sm" onerror="this.onerror=null;this.src='https://placehold.co/600x400/e2e8f0/333?text=Gambar+Produk';">
        </div>
        <div>
            <h2 class="text-4xl font-bold text-gray-800"><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="text-md text-gray-500 mt-2">Kategori: <span class="font-semibold"><?php echo htmlspecialchars($product['category_name']); ?></span></p>
            <p class="text-4xl font-bold text-indigo-600 my-4"><?php echo format_rupiah($product['price']); ?></p>
            
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-700">Deskripsi Produk</h3>
                <p class="text-gray-600 mt-2 whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <div class="mt-4">
                <p class="text-gray-600">Berat: <span class="font-semibold"><?php echo $product['weight']; ?> gram</span></p>
                <p class="text-gray-600">Stok: <span class="font-semibold"><?php echo $product['stock']; ?></span></p>
            </div>

            <div class="mt-8">
                <a href="index.php?page=checkout&id=<?php echo $product['id']; ?>" class="w-full flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-md text-white whatsapp-button focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.894 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.886-.001 2.267.651 4.383 1.88 6.166l-1.29 4.721 4.793-1.262z"/></svg>
                    Beli Sekarang via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>