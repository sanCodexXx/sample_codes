<?php
require 'includes/auth.php';

$menuQuery = "SELECT name, description, price, region, island, image_path FROM menu";
$menuResult = $conn->query($menuQuery);
$menuItems = [];
while ($row = $menuResult->fetch_assoc()) {
    $menuItems[] = $row;
}
$conn->close();
?>

<?php include 'includes/header.php'; ?>

<main class="l-main">
    <section class="home" id="home">
        <div class="home__container bd-container">
            <div class="home__data">
                <h1 class="home__title">Authentic Filipino Cuisine</h1>
                <h2 class="home__subtitle">Taste the <br> flavors of the Philippines.</h2>
                <a href="menu.php" class="button">Start Your Adventure</a>
            </div>
            <!-- Clickable map image -->
            <div class="home__img" id="clickable-map">
                <img src="assets/old_mapa.png" alt="Filipino food map">
            </div>
        </div>
    </section>

    <section class="services section bd-container" id="services">
        <span class="section-subtitle">Our offer</span>
        <h2 class="section-title">What we provide</h2>
        <div class="services__container bd-grid">
            <div class="services__content">
                <i class='bx bx-restaurant services__icon'></i>
                <h3 class="services__title">Authentic Recipes</h3>
                <p class="services__description">We preserve generations-old family recipes, using traditional techniques and local ingredients.</p>
            </div>
            <div class="services__content">
                <i class='bx bx-food-menu services__icon'></i>
                <h3 class="services__title">Regional Specialties</h3>
                <p class="services__description">Explore dishes from Luzon, Visayas, and Mindanao.</p>
            </div>
            <div class="services__content">
                <i class='bx bx-car services__icon'></i>
                <h3 class="services__title">Nationwide Delivery</h3>
                <p class="services__description">Hot, fresh, and fast – we bring the taste of the Philippines right to your doorstep.</p>
            </div>
        </div>
    </section>
</main>

<!-- INTERACTIVE MAP MODAL -->
<div class="map-modal" id="map-modal">
    <div class="map-modal__content">
        <span class="map-modal__close" id="close-map-modal">&times;</span>
        <h2 id="modal-title">Choose an Island</h2>

        <!-- Step 1: Island selection -->
        <div class="island-selection" id="island-selection">
            <div class="island-card" data-island="Luzon">
                <img src="assets/old_mapa.png" alt="Luzon">
                <span>Luzon</span>
            </div>
            <div class="island-card" data-island="Visayas">
                <img src="assets/old_mapa.png" alt="Visayas">
                <span>Visayas</span>
            </div>
            <div class="island-card" data-island="Mindanao">
                <img src="assets/old_mapa.png" alt="Mindanao">
                <span>Mindanao</span>
            </div>
        </div>

        <!-- Step 2: Region buttons -->
        <div class="region-selection" id="region-selection" style="display:none;">
            <div id="region-buttons" class="region-buttons"></div>
            <button class="button back-btn" id="back-to-islands"><i class='bx bx-arrow-back'></i> Back to Islands</button>
        </div>

        <!-- Step 3: Dishes grid -->
        <div class="dishes-container" id="dishes-container" style="display:none;">
            <div class="menu__container bd-grid" id="modal-menu-grid"></div>
            <button class="button back-btn" id="back-to-regions"><i class='bx bx-arrow-back'></i> Back to Regions</button>
        </div>
    </div>
</div>

<script>
const allFoods = <?= json_encode($menuItems) ?>;
const defaultImg = "assets/plate-placeholder.png";

const modal = document.getElementById('map-modal');
const clickableMap = document.getElementById('clickable-map');
const closeBtn = document.getElementById('close-map-modal');
const islandSelection = document.getElementById('island-selection');
const regionSelection = document.getElementById('region-selection');
const regionButtonsDiv = document.getElementById('region-buttons');
const dishesContainer = document.getElementById('dishes-container');
const modalMenuGrid = document.getElementById('modal-menu-grid');
const modalTitle = document.getElementById('modal-title');
const backToIslands = document.getElementById('back-to-islands');
const backToRegions = document.getElementById('back-to-regions');

let currentSelectedIsland = null;

clickableMap.addEventListener('click', () => {
    modal.style.display = 'flex';
    resetToIslands();
});

closeBtn.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
});

function resetToIslands() {
    islandSelection.style.display = 'flex';
    regionSelection.style.display = 'none';
    dishesContainer.style.display = 'none';
    modalTitle.textContent = 'Choose an Island';
}

document.querySelectorAll('.island-card').forEach(card => {
    card.addEventListener('click', function() {
        const island = this.dataset.island;
        currentSelectedIsland = island;

        const regions = [...new Set(
            allFoods.filter(f => f.island === island).map(f => f.region)
        )].sort();

        regionButtonsDiv.innerHTML = '';
        if (regions.length === 0) {
            regionButtonsDiv.innerHTML = '<p style="color:white;">No regions available for this island yet.</p>';
        } else {
            regions.forEach(reg => {
                const clean = reg.replace(island + ' – ', '');
                const btn = document.createElement('button');
                btn.className = 'region-btn';
                btn.textContent = clean;
                btn.dataset.region = reg;
                btn.addEventListener('click', () => showDishes(reg));
                regionButtonsDiv.appendChild(btn);
            });
        }

        islandSelection.style.display = 'none';
        regionSelection.style.display = 'block';
        dishesContainer.style.display = 'none';
        modalTitle.textContent = `Regions of ${island}`;
    });
});

function showDishes(region) {
    const filtered = allFoods.filter(f => f.region === region);
    modalMenuGrid.innerHTML = '';

    if (filtered.length === 0) {
        modalMenuGrid.innerHTML = '<p class="no-items">No dishes available yet in this region.</p>';
    } else {
        filtered.forEach(food => {
            const price = parseFloat(food.price || 0).toFixed(2);
            const imgSrc = food.image_path?.trim() ? food.image_path : defaultImg;
            const div = document.createElement('div');
            div.className = 'menu__content';
            div.innerHTML = `
                <img src="${imgSrc}" class="menu__img">
                <h3 class="menu__name">${food.name}</h3>
                <span class="menu__detail">${food.description || ''}</span>
                <span class="menu__region">${food.region.replace(currentSelectedIsland+' – ','')}</span>
                <span class="menu__price">₱${price}</span>
                <button class="button menu__button add-to-cart"
                    data-id="${food.name}"
                    data-name="${food.name}"
                    data-price="${price}">
                    <i class='bx bx-cart'></i> Add to Basket
                </button>
            `;
            modalMenuGrid.appendChild(div);
        });
    }

    regionSelection.style.display = 'none';
    dishesContainer.style.display = 'block';
    modalTitle.textContent = region.replace(/.* – /, '');
}

backToIslands.addEventListener('click', resetToIslands);

backToRegions.addEventListener('click', () => {
    dishesContainer.style.display = 'none';
    regionSelection.style.display = 'block';
    modalTitle.textContent = `Regions of ${currentSelectedIsland}`;
});

// Cart system in modal
document.addEventListener('click', function (e) {
    if (e.target.closest('.add-to-cart')) {
        const btn = e.target.closest('.add-to-cart');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const item = {
            id: btn.dataset.id,
            name: btn.dataset.name,
            price: parseFloat(btn.dataset.price),
            qty: 1
        };
        const existing = cart.find(i => i.id === item.id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push(item);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        // update header cart count
        const countEl = document.getElementById('cart-count');
        if (countEl) {
            countEl.textContent = cart.reduce((sum, it) => sum + it.qty, 0);
        }
        btn.innerHTML = "<i class='bx bx-check'></i> Added!";
        setTimeout(() => {
            btn.innerHTML = "<i class='bx bx-cart'></i> Add to Basket";
        }, 1000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>