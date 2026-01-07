<?php

declare(strict_types=1);

require __DIR__ . '/src/header.php';
?>

<main class="island-page">
    <section class="island-hero">
        <div class="island-hero-overlay">
            <h1 class="island-hero-title">COZEA ISLAND</h1>
            <p class="island-hero-lead">
                Cozea Island is not the kind of place you find.<br>
                It’s the kind of place you end up in — when you needed it most.
            </p>

            <p class="island-hero-sub">
                January 2026 · Three rooms · Quiet agreements
            </p>

            <a class="island-hero-cta" href="booking.php">Explore rooms & availability</a>
        </div>
    </section>

    <section class="island-highlights" aria-label="Island highlights">
        <h2 class="island-section-title">THE ISLAND HIGHLIGHTS</h2>

        <div class="island-cards">
            <article class="island-card">
                <h3 class="island-card-title">LAGOON & CLIFFS</h3>
                <p class="island-card-text">
                    Turquoise water, warm stone, and long horizons.
                    The kind of view that makes you stop talking.
                </p>
            </article>

            <article class="island-card">
                <h3 class="island-card-title">THE LOTUS PATH</h3>
                <p class="island-card-text">
                    A hidden route between palms and silence.
                    Follow it once — you’ll want to again.
                </p>
            </article>

            <article class="island-card">
                <h3 class="island-card-title">THE QUIET SIDE</h3>
                <p class="island-card-text">
                    No crowds. No rush. Just salt air and space.
                    The island keeps this part for those who listen.
                </p>
            </article>
        </div>
    </section>

    <section class="island-story" aria-label="Cinematic island story">
        <article class="island-split">
            <div class="island-split-media">
                <img src="pictures/boat.jpg" alt="Arrival by boat to Cozea Island">
            </div>
            <div class="island-split-content">
                <h2 class="island-split-title">THE ARRIVAL</h2>
                <p class="island-split-text">
                    The boat cuts through turquoise water. By the time you step onto the shore,
                    you already feel it: something here is watching back.
                </p>
                <p class="island-split-text">
                    You came for the beauty. You stay for what it reveals.
                </p>
            </div>
        </article>

        <article class="island-split">
            <div class="island-split-media">
                <img src="pictures/girl.jpg" alt="girl at the beach on Cozea Island">
            </div>
            <div class="island-split-content">
                <h2 class="island-split-title">THE SECRET</h2>
                <p class="island-split-text">
                    People come here for peace. But Cozea has a habit of revealing what you try to hide.
                    Beauty always comes with a price — and the island never forgets.
                </p>
                <p class="island-split-text">
                    Some guests return home lighter. Others return… different.
                </p>
            </div>
        </article>
        <article class="island-split island-split--reverse">
            <div class="island-split-media">
                <img src="pictures/cozeaoverwiew.png" alt="Island overview of Cozea">
            </div>
            <div class="island-split-content">
                <h2 class="island-split-title">THE ISLAND RHYTHM</h2>
                <p class="island-split-text">
                    Mornings move slowly here. Time behaves differently.
                    No schedules, no noise — only sun, shadows, and the soft feeling of disappearing.
                </p>
                <p class="island-split-text">
                    On Cozea, rest isn’t something you do. It’s something that happens to you.
                </p>
            </div>
        </article>

    </section>

    <section class="island-info" aria-label="January travel info">
        <div class="island-info-card">
            <h2 class="island-info-title">RULES OF JANUARY</h2>
            <p class="island-info-text">
                Cozea Island only accepts visitors during January 2026.
                Plan wisely — the island is small, and the rooms are few.
            </p>

            <ul class="island-info-list">
                <li>Booking calendar is limited to January 2026</li>
                <li>Check-in: 15:00</li>
                <li>Check-out: 11:00</li>
                <li>Three rooms only (Budget, Standard, Luxury)</li>
            </ul>

            <a class="island-info-cta" href="booking.php">Check availability</a>
        </div>
    </section>
</main>

<?php require __DIR__ . '/src/footer.php'; ?>