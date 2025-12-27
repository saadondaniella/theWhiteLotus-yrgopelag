<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>The White Lotus – Cozea Island</title>
    <link rel="stylesheet" href="./public/style.css">
    <link rel="stylesheet" href="./public/style2.css">
    <link rel="stylesheet" href="./public/styleB.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kaisei+Decol&display=swap" rel="stylesheet">

</head>

<body>
    <header class="header">
        <div class="header-top">
            <div class="header-contact">
                <div class="header-rating">
                    <?php for ($i = 1; $i <= $maxStars; $i++) : ?>
                        <?php if ($i <= $hotelRating) : ?>
                            <span class="star filled">★</span>
                        <?php else : ?>
                            <span class="star">☆</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="header-icons">
                <a class="navbar-icon" href="https://www.instagram.com/" aria-label="Instagram">
                    <img class="navbar-icon-image" src="pictures/instagram-icon.png" alt="Instagram icon" />
                </a>
                <a class="navbar-icon" href="#" aria-label="Facebook">
                    <img class="navbar-icon-image" src="pictures/facebook-icon.png" alt="Facebook icon" />
                </a>
                <a class="navbar-icon" href="#" aria-label="Mail">
                    <img class="navbar-icon-image" src="pictures/mail-icon.png" alt="MAil icon" />
                </a>
                <a class="navbar-icon" href="#" aria-label="Telefon">
                    <img class="navbar-icon-image" src="pictures/phone-icon.png" alt="phone icon" />
                </a>
            </div>
        </div>

        <div class="header-brand">
            <img
                class="header-logo"
                src="pictures/logga.png"
                alt="The White Lotus" />
        </div>

        <hr class="header-divider" />

        <nav class="navbar" aria-label="Huvudmeny">
            <ul class="navbar-list">
                <li class="navbar-item"><a class="navbar-text" href="index.php">DESTINATION</a></li>
                <li class="navbar-item"><a class="navbar-text" href="booking.php">ROOMS</a></li>
                <li class="navbar-item"><a class="navbar-text" href="index.php">THE ISLAND</a></li>
                <li class="navbar-item"><a class="navbar-text" href="contact.php">CONTACT</a></li>
                <li class="navbar-item"><a class="navbar-text" href="features.php">FEATURES</a></li>
            </ul>
        </nav>
    </header>