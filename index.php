<?php

declare(strict_types=1);

$rooms = [
    [
        'slug' => 'luxury',
        'name' => 'Luxury',
        'price' => 8,
        'image' => '/public/pictures/room-luxury.png',
    ],
    [
        'slug' => 'standard',
        'name' => 'Standard',
        'price' => 5,
        'image' => '/public/pictures/room-standard.png',
    ],
    [
        'slug' => 'budget',
        'name' => 'Budget',
        'price' => 2,
        'image' => '/public/pictures/room-budget.png',
    ],
];

require __DIR__ . '/src/header.php';
?>

<main>
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
    <section class="rooms">
        <h2 class="rooms-title">OUR ROOMS</h2>

        <div class="rooms-list">
            <?php foreach ($rooms as $room): ?>
                <article class="room-card">
                    <img
                        class="room-image"
                        src="<?= $room['image']; ?>"
                        alt="<?= $room['name']; ?> room" />

                    <div class="room-overlay">
                        <p class="room-name">
                            <?= strtoupper($room['name']); ?> <?= $room['price']; ?>€
                        </p>

                        <a class="room-button" href="/booking.php?room=<?= $room['slug']; ?>">
                            BOOK HERE
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <article class="island-story">
        <h2 class="island-story-title">THE ISLAND OF COZEA</h2>

        <p class="island-story-text">
            Far from the mainland, surrounded by endless shades of turquoise, lies Cozea
            Island — a place whispered about rather than spoken of openly.
        </p>

        <p class="island-story-text">
            Once a quiet paradise, Cozea has long attracted those seeking escape:
            artists, wanderers, the wealthy, and the restless. Some arrive in search of
            beauty, others in search of themselves. Few leave unchanged.
        </p>

        <p class="island-story-text">
            At the heart of the island stands <strong>The White Lotus</strong>, a resort
            known as much for its serenity as for its secrets. Beneath its calm surface,
            stories unfold — of desire, power, and unexpected turns.
        </p>

        <p class="island-story-text">
            On Cozea Island, paradise is real. But so are the consequences.
        </p>
    </article>
</main>
<?php require __DIR__ . '/src/footer.php'; ?>
<script src="/public/script.js"></script>
</body>

</html>