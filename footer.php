</main>
<footer class="footer section bd-container">
    <div class="footer__container bd-grid">
        <div class="footer__content">
            <a href="#" class="footer__logo">Authentic Filipino Cuisine</a>
            <span class="footer__description">Traditional flavors</span>
            <div>
                <a href="#" class="footer__social"><i class='bx bxl-facebook'></i></a>
                <a href="#" class="footer__social"><i class='bx bxl-instagram'></i></a>
                <a href="#" class="footer__social"><i class='bx bxl-twitter'></i></a>
            </div>
        </div>
        <div class="footer__content">
            <h3 class="footer__title">Services</h3>
            <ul>
                <li><a href="#" class="footer__link">Delivery</a></li>
                <li><a href="#" class="footer__link">Catering</a></li>
                <li><a href="#" class="footer__link">Recipes</a></li>
                <li><a href="#" class="footer__link">Reservations</a></li>
            </ul>
        </div>
        <div class="footer__content">
            <h3 class="footer__title">Information</h3>
            <ul>
                <li><a href="#" class="footer__link">About Us</a></li>
                <li><a href="contact.php" class="footer__link">Contact us</a></li>
                <li><a href="#" class="footer__link">Privacy policy</a></li>
                <li><a href="#" class="footer__link">Terms of service</a></li>
            </ul>
        </div>
        <div class="footer__content">
            <h3 class="footer__title">Address</h3>
            <ul>
                <li>Philippines</li>
                <li>AFC@gmail.com</li>
            </ul>
        </div>
    </div>
    <p class="footer__copy">&#169; 2025 Authentic Filipino Cuisine. All rights reserved</p>
</footer>
<script src="https://unpkg.com/scrollreveal"></script>
<script>
    const sr = ScrollReveal({ origin: 'top', distance: '30px', duration: 2000, reset: true });
    sr.reveal(`.home__data, .about__data, .about__img, .services__content, .menu__content, .contact__data, .contact__button, .footer__content`, { interval: 200 });

    const navMenu = document.getElementById('nav-menu');
    const navToggle = document.getElementById('nav-toggle');
    if (navToggle) navToggle.addEventListener('click', () => navMenu.classList.toggle('show-menu'));
    document.querySelectorAll('.nav__link').forEach(link => link.addEventListener('click', () => navMenu.classList.remove('show-menu')));

    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll('.nav__link').forEach(link => {
        if (link.getAttribute('href') === currentPage) link.classList.add('active-link');
        else link.classList.remove('active-link');
    });

    window.addEventListener('scroll', () => {
        const header = document.getElementById('header');
        header.classList.toggle('scroll-header', window.scrollY >= 200);
        const scrollTop = document.getElementById('scroll-top');
        scrollTop.classList.toggle('show-scroll', window.scrollY >= 560);
    });

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const total = cart.reduce((sum, item) => sum + item.qty, 0);
        const el = document.getElementById('cart-count');
        if (el) el.textContent = total;
    }
    updateCartCount();
</script>
</body>
</html>