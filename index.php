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
</main>
<?php require __DIR__ . '/src/footer.php'; ?>
</body>

</html>