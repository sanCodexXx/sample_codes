<?php require 'includes/auth.php'; ?>
<?php include 'includes/header.php'; ?>
<main class="l-main">
    <section class="about section bd-container" id="about" style="padding-top: calc(var(--header-height) + 4rem);">
        <span class="section-subtitle">Who we are</span>
        <h2 class="section-title">Authentic Filipino Cuisine</h2>
        <div class="about__container bd-grid">
            <img src="assets/restaurant.jpg" alt="Our Restaurant" class="about__img">
            <div class="about__data">
                <p style="font-size:0.8rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--gold);margin-bottom:0.5rem;">Our Story</p>
                <h3 style="font-family:var(--display-font);font-size:2rem;color:var(--cream);line-height:1.2;margin-bottom:1.5rem;">
                    Where every dish<br>tells a story.
                </h3>
                <p class="about__description">
                    Filipino cuisine is a vibrant fusion of indigenous, Spanish, Chinese,
                    and American influences. Each dish is a reflection of our history,
                    our resilience, and our love for bringing people together at the table.
                </p>
                <p style="color:var(--text-muted);font-size:0.95rem;">
                    Our mission is simple: to bring the true, unfiltered taste of the Philippines
                    to your table — prepared with care, served with pride.
                </p>
                <a href="menu.php" class="button" style="margin-top:1.5rem;">Discover the Menu</a>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
