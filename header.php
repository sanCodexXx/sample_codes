<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentic Filipino Cuisine</title>
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="assets/logo.png">
</head>
<body>
    <a href="#" class="scrolltop" id="scroll-top">
        <i class='bx bx-chevron-up scrolltop__icon'></i>
    </a>
    <header class="l-header" id="header">
        <nav class="nav bd-container">
            <a href="home.php" class="nav__logo">
                <img src="assets/logo.png" alt="Logo" class="nav__logo-img">
                <span class="nav__logo-text">Authentic Filipino Cuisine</span>
            </a>
            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item"><a href="home.php" class="nav__link">Home</a></li>
                    <li class="nav__item"><a href="menu.php" class="nav__link">Menu</a></li>
                    <li class="nav__item"><a href="about.php" class="nav__link">About</a></li>
                    <li class="nav__item"><a href="contact.php" class="nav__link">Contact</a></li>
                    <li class="nav__item">
                        <a href="cart.php" class="nav__link cart-link">
                            <i class='bx bx-cart'></i>
                            <span id="cart-count" class="cart-count">0</span>
                        </a>
                    </li>
                    <li class="nav__item">
                        <a href="logout.php" class="nav__link nav__logout"><i class='bx bx-log-out'></i> Logout</a>
                    </li>
                </ul>
            </div>
            <div class="nav__toggle" id="nav-toggle">
                <i class='bx bx-menu'></i>
            </div>
        </nav>
    </header>