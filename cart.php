<?php require 'includes/auth.php'; ?>
<?php include 'includes/header.php'; ?>
<main class="l-main">
    <section class="cart section bd-container" id="cart">
        <span class="section-subtitle">Your Basket</span>
        <h2 class="section-title">Checkout</h2>
        <div class="card" style="max-width:900px; margin:auto;">
            <table class="cart-table" id="cart-table">
                <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                <tbody id="cart-items-body"></tbody>
            </table>
            <div id="empty-cart-msg" style="display:none; text-align:center; padding:2rem;">
                <p>Your basket is empty.</p><a href="menu.php" class="button">Browse Menu</a>
            </div>
            <div class="cart-summary"><h3 id="cart-total">Total: ₱0.00</h3></div>
            <form method="post" class="checkout-form">
                <div class="form-group"><label>Delivery Address</label><textarea name="address" class="form-input" required></textarea></div>
                <div class="form-group"><label>Special Instructions</label><textarea name="notes" class="form-input"></textarea></div>
                <button type="submit" class="submit-button">Place Order</button>
            </form>
        </div>
    </section>
</main>

<script>
function loadCart() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const tbody = document.getElementById('cart-items-body');
    const totalEl = document.getElementById('cart-total');
    const emptyMsg = document.getElementById('empty-cart-msg');
    const table = document.getElementById('cart-table');
    tbody.innerHTML = '';
    if (cart.length === 0) { table.style.display = 'none'; emptyMsg.style.display = 'block'; totalEl.textContent = 'Total: ₱0.00'; return; }
    table.style.display = 'table'; emptyMsg.style.display = 'none';
    let total = 0;
    cart.forEach((item, index) => {
        const lineTotal = item.price * item.qty; total += lineTotal;
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><strong>${item.name}</strong></td><td>₱${item.price.toFixed(2)}</td><td><input type="number" min="1" value="${item.qty}" class="qty-input" data-index="${index}" style="width:60px;"></td><td>₱${lineTotal.toFixed(2)}</td><td><button class="remove-btn" data-index="${index}"><i class='bx bx-trash'></i></button></td>`;
        tbody.appendChild(tr);
    });
    totalEl.textContent = 'Total: ₱' + total.toFixed(2);
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const idx = parseInt(this.dataset.index);
            let newQty = parseInt(this.value) || 1; if (newQty < 1) newQty = 1;
            cart[idx].qty = newQty;
            localStorage.setItem('cart', JSON.stringify(cart)); loadCart(); updateCartCount();
        });
    });
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const idx = parseInt(this.dataset.index);
            cart.splice(idx, 1); localStorage.setItem('cart', JSON.stringify(cart)); loadCart(); updateCartCount();
        });
    });
}
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const total = cart.reduce((sum, item) => sum + item.qty, 0);
    const el = document.getElementById('cart-count');
    if (el) el.textContent = total;
}
loadCart();
updateCartCount();
</script>

<?php include 'includes/footer.php'; ?>