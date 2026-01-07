<?php

declare(strict_types=1);

require __DIR__ . '/src/database.php';
require __DIR__ . '/src/functions.php';

$statement = $database->query('SELECT slug, name, price_per_night FROM rooms ORDER BY price_per_night DESC');
$roomsFromDatabase = $statement->fetchAll(PDO::FETCH_ASSOC);

$images = [
    'luxury' => 'pictures/room-luxury.png',
    'standard' => 'pictures/room-standard.png',
    'budget' => 'pictures/room-budget.png',
];

$rooms = [];

foreach ($roomsFromDatabase as $room) {
    $rooms[] = [
        'slug' => $room['slug'],
        'name' => $room['name'],
        'price' => (int) $room['price_per_night'],
        'image' => $images[$room['slug']] ?? 'pictures/room-budget.png',
    ];
}

require __DIR__ . '/src/header.php';
?>

<main>
    <section class="hero">
        <img
            class="hero-bg"
            src="pictures/island-hero-1.png"
            alt="Cozea Island">

        <div class="hero-box">
            <h1 class="hero-title">WELCOME TO COZEA ISLAND</h1>

            <p class="hero-text">
                Home to the exclusive resort The White Lotus.<br>
                An island of beauty, secrets, and unexpected turns — where paradise reveals
                more than you came for.<br>
                Book, if you dare.
            </p>

            <a class="room-button hero-cta" href="booking.php">
                → CHECK AVAILABLE ROOMS
            </a>
        </div>
    </section>
    <section class="promo-split">
        <div class="promo-split-image">
            <img
                src="pictures/relaxroom.jpg"
                alt="Cozea Island experience">
        </div>

        <div class="promo-split-content">
            <h2 class="promo-split-title">STAY LONGER. SCORE MORE.</h2>

            <p class="promo-split-text">
                Combine a <strong>3-night stay</strong> with a
                <strong>full tier set</strong> from 24 € total. Yes, there’s a discount.
            </p>

            <a class="room-button promo-split-cta" href="booking.php">
                → Build your stay
            </a>
        </div>
    </section>

    <section class="rooms">
        <h2 class="rooms-title">OUR ROOMS</h2>

        <div class="rooms-list">
            <?php foreach ($rooms as $room): ?>
                <article class="room-card">
                    <img
                        class="room-image"
                        src="<?= escapeHtml($room['image']); ?>"
                        alt="<?= escapeHtml($room['name']); ?> room" />

                    <div class="room-overlay">
                        <p class="room-name">
                            <?= escapeHtml(strtoupper($room['name'])); ?> <?= (int) $room['price']; ?>€
                        </p>

                        <a class="room-button" href="booking.php?room=<?= urlencode($room['slug']); ?>">
                            BOOK HERE
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </section>

    <article class="intro-story">
        <h2 class="intro-story-title">Let us tell you a little bit of Cozea Island</h2>

        <p class="intro-story-text">
            Far from the mainland, surrounded by endless shades of turquoise, lies Cozea
            Island — a place whispered about rather than spoken of openly.
        </p>

        <p class="intro-story-text">
            Once a quiet paradise, Cozea has long attracted those seeking escape:
            artists, wanderers, the wealthy, and the restless. Some arrive in search of
            beauty, others in search of themselves. Few leave unchanged.
        </p>

        <p class="intro-story-text">
            At the heart of the island stands <strong>The White Lotus</strong>, a resort
            known as much for its serenity as for its secrets. Beneath its calm surface,
            stories unfold — of desire, power, and unexpected turns.
        </p>

        <p class="intro-story-text">
            On Cozea Island, paradise is real. But so are the consequences.
        </p>
    </article>
</main>

<?php require __DIR__ . '/src/footer.php'; ?>