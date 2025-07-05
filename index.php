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
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #4f46e5; border-radius: 50%; width: 24px; height: 24px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .chat-bubble { max-width: 80%; width: fit-content; }
        .chat-bubble.user { background-color: #e0e7ff; color: #3730a3; border-radius: 1.25rem 1.25rem 0.25rem 1.25rem; }
        .chat-bubble.ai { background-color: #f1f5f9; color: #334155; border-radius: 1.25rem 1.25rem 1.25rem 0.25rem; }
    </style>
</head>
<body class="bg-slate-50">

    <div class="container mx-auto max-w-4xl p-4 pb-32">
        <header class="text-center my-10">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800"><?php echo $storeConfig['storeName']; ?></h1>
            <p class="text-slate-500 mt-2 text-lg">temukan produk kamu di sini</p>
        </header>
        
        <div id="category-filters" class="flex flex-wrap justify-center gap-2 mb-8">
            <button class="category-btn px-4 py-2 rounded-full text-sm font-semibold transition bg-indigo-600 text-white shadow" data-category-id="all">Semua</button>
            <?php foreach ($categories as $cat): ?>
            <button class="category-btn px-4 py-2 rounded-full text-sm font-semibold transition bg-white text-slate-700" data-category-id="<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </button>
            <?php endforeach; ?>
        </div>

        <main id="product-list" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Produk akan di-render oleh JavaScript -->
        </main>
    </div>

    <!-- Tombol Floating -->
    <div class="fixed bottom-4 right-4 z-40 flex flex-col items-center gap-3">
        <button id="advisor-button" class="bg-purple-600 text-white w-16 h-16 rounded-full shadow-lg hover:bg-purple-700 flex items-center justify-center transition-transform transform hover:scale-110" title="AI Advisor">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3c.3 0 .5.1.8.4l.2.2c.4.4.5.8.5 1.4v.1c0 .6-.2 1.1-.5 1.5l-.2.2c-.3.3-.6.4-1 .4H8c-.7 0-1.3-.3-1.8-.7L6 6.4c-.5-.4-1-1-1.8-1.3l-.4-.2c-.4-.2-.8-.2-1.2 0l-.5.2c-.4.2-.7.6-1 1l-.2.4c-.2.4-.2.9 0 1.3l.2.4c.3.5.8 1 1.3 1.3l.4.2H4c.6 0 1.1.2 1.5.5l.2.2c.4.4.5.9.5 1.5v.2c0 .6-.2 1.1-.5 1.5l-.2.2c-.4.4-.9.5-1.5.5H3c-.6 0-1.1-.2-1.5-.5l-.2-.2c-.4-.4-.5-.9-.5-1.5v-.2c0-.6.2-1.1.5-1.5l.2-.2c.4-.4.9-.5 1.5-.5h.2c.3 0 .6-.1.8-.4l.2-.2c.4-.4.5-.8.5-1.4v-.1c0-.6-.2-1.1-.5-1.5l-.2-.2c-.3-.3-.6-.4-1-.4H3c-.7 0-1.3.3-1.8.7L1 5.6c-.5.4-1 .9-1.8 1.2l-.4.2c-.4.2-.8.2-1.2 0l-.5-.2c-.4-.2-.7-.6-1-1l-.2-.4c-.2.4-.2.9 0-1.3l.2-.4c.3-.5.8-1 1.3-1.3l.4-.2H0c.6 0 1.1.2 1.5.5l.2.2c.4.4.5.9.5 1.5v.2c0 .6-.2 1.1-.5 1.5l-.2.2c-.4.4-.9.5-1.5.5h-.2c-.3 0-.6.1-.8.4l-.2.2c-.4.4-.5.8-.5 1.4v.1c0 .6.2 1.1.5 1.5l.2.2c.3.3.6.4 1 .4h.2c.7 0 1.3.3 1.8.7l.2.2c.5.4 1 .9 1.8 1.2l.4.2c.4.2.8.2 1.2 0l.5-.2c.4-.2.7-.6 1-1l.2-.4c.2-.4.2-.9 0-1.3l-.2-.4c-.3-.5-.8-1-1.3-1.3l-.4-.2H20c-.6 0-1.1-.2-1.5-.5l-.2-.2c-.4-.4-.5-.9-.5-1.5v-.2c0-.6.2-1.1.5-1.5l.2-.2c.4-.4.9-.5 1.5-.5h.2c.3 0 .6.1.8.4l.2.2c.4.4.5.8.5 1.4v.1c0 .6-.2 1.1-.5 1.5l-.2.2c-.3.3-.6.4-1 .4h-.2c-.7 0-1.3-.3-1.8-.7l-.2-.2c-.5-.4-1-.9-1.8-1.2l-.4-.2c-.4-.2-.8-.2-1.2 0l-.5.2c-.4-.2-.7-.6-1-1l-.2-.4c-.2-.4-.2-.9 0-1.3l.2-.4c.3-.5.8-1 1.3-1.3l.4-.2H24c-.6 0-1.1.2-1.5-.5l-.2-.2c-.4-.4-.5-.9-.5-1.5v-.2c0-.6.2-1.1.5-1.5l.2-.2c.4-.4.9-.5 1.5-.5h-.2c-.3 0-.6-.1-.8-.4l-.2-.2c-.4-.4-.5-.8-.5-1.4v-.1c0-.6.2-1.1.5-1.5l-.2-.2c.3-.3.6-.4 1-.4H21c.7 0 1.3.3 1.8.7l.2.2c.5.4 1 .9 1.8 1.2l.4.2c.4.2.8.2 1.2 0l-.5-.2c-.4-.2-.7-.6-1-1l-.2-.4c.2-.4.2-.9 0-1.3l-.2-.4c-.3-.5-.8-1-1.3-1.3l-.4-.2H12z"/></svg>
        </button>
        <button id="cart-button" class="bg-indigo-600 text-white w-16 h-16 rounded-full shadow-lg hover:bg-indigo-700 flex items-center justify-center transition-transform transform hover:scale-110" title="Keranjang Belanja">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <span id="cart-count" class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">0</span>
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
    
    <!-- Modal AI Advisor -->
    <div id="advisor-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div id="advisor-modal-content" class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b"><h2 class="text-xl font-semibold text-slate-800">✨ AI Advisor</h2><button id="close-advisor-modal-button" class="text-slate-500 hover:text-slate-800">&times;</button></div>
            <div id="advisor-chat-box" class="p-4 flex-1 overflow-y-auto flex flex-col gap-4"></div>
            <div class="p-4 border-t bg-slate-50"><form id="advisor-form" class="flex gap-2"><input type="text" id="advisor-input" class="flex-1 w-full rounded-md border-slate-300 shadow-sm" placeholder="Tanya saran produk..." required><button type="submit" class="bg-purple-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-purple-700 transition">Kirim</button></form></div>
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
        const allProducts = <?php echo json_encode($products); ?>;
        const storeConfig = <?php echo json_encode($storeConfig); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            let cart = [];
            let shippingCost = 0;
            let currentCategory = 'all';

            const DOMElements = {
                productList: document.getElementById('product-list'),
                categoryFilters: document.getElementById('category-filters'),
                cartButton: document.getElementById('cart-button'),
                cartModal: document.getElementById('cart-modal'),
                closeCartModalButton: document.getElementById('close-cart-modal-button'),
                cartCount: document.getElementById('cart-count'),
                cartItemsContainer: document.getElementById('cart-items-container'),
                subtotalPrice: document.getElementById('subtotal-price'),
                shippingCost: document.getElementById('shipping-cost'),
                totalPrice: document.getElementById('total-price'),
                checkoutButton: document.getElementById('checkout-button'),
                customerName: document.getElementById('customer-name'),
                customerAddress: document.getElementById('customer-address'),
                provinceSelect: document.getElementById('province'),
                regencySelect: document.getElementById('regency'),
                districtSelect: document.getElementById('district'),
                villageSelect: document.getElementById('village'),
                checkOngkirButton: document.getElementById('check-ongkir-button'),
                paymentMethodSelect: document.getElementById('payment-method'),
                transferInfo: document.getElementById('transfer-info'),
                infoModal: document.getElementById('info-modal'),
                infoModalTitle: document.getElementById('info-modal-title'),
                infoModalContent: document.getElementById('info-modal-content'),
                infoModalCloseBtn: document.getElementById('info-modal-close-btn'),
                advisorButton: document.getElementById('advisor-button'),
                advisorModal: document.getElementById('advisor-modal'),
                closeAdvisorModalButton: document.getElementById('close-advisor-modal-button'),
                advisorChatBox: document.getElementById('advisor-chat-box'),
                advisorForm: document.getElementById('advisor-form'),
                advisorInput: document.getElementById('advisor-input'),
            };

            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            const showInfoModal = (title, content) => { DOMElements.infoModalTitle.textContent = title; DOMElements.infoModalContent.innerHTML = content; DOMElements.infoModal.classList.remove('hidden'); DOMElements.infoModal.classList.add('flex'); };
            const hideInfoModal = () => { DOMElements.infoModal.classList.add('hidden'); };
            const toggleModal = (modalEl, show) => { if (show) { modalEl.classList.remove('hidden'); modalEl.classList.add('flex'); modalEl.querySelector('div').classList.add('fade-in'); document.body.classList.add('modal-open'); } else { modalEl.querySelector('div').classList.remove('fade-in'); setTimeout(() => { modalEl.classList.add('hidden'); if (!document.querySelector('.z-50:not(.hidden)')) { document.body.classList.remove('modal-open'); } }, 300); } };

            const renderProducts = () => {
                DOMElements.productList.innerHTML = '';
                const filtered = currentCategory === 'all' ? allProducts : allProducts.filter(p => p.category_id == currentCategory);
                filtered.forEach(product => {
                    const card = document.createElement('div');
                    const isSoldOut = parseInt(product.stock) <= 0;
                    card.className = 'bg-white rounded-lg shadow-md overflow-hidden group transition-all duration-300 hover:shadow-xl hover:-translate-y-1';
                    card.innerHTML = `
                        <div class="relative overflow-hidden">
                            <img src="uploads/${product.image}" alt="${product.name}" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300 ${isSoldOut ? 'grayscale' : ''}">
                            ${isSoldOut ? '<div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center"><span class="text-white font-bold text-lg bg-red-600 px-3 py-1 rounded-md">STOK HABIS</span></div>' : ''}
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-slate-800 truncate">${product.name}</h3>
                            <p class="text-indigo-600 font-bold mt-1">${formatCurrency(product.price)}</p>
                            <button data-product-id="${product.id}" class="add-to-cart-btn w-full mt-4 font-semibold py-2 rounded-lg text-sm transition-colors ${isSoldOut ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'}" ${isSoldOut ? 'disabled' : ''}>
                                ${isSoldOut ? 'Stok Habis' : '+ Keranjang'}
                            </button>
                        </div>`;
                    DOMElements.productList.appendChild(card);
                });
            };

            const callGeminiAPI = async (payload) => {
                const apiKey = storeConfig.geminiApiKey;
                if (!apiKey) {
                    throw new Error("API Key untuk Gemini tidak ditemukan. Silakan atur di config.php");
                }
                const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`;
                try {
                    const response = await fetch(apiUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                    if (!response.ok) { const error = await response.json(); throw new Error(error.error.message || 'Gagal menghubungi API'); }
                    const result = await response.json();
                    if (result.candidates?.[0]?.content?.parts?.[0]) { return result.candidates[0].content.parts[0].text; }
                    throw new Error(`Respon diblokir atau kosong: ${result.promptFeedback?.blockReason || 'Tidak ada konten'}`);
                } catch (error) { console.error("Gemini API Error:", error); throw error; }
            };

            const handleAdvisorSubmit = async (e) => {
                e.preventDefault();
                const userQuery = DOMElements.advisorInput.value.trim();
                if (!userQuery) return;
                addChatMessage(userQuery, 'user');
                DOMElements.advisorInput.value = '';
                DOMElements.advisorForm.querySelector('button').disabled = true;
                addChatMessage('<div class="loader"></div>', 'ai', true);
                const productListString = allProducts.map(p => p.name).join(', ');
                const prompt = `Anda adalah AI Advisor untuk toko "${storeConfig.storeName}". Pelanggan bertanya: "${userQuery}". Berdasarkan pertanyaan itu, rekomendasikan maksimal 3 produk dari daftar ini: [${productListString}]. Jelaskan mengapa setiap produk cocok. Jika tidak ada yang cocok, jelaskan dengan sopan.`;
                const payload = { contents: [{ parts: [{ text: prompt }] }], generationConfig: { responseMimeType: "application/json", responseSchema: { type: "OBJECT", properties: { reply: { type: "STRING" }, recommendations: { type: "ARRAY", items: { type: "OBJECT", properties: { productName: { type: "STRING" }, reason: { type: "STRING" } } } } } } } };
                try {
                    const resultText = await callGeminiAPI(payload);
                    displayAdvisorResponse(JSON.parse(resultText));
                } catch (error) { displayAdvisorResponse({ reply: `Maaf, terjadi kesalahan: ${error.message}` }); } finally { DOMElements.advisorForm.querySelector('button').disabled = false; }
            };
    
            const addChatMessage = (content, sender, isLoading = false) => {
                const chatBox = document.createElement('div');
                chatBox.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;
                const bubble = document.createElement('div');
                bubble.className = `chat-bubble p-3 ${sender}`;
                bubble.innerHTML = content;
                if (isLoading) bubble.id = 'ai-thinking-bubble';
                chatBox.appendChild(bubble);
                DOMElements.advisorChatBox.appendChild(chatBox);
                DOMElements.advisorChatBox.scrollTop = DOMElements.advisorChatBox.scrollHeight;
            };
            
            const displayAdvisorResponse = (response) => {
                const thinkingBubble = document.getElementById('ai-thinking-bubble');
                if (thinkingBubble) thinkingBubble.parentElement.remove();
                let content = `<p>${response.reply || 'Berikut rekomendasi untukmu:'}</p>`;
                if (response.recommendations && response.recommendations.length > 0) {
                    response.recommendations.forEach(rec => {
                        const product = allProducts.find(p => p.name.toLowerCase() === rec.productName.toLowerCase());
                        if (product) {
                            content += `<div class="bg-white rounded-lg p-2 border my-2"><h4 class="font-bold">${product.name}</h4><p class="text-sm italic">"${rec.reason}"</p><button data-product-id="${product.id}" class="add-to-cart-btn text-sm text-indigo-600 hover:underline mt-1 w-full text-left">Tambah ke Keranjang &rarr;</button></div>`;
                        }
                    });
                }
                addChatMessage(content, 'ai');
            };

            const saveCartToCookie = () => { document.cookie = `nandraShopCart=${JSON.stringify(cart)};path=/;max-age=604800`; };
            const loadCartFromCookie = () => { const cookieValue = document.cookie.split('; ').find(row => row.startsWith('nandraShopCart=')); if (cookieValue) { try { cart = JSON.parse(cookieValue.split('=')[1]); } catch (e) { cart = []; } } };
            
            const addToCart = (productId) => {
                const product = allProducts.find(p => p.id == productId);
                const itemInCart = cart.find(item => item.id == productId);
                const currentQtyInCart = itemInCart ? itemInCart.quantity : 0;
                if (currentQtyInCart >= product.stock) { showInfoModal('Stok Tidak Cukup', `Maaf, stok untuk ${product.name} hanya tersisa ${product.stock}.`); return; }
                if (itemInCart) { itemInCart.quantity++; } else { cart.push({ ...product, quantity: 1 }); }
                updateCart();
            };
            const updateCart = () => { renderCartItems(); updateCartSummary(); updateCartCount(); saveCartToCookie(); };
            const renderCartItems = () => { DOMElements.cartItemsContainer.innerHTML = ''; if (cart.length === 0) { DOMElements.cartItemsContainer.innerHTML = `<p class="text-center text-slate-500 py-8">Keranjang kosong.</p>`; return; } cart.forEach(item => { const el = document.createElement('div'); el.className = 'flex items-center justify-between py-3 border-b'; el.innerHTML = `<div class="flex items-center gap-3"><img src="uploads/${item.image}" alt="${item.name}" class="w-16 h-16 rounded-md object-cover"><div><p class="font-semibold">${item.name}</p><p class="text-sm text-slate-500">${formatCurrency(item.price)}</p></div></div><div class="flex items-center gap-3"><div class="flex items-center border rounded-md"><button data-id="${item.id}" class="quantity-change-btn px-2 py-1">-</button><span class="px-2">${item.quantity}</span><button data-id="${item.id}" class="quantity-change-btn px-2 py-1">+</button></div></div>`; DOMElements.cartItemsContainer.appendChild(el); }); };
            const changeQuantity = (productId, change) => { const itemInCart = cart.find(item => item.id == productId); if (!itemInCart) return; if (change > 0) { const product = allProducts.find(p => p.id == productId); if (itemInCart.quantity >= product.stock) { showInfoModal('Stok Tidak Cukup', `Stok untuk ${product.name} hanya tersisa ${product.stock}.`); return; } } itemInCart.quantity += change; if (itemInCart.quantity <= 0) { cart = cart.filter(item => item.id != productId); } updateCart(); };
            const updateCartSummary = () => { const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0); const total = subtotal + shippingCost; DOMElements.subtotalPrice.textContent = formatCurrency(subtotal); DOMElements.shippingCost.textContent = formatCurrency(shippingCost); DOMElements.totalPrice.textContent = formatCurrency(total); };
            const updateCartCount = () => { const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0); DOMElements.cartCount.textContent = totalItems; DOMElements.cartCount.style.display = totalItems > 0 ? 'flex' : 'none'; };
            const API_WILAYAH_BASE_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';
            async function fetchAndPopulateSelect(url, selectElement, placeholder) { selectElement.innerHTML = `<option value="">Memuat...</option>`; selectElement.disabled = true; try { const response = await fetch(url); const data = await response.json(); selectElement.innerHTML = `<option value="">${placeholder}</option>`; data.forEach(item => { const option = document.createElement('option'); option.value = item.id; option.textContent = item.name; selectElement.appendChild(option); }); selectElement.disabled = false; } catch (error) { selectElement.innerHTML = `<option value="">Gagal memuat</option>`; } }
            const handleCheckout = () => { const data = { name: DOMElements.customerName.value.trim(), address: DOMElements.customerAddress.value.trim(), province: DOMElements.provinceSelect.options[DOMElements.provinceSelect.selectedIndex]?.text, regency: DOMElements.regencySelect.options[DOMElements.regencySelect.selectedIndex]?.text, district: DOMElements.districtSelect.options[DOMElements.districtSelect.selectedIndex]?.text, village: DOMElements.villageSelect.options[DOMElements.villageSelect.selectedIndex]?.text, paymentMethod: DOMElements.paymentMethodSelect.value }; if (cart.length === 0) { showInfoModal('Oops!', 'Keranjang Anda kosong!'); return; } if (!data.name || !data.address || !data.province || !data.regency || !data.district || !data.village) { showInfoModal('Oops!', 'Harap lengkapi semua data pelanggan dan alamat.'); return; } if (shippingCost === 0) { showInfoModal('Oops!', 'Harap tekan tombol "Cek Estimasi Ongkos Kirim" dulu.'); return; } if (!data.paymentMethod) { showInfoModal('Oops!', 'Harap pilih metode pembayaran.'); return; } let msg = `Halo, ${storeConfig.storeName}. Saya mau pesan:\n\n`; cart.forEach(i => { msg += `*${i.name}* (${i.quantity}x) - ${formatCurrency(i.price * i.quantity)}\n`; }); const sub = cart.reduce((s, i) => s + (i.price * i.quantity), 0); msg += `\nSubtotal: *${formatCurrency(sub)}*\nOngkir (Estimasi): *${formatCurrency(shippingCost)}*\nTotal: *${formatCurrency(sub + shippingCost)}*`; msg += `\n\n*Metode Pembayaran:*\n${data.paymentMethod}`; if (data.paymentMethod === 'Transfer') { msg += ` (${storeConfig.paymentInfo.bank} - ${storeConfig.paymentInfo.accountNumber})`; } msg += `\n\n*Alamat Pengiriman:*\n${data.name}\n${data.address}\n${data.village}, ${data.district}\n${data.regency}, ${data.province}\n\nTerima kasih!`; window.open(`https://wa.me/${storeConfig.whatsappNumber}?text=${encodeURIComponent(msg)}`, '_blank'); };
            
            // Event Listeners
            DOMElements.categoryFilters.addEventListener('click', (e) => { if (e.target.classList.contains('category-btn')) { document.querySelectorAll('.category-btn').forEach(btn => { btn.classList.remove('bg-indigo-600', 'text-white', 'shadow'); btn.classList.add('bg-white', 'text-slate-700'); }); e.target.classList.add('bg-indigo-600', 'text-white', 'shadow'); e.target.classList.remove('bg-white', 'text-slate-700'); currentCategory = e.target.dataset.categoryId; renderProducts(); } });
            document.body.addEventListener('click', e => { if (e.target.classList.contains('add-to-cart-btn')) { addToCart(e.target.dataset.productId); } });
            DOMElements.cartItemsContainer.addEventListener('click', (e) => { if (e.target.classList.contains('quantity-change-btn')) { const change = e.target.textContent === '+' ? 1 : -1; changeQuantity(e.target.dataset.id, change); } });
            DOMElements.cartButton.addEventListener('click', () => toggleModal(DOMElements.cartModal, true));
            DOMElements.closeCartModalButton.addEventListener('click', () => toggleModal(DOMElements.cartModal, false));
            DOMElements.infoModalCloseBtn.addEventListener('click', hideInfoModal);
            DOMElements.checkoutButton.addEventListener('click', handleCheckout);
            DOMElements.paymentMethodSelect.addEventListener('change', (e) => { DOMElements.transferInfo.classList.toggle('hidden', e.target.value !== 'Transfer'); });
            DOMElements.provinceSelect.addEventListener('change', () => { const id = DOMElements.provinceSelect.value; DOMElements.regencySelect.innerHTML = '<option value="">Pilih Kota/Kab</option>'; DOMElements.regencySelect.disabled = true; DOMElements.districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>'; DOMElements.districtSelect.disabled = true; DOMElements.villageSelect.innerHTML = '<option value="">Pilih Desa/Kel</option>'; DOMElements.villageSelect.disabled = true; if (id) { fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/regencies/${id}.json`, DOMElements.regencySelect, 'Pilih Kota/Kab'); } });
            DOMElements.regencySelect.addEventListener('change', () => { const id = DOMElements.regencySelect.value; DOMElements.districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>'; DOMElements.districtSelect.disabled = true; DOMElements.villageSelect.innerHTML = '<option value="">Pilih Desa/Kel</option>'; DOMElements.villageSelect.disabled = true; if (id) { fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/districts/${id}.json`, DOMElements.districtSelect, 'Pilih Kecamatan'); } });
            DOMElements.districtSelect.addEventListener('change', () => { const id = DOMElements.districtSelect.value; if (id) { fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/villages/${id}.json`, DOMElements.villageSelect, 'Pilih Desa/Kel'); } });
            DOMElements.checkOngkirButton.addEventListener('click', () => {
                const totalWeight = cart.reduce((sum, item) => sum + (item.weight * item.quantity), 0);
                if (totalWeight === 0) { showInfoModal('Oops!', 'Keranjang Anda kosong, tidak bisa menghitung ongkir.'); return; }
                const provinceName = DOMElements.provinceSelect.options[DOMElements.provinceSelect.selectedIndex]?.text.toUpperCase();
                if (!provinceName || DOMElements.provinceSelect.value === "") { showInfoModal('Oops!', 'Pilih provinsi terlebih dahulu.'); return; }
                const zones = {'JAWA TIMUR':{min:8e3,max:15e3},'JAWA TENGAH':{min:15e3,max:25e3},'DI YOGYAKARTA':{min:15e3,max:25e3},'JAWA BARAT':{min:18e3,max:3e4},'DKI JAKARTA':{min:18e3,max:3e4},BANTEN:{min:18e3,max:3e4},BALI:{min:15e3,max:28e3},'NUSA TENGGARA':{min:3e4,max:5e4},SUMATERA:{min:35e3,max:6e4},KALIMANTAN:{min:4e4,max:65e3},SULAWESI:{min:45e3,max:7e4},MALUKU:{min:5e4,max:8e4},PAPUA:{min:6e4,max:1e5}};
                let zoneKey = Object.keys(zones).find(key => provinceName.includes(key)) || 'default';
                let zone = zones[zoneKey] || { min: 25000, max: 50000 };
                // Simulasi ongkir per kg (misal: 10rb/kg untuk zona terdekat)
                const costPerKg = zone.min / 1000; 
                shippingCost = Math.ceil(totalWeight / 1000) * costPerKg;
                shippingCost = Math.max(shippingCost, zone.min); // Pastikan tidak lebih rendah dari ongkir minimum
                updateCartSummary();
                showInfoModal('Estimasi Ongkir', `Estimasi ongkir dari Banyuwangi untuk berat ${totalWeight} gram adalah ${formatCurrency(shippingCost)}.`);
            });
            DOMElements.advisorButton.addEventListener('click', () => { toggleModal(DOMElements.advisorModal, true); if(DOMElements.advisorChatBox.children.length === 0) addChatMessage('Halo! Ada yang bisa saya bantu? Tanyakan saja saran produk, misalnya "template apa yang bagus untuk promosi makanan?".', 'ai'); });
            DOMElements.closeAdvisorModalButton.addEventListener('click', () => toggleModal(DOMElements.advisorModal, false));
            DOMElements.advisorForm.addEventListener('submit', handleAdvisorSubmit);

            // Inisialisasi
            loadCartFromCookie();
            renderProducts();
            updateCart();
            fetchAndPopulateSelect(`${API_WILAYAH_BASE_URL}/provinces.json`, DOMElements.provinceSelect, 'Pilih Provinsi');
        });
    </script>
</body>
</html>
