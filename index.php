<?php
require_once "config.php";

// Ambil semua kategori
$category_result = mysqli_query($link, "SELECT * FROM categories ORDER BY name ASC");
$categories = [];
while ($row = mysqli_fetch_assoc($category_result)) {
    $categories[] = $row;
}

// Ambil semua produk
$product_result = mysqli_query($link, "SELECT * FROM products");
$products = [];
while ($row = mysqli_fetch_assoc($product_result)) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $storeConfig['storeName']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .fade-in { animation: fadeIn 0.3s ease-out; }
        .fade-out { animation: fadeOut 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes fadeOut { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(0.95); } }
        .modal-open { overflow: hidden; }
        select:disabled { background-color: #f1f5f9; }
    </style>
</head>
<body class="bg-slate-50">

    <div class="container mx-auto max-w-3xl p-4 pb-32">
        <header class="text-center my-8">
            <h1 class="text-4xl font-bold text-slate-800"><?php echo $storeConfig['storeName']; ?></h1>
            <p class="text-slate-500 mt-2">Temukan Kebutuhan Digital Anda Di Sini</p>
        </header>
        
        <div id="category-filters" class="flex flex-wrap justify-center gap-2 mb-8">
            <button class="category-btn px-4 py-2 rounded-full text-sm font-medium transition bg-indigo-600 text-white" data-category-id="all">Semua</button>
            <?php foreach ($categories as $cat): ?>
            <button class="category-btn px-4 py-2 rounded-full text-sm font-medium transition bg-white text-slate-700" data-category-id="<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </button>
            <?php endforeach; ?>
        </div>

        <main id="product-list" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Produk akan di-render oleh JavaScript dari data PHP -->
        </main>
    </div>

    <div id="cart-button" class="fixed bottom-4 right-4 z-40">
        <button class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition-transform transform hover:scale-110">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        </button>
    </div>

    <!-- Modal Keranjang Belanja -->
    <div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div id="cart-modal-content" class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b"><h2 class="text-xl font-semibold">Keranjang Belanja</h2><button id="close-cart-modal-button" class="text-slate-500">&times;</button></div>
            <div class="p-4 overflow-y-auto">
                <div id="cart-items-container"><p class="text-center text-slate-500 py-8">Keranjang kosong.</p></div>
                <div class="mt-6 border-t pt-4"><h3 class="font-semibold mb-2">Data Pengiriman</h3>
                    <div class="space-y-3">
                        <div><label for="customer-name" class="block text-sm font-medium">Nama Lengkap</label><input type="text" id="customer-name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" placeholder="Nama Anda"></div>
                        <div><label for="province" class="block text-sm font-medium">Provinsi</label><select id="province" class="mt-1 block w-full rounded-md border-slate-300"><option value="">Memuat...</option></select></div>
                        <div><label for="regency" class="block text-sm font-medium">Kota/Kabupaten</label><select id="regency" class="mt-1 block w-full rounded-md border-slate-300" disabled><option value="">Pilih Kota/Kab</option></select></div>
                        <div><label for="district" class="block text-sm font-medium">Kecamatan</label><select id="district" class="mt-1 block w-full rounded-md border-slate-300" disabled><option value="">Pilih Kecamatan</option></select></div>
                        <div><label for="village" class="block text-sm font-medium">Desa/Kelurahan</label><select id="village" class="mt-1 block w-full rounded-md border-slate-300" disabled><option value="">Pilih Desa/Kel</option></select></div>
                        <div><label for="customer-address" class="block text-sm font-medium">Detail Alamat</label><textarea id="customer-address" rows="2" class="mt-1 block w-full rounded-md border-slate-300" placeholder="Contoh: Jl. Merdeka No. 5"></textarea></div>
                        <button id="check-ongkir-button" class="w-full text-sm bg-slate-100 px-4 py-2 rounded-md hover:bg-slate-200">Cek Estimasi Ongkir</button>
                        <p class="text-xs text-slate-400 text-center">*) Ongkir adalah estimasi dari Banyuwangi.</p>
                    </div>
                </div>
                <div class="mt-6 border-t pt-4"><h3 class="font-semibold mb-2">Metode Pembayaran</h3>
                    <div class="space-y-3">
                        <select id="payment-method" class="mt-1 block w-full rounded-md border-slate-300">
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="COD">COD (Bayar di Tempat)</option>
                            <option value="Transfer">Transfer Bank</option>
                        </select>
                        <div id="transfer-info" class="hidden p-3 bg-blue-50 text-blue-800 rounded-md text-sm">
                            Silakan transfer ke: <strong><?php echo $storeConfig['paymentInfo']['bank'] . ' - ' . $storeConfig['paymentInfo']['accountNumber']; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t bg-slate-50 mt-auto">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><span id="subtotal-price" class="font-semibold">Rp 0</span></div>
                    <div class="flex justify-between"><span>Ongkos Kirim</span><span id="shipping-cost" class="font-semibold">Rp 0</span></div>
                    <div class="flex justify-between text-base font-bold"><span>Total</span><span id="total-price" class="text-indigo-600">Rp 0</span></div>
                </div>
                <button id="checkout-button" class="mt-4 w-full bg-green-500 text-white font-bold py-3 rounded-lg hover:bg-green-600">Pesan via WhatsApp</button>
            </div>
        </div>
    </div>
    
    <div id="info-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6 text-center">
            <h3 id="info-modal-title" class="text-lg font-semibold mb-2"></h3>
            <div id="info-modal-content" class="text-slate-600 mb-4"></div>
            <button id="info-modal-close-btn" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Tutup</button>
        </div>
    </div>

    <script>
        // Data dari PHP
        const allProducts = <?php echo json_encode($products); ?>;
        const storeConfig = <?php echo json_encode($storeConfig); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            // State
            let cart = [];
            let shippingCost = 0;
            let currentCategory = 'all';

            // DOM Elements
            const productListEl = document.getElementById('product-list');
            const categoryFiltersEl = document.getElementById('category-filters');
            const cartButton = document.getElementById('cart-button');
            const cartModal = document.getElementById('cart-modal');
            const closeModalButton = document.getElementById('close-cart-modal-button');
            const cartCountEl = document.getElementById('cart-count');
            const cartItemsContainer = document.getElementById('cart-items-container');
            const subtotalPriceEl = document.getElementById('subtotal-price');
            const shippingCostEl = document.getElementById('shipping-cost');
            const totalPriceEl = document.getElementById('total-price');
            const checkoutButton = document.getElementById('checkout-button');
            
            // Form Elements
            const customerNameInput = document.getElementById('customer-name');
            const customerAddressInput = document.getElementById('customer-address');
            const provinceSelect = document.getElementById('province');
            const regencySelect = document.getElementById('regency');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');
            const checkOngkirButton = document.getElementById('check-ongkir-button');
            const paymentMethodSelect = document.getElementById('payment-method');
            const transferInfoEl = document.getElementById('transfer-info');

            // Info Modal Elements
            const infoModal = document.getElementById('info-modal');
            const infoModalTitle = document.getElementById('info-modal-title');
            const infoModalContent = document.getElementById('info-modal-content');
            const infoModalCloseBtn = document.getElementById('info-modal-close-btn');

            // Fungsi utilitas
            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            const showInfoModal = (title, content) => {
                infoModalTitle.textContent = title;
                infoModalContent.innerHTML = content;
                infoModal.classList.remove('hidden');
                infoModal.classList.add('flex');
            };
            const hideInfoModal = () => {
                infoModal.classList.add('hidden');
                infoModal.classList.remove('flex');
            };

            // Fungsi render produk
            const renderProducts = () => {
                productListEl.innerHTML = '';
                const filteredProducts = currentCategory === 'all'
                    ? allProducts
                    : allProducts.filter(p => p.category_id == currentCategory);

                if (filteredProducts.length === 0) {
                    productListEl.innerHTML = `<p class="col-span-full text-center text-slate-500">Produk tidak ditemukan.</p>`;
                    return;
                }

                filteredProducts.forEach(product => {
                    const productCard = document.createElement('div');
                    productCard.className = 'bg-white rounded-lg shadow-md overflow-hidden group';
                    productCard.innerHTML = `
                        <img src="uploads/${product.image}" alt="${product.name}" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="font-semibold text-slate-800 truncate">${product.name}</h3>
                            <p class="text-indigo-600 font-bold mt-1">${formatCurrency(product.price)}</p>
                            <button data-product-id="${product.id}" class="add-to-cart-btn w-full mt-3 bg-indigo-50 text-indigo-700 font-semibold py-2 rounded-md hover:bg-indigo-100 text-sm">
                                + Keranjang
                            </button>
                        </div>
                    `;
                    productListEl.appendChild(productCard);
                });
            };

            // Fungsi Keranjang
            const addToCart = (productId) => {
                const product = allProducts.find(p => p.id == productId);
                const itemInCart = cart.find(item => item.id == productId);
                if (itemInCart) {
                    itemInCart.quantity++;
                } else {
                    cart.push({ ...product, quantity: 1 });
                }
                updateCart();
            };
            
            const updateCart = () => {
                renderCartItems();
                updateCartSummary();
                updateCartCount();
            };
            
            const renderCartItems = () => {
                cartItemsContainer.innerHTML = '';
                if (cart.length === 0) {
                    cartItemsContainer.innerHTML = `<p class="text-center text-slate-500 py-8">Keranjang belanja Anda kosong.</p>`;
                    return;
                }
                cart.forEach(item => {
                    const cartItemEl = document.createElement('div');
                    cartItemEl.className = 'flex items-center justify-between py-3 border-b';
                    cartItemEl.innerHTML = `
                        <div class="flex items-center gap-3">
                            <img src="uploads/${item.image}" alt="${item.name}" class="w-16 h-16 rounded-md object-cover">
                            <div>
                                <p class="font-semibold">${item.name}</p>
                                <p class="text-sm text-slate-500">${formatCurrency(item.price)}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border rounded-md">
                                <button data-id="${item.id}" class="quantity-change-btn px-2 py-1">-</button>
                                <span class="px-2">${item.quantity}</span>
                                <button data-id="${item.id}" class="quantity-change-btn px-2 py-1">+</button>
                            </div>
                        </div>
                    `;
                    cartItemsContainer.appendChild(cartItemEl);
                });
            };

            const changeQuantity = (productId, change) => {
                const itemInCart = cart.find(item => item.id == productId);
                if (itemInCart) {
                    itemInCart.quantity += change;
                    if (itemInCart.quantity <= 0) {
                        cart = cart.filter(item => item.id != productId);
                    }
                }
                updateCart();
            };

            const updateCartSummary = () => {
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const total = subtotal + shippingCost;
                subtotalPriceEl.textContent = formatCurrency(subtotal);
                shippingCostEl.textContent = formatCurrency(shippingCost);
                totalPriceEl.textContent = formatCurrency(total);
            };

            const updateCartCount = () => {
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCountEl.textContent = totalItems;
                cartCountEl.classList.toggle('hidden', totalItems === 0);
            };

            // Fungsi Wilayah & Ongkir
            const API_WILAYAH_BASE_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';
            async function fetchAndPopulateSelect(url, selectElement, placeholder) {
                selectElement.innerHTML = `<option value="">Memuat...</option>`;
                selectElement.disabled = true;
                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    selectElement.innerHTML = `<option value="">${placeholder}</option>`;
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        selectElement.appendChild(option);
                    });
                    selectElement.disabled = false;
                } catch (error) {
                    selectElement.innerHTML = `<option value="">Gagal memuat</option>`;
                }
            }

            // Fungsi Checkout
            const handleCheckout = () => {
                const data = {
                    name: customerNameInput.value.trim(),
                    address: customerAddressInput.value.trim(),
                    province: provinceSelect.options[provinceSelect.selectedIndex]?.text,
                    regency: regencySelect.options[regencySelect.selectedIndex]?.text,
                    district: districtSelect.options[districtSelect.selectedIndex]?.text,
                    village: villageSelect.options[villageSelect.selectedIndex]?.text,
                    paymentMethod: paymentMethodSelect.value
                };

                if (cart.length === 0) { showInfoModal('Oops!', 'Keranjang Anda kosong!'); return; }
                if (!data.name || !data.address || !data.province || !data.regency || !data.district || !data.village) { showInfoModal('Oops!', 'Harap lengkapi semua data pelanggan dan alamat.'); return; }
                if (shippingCost === 0) { showInfoModal('Oops!', 'Harap tekan tombol "Cek Estimasi Ongkos Kirim" dulu.'); return; }
                if (!data.paymentMethod) { showInfoModal('Oops!', 'Harap pilih metode pembayaran.'); return; }
                
                let msg = `Halo, ${storeConfig.storeName}. Saya mau pesan:\n\n`;
                cart.forEach(i => { msg += `*${i.name}* (${i.quantity}x) - ${formatCurrency(i.price * i.quantity)}\n`; });
                const sub = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
                msg += `\nSubtotal: *${formatCurrency(sub)}*\nOngkir (Estimasi): *${formatCurrency(shippingCost)}*\nTotal: *${formatCurrency(sub + shippingCost)}*`;
                msg += `\n\n*Metode Pembayaran:*\n${data.paymentMethod}`;
                if (data.paymentMethod === 'Transfer') {
                    msg += ` (${storeConfig.paymentInfo.bank} - ${storeConfig.paymentInfo.accountNumber})`;
                }
                msg += `\n\n*Alamat Pengiriman:*\n${data.name}\n${data.address}\n${data.village}, ${data.district}\n${data.regency}, ${data.province}\n\nTerima kasih!`;
                window.open(`https://wa.me/${storeConfig.whatsappNumber}?text=${encodeURIComponent(msg)}`, '_blank');
            };
            
            // Event Listeners
            categoryFiltersEl.addEventListener('click', (e) => {
                if (e.target.classList.contains('category-btn')) {
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('bg-indigo-600', 'text-white');
                        btn.classList.add('bg-white', 'text-slate-700');
                    });
                    e.target.classList.add('bg-indigo-600', 'text-white');
                    e.target.classList.remove('bg-white', 'text-slate-700');
                    currentCategory = e.target.dataset.categoryId;
                    renderProducts();
                }
            });

            productListEl.addEventListener('click', (e) => {
                if (e.target.classList.contains('add-to-cart-btn')) {
                    addToCart(e.target.dataset.productId);
                }
            });

            cartItemsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('quantity-change-btn')) {
                    const change = e.target.textContent === '+' ? 1 : -1;
                    changeQuantity(e.target.dataset.id, change);
                }
            });

            cartButton.addEventListener('click', () => { cartModal.classList.remove('hidden'); cartModal.classList.add('flex'); });
            closeModalButton.addEventListener('click', () => { cartModal.classList.add('hidden'); cartModal.classList.remove('flex'); });
            infoModalCloseBtn.addEventListener('click', hideInfoModal);
            checkoutButton.addEventListener('click', handleCheckout);
            paymentMethodSelect.addEventListener('change', (e) => { transferInfoEl.classList.toggle('hidden', e.target.value !== 'Transfer'); });

            provinceSelect.addEventListener('change', () => {
                const id = provinceSelect.value;
                regencySelect.innerHTML = '<option value="">Pilih Kota/Kab</option>'; regencySelect.disabled = true;
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>'; districtSelect.disabled = true;
                villageSelect.innerHTML = '<option value="">Pilih Desa/Kel</option>'; villageSelect.disabled = true;
                if (id) { fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/regencies/${id}.json`, regencySelect, 'Pilih Kota/Kab'); }
            });
            regencySelect.addEventListener('change', () => {
                const id = regencySelect.value;
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>'; districtSelect.disabled = true;
                villageSelect.innerHTML = '<option value="">Pilih Desa/Kel</option>'; villageSelect.disabled = true;
                if (id) { fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/districts/${id}.json`, districtSelect, 'Pilih Kecamatan'); }
            });
            districtSelect.addEventListener('change', () => {
                const id = districtSelect.value;
                if (id) { fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/villages/${id}.json`, villageSelect, 'Pilih Desa/Kel'); }
            });

            checkOngkirButton.addEventListener('click', () => {
                const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.text.toUpperCase();
                if (!provinceName || provinceSelect.value === "") { showInfoModal('Oops!', 'Pilih provinsi terlebih dahulu.'); return; }
                const zones = {'JAWA TIMUR':{min:8e3,max:15e3},'JAWA TENGAH':{min:15e3,max:25e3},'DI YOGYAKARTA':{min:15e3,max:25e3},'JAWA BARAT':{min:18e3,max:3e4},'DKI JAKARTA':{min:18e3,max:3e4},BANTEN:{min:18e3,max:3e4},BALI:{min:15e3,max:28e3},'NUSA TENGGARA':{min:3e4,max:5e4},SUMATERA:{min:35e3,max:6e4},KALIMANTAN:{min:4e4,max:65e3},SULAWESI:{min:45e3,max:7e4},MALUKU:{min:5e4,max:8e4},PAPUA:{min:6e4,max:1e5}};
                let zoneKey = Object.keys(zones).find(key => provinceName.includes(key)) || 'default';
                let zone = zones[zoneKey] || { min: 25000, max: 50000 };
                shippingCost = Math.floor(Math.random() * (zone.max - zone.min + 1)) + zone.min;
                updateCartSummary();
                showInfoModal('Estimasi Ongkir', `Estimasi ongkir dari Banyuwangi adalah ${formatCurrency(shippingCost)}.`);
            });

            // Inisialisasi
            renderProducts();
            fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/provinces.json`, provinceSelect, 'Pilih Provinsi');
        });
    </script>
</body>
</html>
