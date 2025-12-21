<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$errors = [];
$successMessage = null;

if (isset($_POST['name'], $_POST['email'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name === '') {
        $errors[] = 'The name field is missing.';
    }

    if ($email === '') {
        $errors[] = 'The email field is missing.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'The email is not a valid email address.';
    }

    if (empty($errors)) {
        $successMessage = "Thanks, $name, for signing up!";
    }
}
?>

<section class="contact-newsletter" id="newsletter">
    <div class="contact-newsletter-inner">
        <div class="contact-block">
            <h2 class="contact-block-title">CONTACT The White Lotus</h2>
            <p class="contact-block-text">
                Cozea Travel Advisors are available to<br>
                answer all of your questions.
            </p>

            <nav class="contact-block-links" aria-label="Contact links">
                <a class="contact-block-link" href="mailto:saadondaniella@gmail.com">Email</a>
            </nav>
            <a class="header-phone" href="tel:+46735906163">
                <img
                    class="header-phone-symbol"
                    src="pictures/phone-symbol.png"
                    alt="" />
                <span class="header-phone-text">+46 735 906163</span>
            </a>
        </div>

        <div class="newsletter-block">
            <h2 class="newsletter-block-title">NEWSLETTER</h2>
            <p class="newsletter-block-text">Subscribe to receive news from The White Lotus</p>

            <form class="newsletter-block-form" method="post" action="#newsletter">
                <label class="sr-only" for="name">Your name</label>
                <input
                    class="newsletter-block-input"
                    id="name"
                    name="name"
                    type="text"
                    placeholder="Your name"
                    required>

                <label class="sr-only" for="email">Your email</label>
                <input
                    class="newsletter-block-input"
                    id="email"
                    name="email"
                    type="email"
                    placeholder="Your email"
                    required>

                <button class="newsletter-block-button" type="submit">
                    SIGN UP
                </button>
            </form>

            <?php if (!empty($errors)) : ?>
                <ul class="newsletter-block-messages" role="alert">
                    <?php foreach ($errors as $error) : ?>
                        <li><?= escapeHtml($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($successMessage !== null) : ?>
                <p class="newsletter-block-success" role="status">
                    <?= escapeHtml($successMessage) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="footer-content">
        <p class="footer-text">
            © The White Lotus, Cozea Island
        </p>

        <nav class="footer-nav">
            <a class="footer-link" href="contact.php">Contact</a>
            <a class="footer-link" href="index.php">Privacy Policy</a>
            <a class="footer-link" href="index.php">Terms & Conditions</a>
        </nav>
    </div>
</footer>
<button
    class="scrolltop"
    id="scrollTopButton"
    aria-label="Back to top">
    <img src="pictures/lotus-icon.png" alt="lotus flower" />
</button>
<script src="public/script.js"></script>
</body>

</html>