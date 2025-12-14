<?php

declare(strict_types=1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>The White Lotus</title>
    <link rel="stylesheet" href="/public/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kaisei+Decol&display=swap" rel="stylesheet">

</head>

<body>
    <header class="header">
        <div class="header-top">
            <a class="header-phone" href="tel:+46735906163">
                <img
                    class="header-phone-symbol"
                    src="/public/pictures/phone-symbol.png"
                    alt="" />
                <span class="header-phone-text">+46 735 906163</span>
            </a>

            <div class="header-icons">
                <a class="navbar-icon" href="#" aria-label="Instagram">
                    <img class="navbar-icon-image" src="/public/pictures/instagram-icon.png" alt="Instagram icon" />
                </a>
                <a class="navbar-icon" href="#" aria-label="Facebook">
                    <img class="navbar-icon-image" src="/public/pictures/facebook-icon.png" alt="Facebook icon" />
                </a>
                <a class="navbar-icon" href="#" aria-label="Mail">
                    <img class="navbar-icon-image" src="/public/pictures/mail-icon.png" alt="MAil icon" />
                </a>
                <a class="navbar-icon" href="#" aria-label="Telefon">
                    <img class="navbar-icon-image" src="/public/pictures/phone-icon.png" alt="phone icon" />
                </a>
            </div>
        </div>

        <div class="header-brand">
            <img
                class="header-logo"
                src="/public/pictures/logga.png"
                alt="The White Lotus" />
        </div>

        <hr class="header-divider" />

        <nav class="navbar" aria-label="Huvudmeny">
            <ul class="navbar-list">
                <li class="navbar-item"><a class="navbar-text" href="#">DESTINATION</a></li>
                <li class="navbar-item"><a class="navbar-text" href="#">ROOMS</a></li>
                <li class="navbar-item"><a class="navbar-text" href="#">THE ISLAND</a></li>
                <li class="navbar-item"><a class="navbar-text" href="#">CONTACT</a></li>
                <li class="navbar-item"><a class="navbar-text" href="#">FEATURES</a></li>
            </ul>
        </nav>
    </header>
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">WELCOME TO COZEA ISLAND</h1>

            <p class="hero-text">
                Home to the exclusive resort The White Lotus. <br>An island of beauty, secrets, and unexpected turns — where paradise reveals
                more than you came for.<br />
                Book, if you dare.
            </p>
            <a class="room-button hero-cta" href="/booking.php">
                CHECK AVAILABLE ROOMS
            </a>
        </div>
        <div class="hero-image">
            <img
                src="/public/pictures/island-hero.png"
                alt="Cozea Island" />
        </div>
    </section>