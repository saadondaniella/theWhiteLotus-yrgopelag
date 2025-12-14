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
                <li class="navbar-item"><a class="navbar-text" href="index.php">DESTINATION</a></li>
                <li class="navbar-item"><a class="navbar-text" href="booking.php">ROOMS</a></li>
                <li class="navbar-item"><a class="navbar-text" href="index.php">THE ISLAND</a></li>
                <li class="navbar-item"><a class="navbar-text" href="index.php">CONTACT</a></li>
                <li class="navbar-item"><a class="navbar-text" href="#">FEATURES</a></li>
            </ul>
        </nav>
    </header>