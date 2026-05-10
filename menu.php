<?php
require 'includes/auth.php';

$menuQuery = "SELECT * FROM menu ORDER BY name";
$menuResult = $conn->query($menuQuery);
$menuItems = [];
while ($row = $menuResult->fetch_assoc()) {
    $menuItems[] = $row;
}

$islandRegions = [
    'Luzon' => ['Region1 (Ilocos)', 'Region2 (Cagayan Valley)', 'Region3 (Central Luzon)', 'Region4-A (CALABARZON)', 'Region4-B (MIMAROPA)', 'Region5 (Bicol)', 'CAR'],
    'Visayas' => ['Region6 (Western Visayas)', 'Region7 (Central Visayas)', 'Region8 (Eastern Visayas)'],
    'Mindanao' => ['Region9 (Zamboanga Peninsula)', 'Region10 (Northern Mindanao)', 'Region11 (Davao)', 'Region12 (SOCCSKSARGEN)', 'Region13 (Caraga)', 'BARMM']
];

$conn->close();

// Build beverage name list (to separate food vs beverage)
$beverageNames = ['Basi','Sugarcane Juice','Kapeng Barako','Samalamig','Lambanog','Fresh Coconut Juice','Pili Juice','Tuba','Tapuy','Kapeng Arabica','Sikwate','Salabat','Sago\'t Gulaman','Calamansi Juice','Fresh Pineapple Juice','Durian Shake','Cacao Tablea Drink','Corn Coffee','Kahawa Sug','Palapa Drink','Buko Juice'];
?>

<?php include 'includes/header.php'; ?>

<div class="menu-ordering">
    <!-- Step 1: Islands -->
    <div id="step-islands" class="step active">
        <h2 class="section-title">Choose an Island</h2>
        <div class="island-grid">
            <div class="island-card" data-island="Luzon">
                <img src="assets/old_mapa.png" alt="Luzon"><h3>Luzon</h3>
            </div>
            <div class="island-card" data-island="Visayas">
                <img src="assets/old_mapa.png" alt="Visayas"><h3>Visayas</h3>
            </div>
            <div class="island-card" data-island="Mindanao">
                <img src="assets/old_mapa.png" alt="Mindanao"><h3>Mindanao</h3>
            </div>
        </div>
    </div>

    <!-- Step 2: Regions -->
    <div id="step-regions" class="step">
        <h2 class="section-title">Select a Region</h2>
        <div id="region-list" class="region-grid"></div>
        <button class="button back-btn" id="back-to-islands"><i class='bx bx-arrow-back'></i> Back</button>
    </div>

    <!-- Step 3: Categories (Food / Beverages) -->
    <div id="step-categories" class="step">
        <h2 class="section-title" id="category-region-title"></h2>
        <div class="category-cards">
            <div class="category-card" data-category="food">
                <img src="assets/old_mapa.png" alt="Food"><span>Food</span>
            </div>
            <div class="category-card" data-category="beverage">
                <img src="assets/old_mapa.png" alt="Beverages"><span>Beverages</span>
            </div>
        </div>
        <button class="button back-btn" id="back-to-regions-cat"><i class='bx bx-arrow-back'></i> Back</button>
    </div>

    <!-- Step 4: Items list -->
    <div id="step-items" class="step">
        <h2 class="section-title" id="items-title"></h2>
        <div class="menu__container bd-grid" id="items-grid"></div>
        <button class="button back-btn" id="back-to-categories"><i class='bx bx-arrow-back'></i> Back</button>
    </div>
</div>

<!-- Item Detail Modal -->
<div class="modal" id="item-modal">
    <div class="modal-content item-detail">
        <span class="close-modal" id="close-item-modal">&times;</span>
        <img id="modal-item-img" src="" alt="">
        <h3 id="modal-item-name"></h3>
        <p id="modal-item-desc"></p>
        <p id="modal-item-region"></p>
        <p id="modal-item-price"></p>
        <div class="qty-selector">
            <button id="qty-minus">-</button>
            <input type="number" id="qty-input" value="1" min="1" readonly>
            <button id="qty-plus">+</button>
        </div>
        <button class="button add-to-bag-btn" id="add-to-bag-btn"><i class='bx bx-cart'></i> Add to Bag</button>
    </div>
</div>

<!-- Bag Sidebar -->
<div class="cart-sidebar" id="cart-sidebar">
    <div class="cart-header">
        <h3>Your Bag</h3>
        <button class="close-cart" id="close-cart">&times;</button>
    </div>
    <div id="cart-items"></div>
    <div class="cart-total">
        <span>Total: </span><strong id="cart-total-amount">₱0.00</strong>
    </div>
    <button class="button checkout-btn" id="checkout-btn">Proceed to Checkout</button>
</div>

<!-- Checkout Modal -->
<div class="modal" id="checkout-modal">
    <div class="modal-content checkout-form">
        <span class="close-modal" id="close-checkout">&times;</span>
        <h2>Checkout</h2>
        <div class="form-group">
            <label>Order Type</label>
            <select id="order-type" class="form-input">
                <option value="dine-in">Dine In</option>
                <option value="take-out">Take Out</option>
            </select>
        </div>
        <div class="form-group">
            <label>Payment Method</label>
            <select id="payment-method" class="form-input">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="gcash">GCash</option>
            </select>
        </div>
        <button class="button" id="place-order-btn">Place Order</button>
    </div>
</div>

<!-- Floating cart button -->
<div class="cart-float" id="cart-float">
    <i class='bx bx-cart'></i>
    <span class="cart-count" id="cart-count">0</span>
</div>

<script>
const menuItems = <?= json_encode($menuItems) ?>;
const islandRegions = <?= json_encode($islandRegions) ?>;
const beverageNames = <?= json_encode($beverageNames) ?>;
let currentIsland = null;
let currentRegion = null;
let selectedItem = null;
let quantity = 1;
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function showStep(stepId) {
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    document.getElementById(stepId).classList.add('active');
}

// Island cards
document.querySelectorAll('.island-card').forEach(card => {
    card.addEventListener('click', function() {
        currentIsland = this.dataset.island;
        const regions = islandRegions[currentIsland];
        const regionList = document.getElementById('region-list');
        regionList.innerHTML = '';
        regions.forEach(reg => {
            const div = document.createElement('div');
            div.className = 'region-card';
            div.innerHTML = `<img src="assets/old_mapa.png" alt=""><span>${reg}</span>`;
            div.addEventListener('click', () => {
                currentRegion = `${currentIsland} – ${reg}`;
                showStep('step-categories');
                document.getElementById('category-region-title').textContent = reg;
            });
            regionList.appendChild(div);
        });
        showStep('step-regions');
    });
});

document.getElementById('back-to-islands').addEventListener('click', () => showStep('step-islands'));

// Categories
document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', function() {
        const category = this.dataset.category;
        showItems(category);
    });
});

document.getElementById('back-to-regions-cat').addEventListener('click', () => showStep('step-regions'));

function showItems(category) {
    const grid = document.getElementById('items-grid');
    grid.innerHTML = '';
    const regionItems = menuItems.filter(item => item.region === currentRegion);
    const filtered = regionItems.filter(item => {
        if (category === 'beverage') return beverageNames.includes(item.name);
        else return !beverageNames.includes(item.name);
    });

    if (filtered.length === 0) {
        grid.innerHTML = '<p class="no-items">No items available.</p>';
    } else {
        filtered.forEach(item => {
            const div = document.createElement('div');
            div.className = 'menu__content';
            div.innerHTML = `
                <img src="${item.image_path || 'assets/plate-placeholder.png'}" class="menu__img">
                <h3>${item.name}</h3>
                <span class="menu__region">${item.region.replace(currentIsland+' – ','')}</span>
                <span class="menu__price">₱${parseFloat(item.price).toFixed(2)}</span>
                <button class="button menu__button item-select" data-id="${item.id}">Select</button>
            `;
            grid.appendChild(div);
        });

        document.querySelectorAll('.item-select').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                selectedItem = menuItems.find(i => i.id == id);
                quantity = 1;
                document.getElementById('modal-item-img').src = selectedItem.image_path || 'assets/plate-placeholder.png';
                document.getElementById('modal-item-name').textContent = selectedItem.name;
                document.getElementById('modal-item-desc').textContent = selectedItem.description;
                document.getElementById('modal-item-region').textContent = selectedItem.region.replace(currentIsland+' – ','');
                document.getElementById('modal-item-price').textContent = '₱'+parseFloat(selectedItem.price).toFixed(2);
                document.getElementById('qty-input').value = 1;
                document.getElementById('item-modal').style.display = 'flex';
            });
        });
    }
    showStep('step-items');
    document.getElementById('items-title').textContent = category.charAt(0).toUpperCase()+category.slice(1) + ' in ' + currentRegion.replace(currentIsland+' – ','');
}

document.getElementById('back-to-categories').addEventListener('click', () => showStep('step-categories'));

// Item modal quantity
document.getElementById('close-item-modal').addEventListener('click', () => document.getElementById('item-modal').style.display = 'none');
document.getElementById('qty-minus').addEventListener('click', () => {
    if (quantity > 1) { quantity--; document.getElementById('qty-input').value = quantity; }
});
document.getElementById('qty-plus').addEventListener('click', () => {
    quantity++; document.getElementById('qty-input').value = quantity;
});
document.getElementById('add-to-bag-btn').addEventListener('click', () => {
    const existing = cart.find(i => i.id == selectedItem.id);
    if (existing) { existing.qty += quantity; }
    else { cart.push({ id: selectedItem.id, name: selectedItem.name, price: selectedItem.price, qty: quantity }); }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    document.getElementById('item-modal').style.display = 'none';
});

// Cart sidebar
function updateCartUI() {
    const cartItemsDiv = document.getElementById('cart-items');
    const totalSpan = document.getElementById('cart-total-amount');
    const countBadge = document.getElementById('cart-count');
    cartItemsDiv.innerHTML = '';
    let total = 0;
    cart.forEach((item, index) => {
        total += item.price * item.qty;
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <span>${item.name} x${item.qty}</span>
            <span>₱${(item.price*item.qty).toFixed(2)}</span>
            <button class="remove-item" data-index="${index}"><i class='bx bx-trash'></i></button>
        `;
        cartItemsDiv.appendChild(div);
    });
    totalSpan.textContent = '₱' + total.toFixed(2);
    countBadge.textContent = cart.reduce((sum, i) => sum + i.qty, 0);

    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            cart.splice(this.dataset.index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        });
    });
}

document.getElementById('cart-float').addEventListener('click', () => document.getElementById('cart-sidebar').classList.toggle('open'));
document.getElementById('close-cart').addEventListener('click', () => document.getElementById('cart-sidebar').classList.remove('open'));

document.getElementById('checkout-btn').addEventListener('click', () => {
    if (cart.length === 0) { alert('Your bag is empty.'); return; }
    document.getElementById('cart-sidebar').classList.remove('open');
    document.getElementById('checkout-modal').style.display = 'flex';
});
document.getElementById('close-checkout').addEventListener('click', () => document.getElementById('checkout-modal').style.display = 'none');

document.getElementById('place-order-btn').addEventListener('click', () => {
    const orderType = document.getElementById('order-type').value;
    const paymentMethod = document.getElementById('payment-method').value;
    const total = cart.reduce((sum, item) => sum + item.price*item.qty, 0);
    fetch('place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            order_type: orderType,
            payment_method: paymentMethod,
            total: total,
            items: cart
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            cart = [];
            localStorage.removeItem('cart');
            updateCartUI();
            document.getElementById('checkout-modal').style.display = 'none';
            window.location.href = 'receipt.php?order_id=' + data.order_id;
        } else {
            alert('Order failed: ' + data.message);
        }
    });
});

// Initial cart UI
updateCartUI();
</script>

<?php include 'includes/footer.php'; ?>