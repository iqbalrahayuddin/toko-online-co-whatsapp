<?php
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id == 0) {
    echo "<p class='text-center text-red-500'>Produk tidak valid.</p>";
    return;
}
$product = get_product_detail($db, $product_id);
if (!$product) {
    echo "<p class='text-center text-red-500'>Produk tidak ditemukan.</p>";
    return;
}
?>
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Checkout Pesanan</h2>
    <div class="bg-white p-8 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <!-- Rincian Produk -->
            <div>
                <h3 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h3>
                <div class="flex items-center space-x-4 border-b pb-4">
                    <img src="uploads/<?php echo htmlspecialchars($product['image'] ? $product['image'] : 'placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-24 h-24 object-cover rounded-md" onerror="this.onerror=null;this.src='https://placehold.co/100x100/e2e8f0/333?text=...';">
                    <div>
                        <h4 class="font-semibold text-lg"><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="text-gray-600"><?php echo format_rupiah($product['price']); ?></p>
                        <p class="text-sm text-gray-500">Berat: <?php echo $product['weight']; ?> gr</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2 text-lg">
                     <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold"><?php echo format_rupiah($product['price']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span id="shipping-cost" class="font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold pt-2 border-t">
                        <span>Total</span>
                        <span id="total-cost" class="text-indigo-600"><?php echo format_rupiah($product['price']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Form Alamat -->
            <div>
                <h3 class="text-xl font-semibold mb-4">Alamat Pengiriman</h3>
                <form id="checkout-form" class="space-y-4">
                    <input type="hidden" id="product-name" value="<?php echo htmlspecialchars($product['name']); ?>">
                    <input type="hidden" id="product-price" value="<?php echo $product['price']; ?>">
                    <input type="hidden" id="product-weight" value="<?php echo $product['weight']; ?>">
                    
                    <div>
                        <label for="customer-name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="customer-name" name="customer_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="customer-phone" class="block text-sm font-medium text-gray-700">No. HP (WhatsApp)</label>
                        <input type="tel" id="customer-phone" name="customer_phone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="08123456789" required>
                    </div>
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <select id="province" name="province" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Pilih Provinsi...</option>
                        </select>
                    </div>
                    <div>
                        <label for="regency" class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                        <select id="regency" name="regency" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required disabled>
                            <option value="">Pilih Kabupaten/Kota...</option>
                        </select>
                    </div>
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select id="district" name="district" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required disabled>
                            <option value="">Pilih Kecamatan...</option>
                        </select>
                    </div>
                     <div>
                        <label for="village" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select id="village" name="village" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required disabled>
                            <option value="">Pilih Desa/Kelurahan...</option>
                        </select>
                    </div>
                    <div>
                        <label for="full-address" class="block text-sm font-medium text-gray-700">Alamat Lengkap (Nama Jalan, No. Rumah, RT/RW)</label>
                        <textarea id="full-address" name="full_address" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 05" required></textarea>
                    </div>

                    <button type="submit" id="whatsapp-order-btn" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white whatsapp-button focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50" disabled>
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.894 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.886-.001 2.267.651 4.383 1.88 6.166l-1.29 4.721 4.793-1.262z"/></svg>
                        Pesan via WhatsApp
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiBaseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api/'; // API Wilayah Alternatif
    const provinceSelect = document.getElementById('province');
    const regencySelect = document.getElementById('regency');
    const districtSelect = document.getElementById('district');
    const villageSelect = document.getElementById('village');
    const form = document.getElementById('checkout-form');
    const whatsappBtn = document.getElementById('whatsapp-order-btn');

    const shippingCostEl = document.getElementById('shipping-cost');
    const totalCostEl = document.getElementById('total-cost');
    const productPrice = parseFloat(document.getElementById('product-price').value);
    const productWeight = parseFloat(document.getElementById('product-weight').value);
    const shippingCostPerKg = <?php echo $shipping_cost_per_kg; ?>;

    // Fetch Provinces
    fetch(`${apiBaseUrl}provinces.json`)
        .then(response => response.json())
        .then(provinces => {
            provinces.forEach(province => {
                const option = new Option(province.name, province.id);
                provinceSelect.add(option);
            });
        });

    function resetSelect(selectElement, placeholder) {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        selectElement.disabled = true;
    }

    provinceSelect.addEventListener('change', function() {
        resetSelect(regencySelect, 'Pilih Kabupaten/Kota...');
        resetSelect(districtSelect, 'Pilih Kecamatan...');
        resetSelect(villageSelect, 'Pilih Desa/Kelurahan...');
        if (!this.value) return;

        fetch(`${apiBaseUrl}regencies/${this.value}.json`)
            .then(response => response.json())
            .then(regencies => {
                regencySelect.disabled = false;
                regencies.forEach(regency => {
                    const option = new Option(regency.name, regency.id);
                    regencySelect.add(option);
                });
            });
    });

    regencySelect.addEventListener('change', function() {
        resetSelect(districtSelect, 'Pilih Kecamatan...');
        resetSelect(villageSelect, 'Pilih Desa/Kelurahan...');
        if (!this.value) return;

        fetch(`${apiBaseUrl}districts/${this.value}.json`)
            .then(response => response.json())
            .then(districts => {
                districtSelect.disabled = false;
                districts.forEach(district => {
                    const option = new Option(district.name, district.id);
                    districtSelect.add(option);
                });
            });
    });
    
    districtSelect.addEventListener('change', function() {
        resetSelect(villageSelect, 'Pilih Desa/Kelurahan...');
        if (!this.value) return;

        fetch(`${apiBaseUrl}villages/${this.value}.json`)
            .then(response => response.json())
            .then(villages => {
                villageSelect.disabled = false;
                villages.forEach(village => {
                    const option = new Option(village.name, village.id);
                    villageSelect.add(option);
                });
            });
    });

    villageSelect.addEventListener('change', function() {
        if (this.value) {
            // Hitung Ongkir (Simulasi)
            const weightInKg = productWeight / 1000;
            const calculatedShipping = Math.ceil(weightInKg) * shippingCostPerKg; // Pembulatan ke atas
            const total = productPrice + calculatedShipping;

            shippingCostEl.textContent = 'Rp ' + calculatedShipping.toLocaleString('id-ID');
            totalCostEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            whatsappBtn.disabled = false;
        } else {
            whatsappBtn.disabled = true;
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const customerName = document.getElementById('customer-name').value;
        const customerPhone = document.getElementById('customer-phone').value;
        const province = provinceSelect.options[provinceSelect.selectedIndex].text;
        const regency = regencySelect.options[regencySelect.selectedIndex].text;
        const district = districtSelect.options[districtSelect.selectedIndex].text;
        const village = villageSelect.options[villageSelect.selectedIndex].text;
        const fullAddress = document.getElementById('full-address').value;

        const productName = document.getElementById('product-name').value;
        const finalTotal = totalCostEl.textContent;
        const finalShipping = shippingCostEl.textContent;

        const waNumber = '<?php echo $whatsapp_number; ?>';
        
        let message = `Halo *<?php echo htmlspecialchars($store_name); ?>*, saya mau pesan:\n\n`;
        message += `*Produk:* ${productName}\n`;
        message += `*Harga:* <?php echo format_rupiah($product['price']); ?>\n\n`;
        message += `*--- Data Pengiriman ---*\n`;
        message += `*Nama:* ${customerName}\n`;
        message += `*No. HP:* ${customerPhone}\n`;
        message += `*Alamat:* ${fullAddress}, ${village}, ${district}, ${regency}, ${province}\n\n`;
        message += `*--- Ringkasan Biaya ---*\n`;
        message += `*Ongkos Kirim:* ${finalShipping}\n`;
        message += `*Total Pembayaran:* *${finalTotal}*\n\n`;
        message += `Mohon info lanjut untuk proses pembayarannya. Terima kasih.`;

        const encodedMessage = encodeURIComponent(message);
        const whatsappUrl = `https://api.whatsapp.com/send?phone=${waNumber}&text=${encodedMessage}`;

        window.open(whatsappUrl, '_blank');
    });
});
</script>