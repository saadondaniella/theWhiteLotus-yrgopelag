<?php

declare(strict_types=1);

require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/header.php';

$rituals = [
    [
        'title' => 'Sunrise Coffee Club',
        'subtitle' => 'Economy ritual 2€',
        'text' => "Early mornings are for the curious.\nFresh coffee, quiet conversations, and the comforting feeling that you’re up before everyone else — on purpose.",
        'image' => 'pictures/coffee.jpeg',
        'alt' => 'Sunrise Coffee Club',
    ],
    [
        'title' => 'Library & Fireplace Access',
        'subtitle' => 'Basic ritual 5€',
        'text' => "Soft chairs. Old books. A fireplace that’s always just warm enough.\nTime slows down here — whether you planned for it or not.",
        'image' => 'pictures/feature-library.png',
        'alt' => 'Library & Fireplace Access',
    ],
    [
        'title' => 'Movie Night Under the Palms',
        'subtitle' => 'Premium ritual 10€',
        'text' => "Outdoor cinema beneath swaying palms and an overly dramatic sky.\nBlankets provided. Opinions encouraged.",
        'image' => 'pictures/movienight.png',
        'alt' => 'Movie Night Under the Palms',
    ],
    [
        'title' => 'Island Party Night',
        'subtitle' => 'Superior ritual 17€',
        'text' => "One night, every night. No schedule.\nMusic, lights, and an island that forgets it ever needed to sleep.",
        'image' => 'pictures/party2.png',
        'alt' => 'Island Party Night',
    ],
];
?>


<section class="features">
    <h1 class="features-title">DISCOVER THE WHITE LOTUS RITUALS</h1>

    <div class="features-text">
        <p>Stuff we do because it feels right.</p>
        <p>These rituals are just small moments made better — coffee before everyone else is awake, movies under the stars, nights that go on a bit too long.
            Join when you want. Skip when you don’t. No pressure.</p>
        <p>Want a little tip? Collect all rituals to complete the hotel-specific category.</p>
    </div>

    <div class="rituals-grid">
        <?php foreach ($rituals as $ritual) : ?>
            <article class="ritual-card">
                <img class="ritual-image" src="<?= escapeHtml($ritual['image']) ?>" alt="<?= escapeHtml($ritual['alt']) ?>">

                <div class="ritual-body">
                    <h2 class="ritual-title"><?= escapeHtml($ritual['title']) ?></h2>
                    <p class="ritual-subtitle"><?= escapeHtml($ritual['subtitle']) ?></p>
                    <p class="ritual-text"><?= nl2br(escapeHtml($ritual['text'])) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <a class="features-cta" href="booking.php">Book here</a>
</section>

<?php require __DIR__ . '/src/footer.php'; ?>