<?php require 'includes/auth.php'; ?>
<?php include 'includes/header.php'; ?>
<main class="l-main">
    <section class="contact section bd-container" id="contact">
        <span class="section-subtitle">Get in touch</span>
        <h2 class="section-title">Contact us</h2>
        <div class="contact__container bd-grid">
            <div class="contact__form">
                <form method="post">
                    <h2 class="section-title">Send us a message</h2>
                    <div class="form-group"><input type="text" name="name" class="form-input" placeholder="Your Name" required></div>
                    <div class="form-group"><input type="email" name="email" class="form-input" placeholder="Email" required></div>
                    <div class="form-group"><textarea name="message" class="form-input" rows="4" placeholder="Message" required></textarea></div>
                    <button type="submit" class="submit-button">Send Message</button>
                </form>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>