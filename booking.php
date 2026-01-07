<?php

declare(strict_types=1);

require_once __DIR__ . '/src/database.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/centralbank.php';
require_once __DIR__ . '/config.php';

$errors = [];
$successMessage = null;

$discountPercent = 0;
$discountAmount = 0;
$discountLabel = null;


if ($centralBankApiKey === false || $centralBankApiKey === '') {
    $errors[] = 'Hotel configuration error: API key is missing.';
}

$roomContent = [
    'luxury' => [
        'text' => 'This is the ultimate overnight stay. Effortless luxury, calming details, and a seamless flow between room and ocean. You could save money — or you could be happy. Don’t be cheap. Book it. Your health is at stake. There may or may not be a freezer full of ice cream. Do not forget to add pool in the features.',
        'bullets' => [
            'Total Area 100 sqm',
            'Indoor/outdoor showers',
            'Your own massage therapist',
        ],
    ],
    'standard' => [
        'text' => 'Inspired by oriental elegance, rich textures, and calming tones, this room is designed for slowing down properly. Candles, shadows, and the comforting feeling that you’re exactly where you should be. You could book something simpler — but why would you? This room knows things about you.',
        'bullets' => [
            'Total Area 80 sqm',
            'Own porch to the ocean',
            'Towels and bathrobes',
        ],
    ],
    'budget' => [
        'text' => 'Perfect if you want comfort, views, and that island feeling — without going all in. Clean, airy, and calm,
with front-row access to turquoise waters and palm-lined beaches. A smart choice that still delivers a stay you’ll love (and want to book fast).',
        'bullets' => [
            'Total Area 40 sqm',
            'Own porch to the ocean',
            'Towels and bathrobes',
        ],
    ],
];

$roomsStatement = $database->query(
    'SELECT id, slug, name, price_per_night
     FROM rooms
     ORDER BY price_per_night DESC'
);
$rooms = $roomsStatement->fetchAll(PDO::FETCH_ASSOC);

$statement = $database->prepare(
    'SELECT room_id, arrival_date, departure_date
     FROM bookings
     WHERE arrival_date <= :endDate
       AND departure_date >= :startDate'
);

$statement->execute([
    ':startDate' => '2026-01-01',
    ':endDate' => '2026-01-31',
]);

$bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

$bookedDaysByRoomId = [];

foreach ($bookings as $booking) {
    $roomId = (int) $booking['room_id'];

    $arrival = new DateTime((string) $booking['arrival_date']);
    $departure = new DateTime((string) $booking['departure_date']);

    $departure->modify('-1 day');

    $current = clone $arrival;

    while ($current <= $departure) {
        if ($current->format('Y-m') === '2026-01') {
            $dayNumber = (int) $current->format('j');
            $bookedDaysByRoomId[$roomId][$dayNumber] = true;
        }
        $current->modify('+1 day');
    }
}

$featuresStatement = $database->query(
    'SELECT id, name, cost, activity, tier
     FROM features
     WHERE is_active = 1
     ORDER BY cost ASC'
);

$features = $featuresStatement->fetchAll(PDO::FETCH_ASSOC);

$arrivalDate = '2026-01-01';
$departureDate = '2026-01-02';
$roomSlug = '';
$selectedFeatureIds = [];
$totalCost = null;
$nights = null;


if (isset($_GET['success']) && $_GET['success'] === '1') {
    $successRoom = isset($_GET['room']) ? (string) $_GET['room'] : '';
    $successArrival = isset($_GET['arrival']) ? (string) $_GET['arrival'] : '';
    $successDeparture = isset($_GET['departure']) ? (string) $_GET['departure'] : '';
    $successTotal = isset($_GET['total']) ? (string) $_GET['total'] : '';
    $successFeatures = isset($_GET['features']) ? (string) $_GET['features'] : '';

    if ($successRoom !== '' && $successArrival !== '' && $successDeparture !== '') {
        $lines = [];
        $lines[] = 'Enjoy the room ' . $successRoom . '.';
        $lines[] = 'From ' . $successArrival . ' to ' . $successDeparture . '.';

        if ($successTotal !== '') {
            $lines[] = 'Total cost: ' . $successTotal . ' €.';
        }

        if ($successFeatures !== '') {
            $lines[] = 'Features: ' . $successFeatures . '.';
        }

        $lines[] = 'Welcome to The White Lotus 🌴';

        $successMessage = implode("\n", $lines);
    } else {
        $successMessage = "Booking confirmed!\nEnjoy your stay on Cozea Island 🌴";
    }
}


if (isset($_POST['room_slug'], $_POST['arrival_date'], $_POST['departure_date'])) {
    $guestName = isset($_POST['guest_name']) ? trim((string) $_POST['guest_name']) : '';
    $transferCode = isset($_POST['transfer_code']) ? trim((string) $_POST['transfer_code']) : '';

    $roomSlug = (string) $_POST['room_slug'];
    $arrivalDate = (string) $_POST['arrival_date'];
    $departureDate = (string) $_POST['departure_date'];

    if (isset($_POST['features']) && is_array($_POST['features'])) {
        $selectedFeatureIds = array_map('intval', $_POST['features']);
    } else {
        $selectedFeatureIds = [];
    }

    $wantsConfirmBooking = ($guestName !== '' && $transferCode !== '');
    $enteredOneButNotBoth = (($guestName !== '' && $transferCode === '') || ($guestName === '' && $transferCode !== ''));

    if ($enteredOneButNotBoth) {
        $errors[] = 'To confirm a booking, enter BOTH guest name and transfer code. (Or leave both empty to only calculate total.)';
    }

    if ($guestName !== '' && strcasecmp($guestName, (string) $hotelOwnerUser) === 0) {
        $errors[] = 'Guest name cannot be the same as the hotel owner.';
    }

    if ($arrivalDate < '2026-01-01' || $arrivalDate > '2026-01-31') {
        $errors[] = 'Arrival date must be within January 2026.';
    }

    if ($departureDate < '2026-01-01' || $departureDate > '2026-01-31') {
        $errors[] = 'Departure date must be within January 2026.';
    }

    if ($departureDate <= $arrivalDate) {
        $errors[] = 'Departure must be after arrival.';
    }

    $selectedRoom = null;
    foreach ($rooms as $room) {
        if ((string) $room['slug'] === $roomSlug) {
            $selectedRoom = $room;
            break;
        }
    }

    if ($selectedRoom === null) {
        $errors[] = 'Please choose a room.';
    }

    if ($errors === [] && $selectedRoom !== null) {
        $checkStatement = $database->prepare('
            SELECT COUNT(*)
            FROM bookings
            WHERE room_id = :room_id
              AND NOT (departure_date <= :arrival_date OR arrival_date >= :departure_date)
        ');

        $checkStatement->execute([
            ':room_id' => (int) $selectedRoom['id'],
            ':arrival_date' => $arrivalDate,
            ':departure_date' => $departureDate,
        ]);

        $bookingsCount = (int) $checkStatement->fetchColumn();

        if ($bookingsCount > 0) {
            $errors[] = 'That room is not available for those dates.';
        }
    }

    if ($errors === [] && $selectedRoom !== null) {
        $arrival = new DateTime($arrivalDate);
        $departure = new DateTime($departureDate);
        $nights = (int) $arrival->diff($departure)->days;

        $roomPricePerNight = (int) $selectedRoom['price_per_night'];

        $featuresCostPerNight = 0;
        foreach ($features as $feature) {
            if (in_array((int) $feature['id'], $selectedFeatureIds, true)) {
                $featuresCostPerNight += (int) $feature['cost'];
            }
        }

        $totalCost = ($roomPricePerNight + $featuresCostPerNight) * $nights;

        $tiersWithActivities = [];

        foreach ($features as $feature) {
            $featureId = (int) $feature['id'];

            if (!in_array($featureId, $selectedFeatureIds, true)) {
                continue;
            }

            $tier = (string) $feature['tier'];
            $activity = (string) $feature['activity'];

            if (!isset($tiersWithActivities[$tier])) {
                $tiersWithActivities[$tier] = [];
            }

            $tiersWithActivities[$tier][$activity] = true;
        }

        $requiredActivities = ['water', 'games', 'wheels', 'hotel-specific'];

        foreach ($tiersWithActivities as $tier => $activitiesMap) {
            $hasFullTier = true;

            foreach ($requiredActivities as $requiredActivity) {
                if (!isset($activitiesMap[$requiredActivity])) {
                    $hasFullTier = false;
                    break;
                }
            }

            if ($hasFullTier) {
                $discountPercent = 20;
                $discountLabel = 'Tier Collector (20% off)';
                break;
            }
        }

        if ($discountPercent > 0 && $totalCost !== null) {
            $discountAmount = (int) round($totalCost * ($discountPercent / 100));
            $totalCost = max(0, $totalCost - $discountAmount);
        }
    }


    if ($errors === [] && $wantsConfirmBooking && $selectedRoom !== null && $totalCost !== null) {
        $validation = centralbankValidateTransferCode($transferCode, $totalCost);

        if (!$validation['ok']) {
            $errorText = (string) ($validation['error'] ?? 'Unknown error');

            if (str_contains($errorText, 'Could not reach central bank') || str_contains($errorText, 'cURL error')) {
                $errors[] = 'The Central Bank is not responding right now. Please wait a few seconds and try again.';
            } else {
                $errors[] = 'Transfer code validation failed: ' . $errorText;
            }
        }
    }

    if ($errors === [] && $wantsConfirmBooking && $selectedRoom !== null && $totalCost !== null) {
        $database->beginTransaction();

        try {
            $insertBooking = $database->prepare('
                INSERT INTO bookings (room_id, guest_name, arrival_date, departure_date, transfer_code, total_cost, created_at)
                VALUES (:room_id, :guest_name, :arrival_date, :departure_date, :transfer_code, :total_cost, :created_at)
            ');

            $insertBooking->execute([
                ':room_id' => (int) $selectedRoom['id'],
                ':guest_name' => $guestName,
                ':arrival_date' => $arrivalDate,
                ':departure_date' => $departureDate,
                ':transfer_code' => $transferCode,
                ':total_cost' => $totalCost,
                ':created_at' => date('c'),
            ]);

            $bookingId = (int) $database->lastInsertId();

            if ($selectedFeatureIds !== []) {
                $insertFeature = $database->prepare('
                    INSERT INTO booking_features (booking_id, feature_id)
                    VALUES (:booking_id, :feature_id)
                ');

                foreach ($selectedFeatureIds as $featureId) {
                    $insertFeature->execute([
                        ':booking_id' => $bookingId,
                        ':feature_id' => $featureId,
                    ]);
                }
            }

            $featuresUsed = [];
            foreach ($features as $feature) {
                if (in_array((int) $feature['id'], $selectedFeatureIds, true)) {
                    $featuresUsed[] = [
                        'activity' => (string) $feature['activity'],
                        'tier' => (string) $feature['tier'],
                    ];
                }
            }

            $receipt = centralbankSendReceipt(
                (string) $hotelOwnerUser,
                (string) $centralBankApiKey,
                $guestName,
                $arrivalDate,
                $departureDate,
                $featuresUsed,
                (int) $hotelRating
            );

            if (!$receipt['ok']) {
                error_log('Receipt failed (non-blocking): ' . (string) ($receipt['error'] ?? 'Unknown error'));
            }

            $deposit = centralbankDeposit((string) $hotelOwnerUser, $transferCode);

            if (!$deposit['ok']) {
                throw new RuntimeException('Deposit failed: ' . (string) ($deposit['error'] ?? 'Unknown error'));
            }

            $database->commit();

            $selectedFeatureNames = [];
            foreach ($features as $feature) {
                if (in_array((int) $feature['id'], $selectedFeatureIds, true)) {
                    $selectedFeatureNames[] = (string) $feature['name'];
                }
            }

            $featuresForUrl = implode(', ', $selectedFeatureNames);
            $roomNameForUrl = (string) $selectedRoom['name'];

            header(
                'Location: booking.php?success=1'
                    . '&room=' . urlencode($roomNameForUrl)
                    . '&arrival=' . urlencode($arrivalDate)
                    . '&departure=' . urlencode($departureDate)
                    . '&total=' . urlencode((string) $totalCost)
                    . '&features=' . urlencode($featuresForUrl)
                    . '#booking'
            );
            exit;
        } catch (Throwable $e) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            error_log('Booking error: ' . $e->getMessage());
            $errors[] = 'Booking could not be completed. Please try again.';
        }
    }
}

require_once __DIR__ . '/src/header.php';
?>

<main class="booking">
    <section class="booking-rooms">
        <h2 class="booking-title">OUR ROOMS</h2>

        <div class="booking-rooms-grid">
            <?php foreach ($rooms as $room) : ?>
                <?php $slug = (string) $room['slug']; ?>

                <article class="booking-room-card">
                    <div class="booking-room-image">
                        <img
                            src="pictures/room-<?= escapeHtml($slug) ?>.png"
                            alt="<?= escapeHtml((string) $room['name']) ?>">
                    </div>

                    <h3 class="booking-room-name">
                        <?= escapeHtml((string) $room['name']) ?> <?= (int) $room['price_per_night'] ?>€
                    </h3>

                    <p class="booking-room-text">
                        <?= escapeHtml((string) ($roomContent[$slug]['text'] ?? '')) ?>
                    </p>

                    <ul class="booking-room-features">
                        <?php foreach (($roomContent[$slug]['bullets'] ?? []) as $bullet) : ?>
                            <li><?= escapeHtml((string) $bullet) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="availability">
        <h2 class="availability-heading">CHECK OUT AVAILABILITY</h2>

        <div class="availability-grid-outer">
            <?php foreach ($rooms as $room) : ?>
                <?php
                $roomId = (int) $room['id'];
                $title = strtoupper((string) $room['slug']) . ' - JAN 2026';

                renderJanuaryCalendar(
                    $roomId,
                    $bookedDaysByRoomId,
                    $title
                );
                ?>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<section class="booking-hero">
    <div class="booking-inner">
        <section class="booking-card" id="booking">
            <header class="booking-header">
                <h1 id="booking-title" class="booking-title">Book a room</h1>
            </header>

            <p class="field-hint">
                - Choose room, dates & features and click <strong>Calculate total</strong><br>
                - Create a transfer code in the Central Bank for that amount<br>
                - Enter your name + transfer code and click <strong>Confirm booking</strong><br>
            </p>

            <?php if ($successMessage !== null) : ?>
                <div class="modal is-open" id="successModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                    <div class="modal-backdrop" data-close-modal></div>

                    <div class="modal-card">
                        <h3 id="modalTitle" class="modal-title">Booking confirmed!</h3>

                        <p class="modal-text">
                            <?= nl2br(escapeHtml($successMessage)) ?>
                        </p>

                        <div class="modal-actions">
                            <button type="button" class="btn btn-primary" data-close-modal>
                                Nice!
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($errors !== []) : ?>
                <ul role="alert" class="notice notice-error">
                    <?php foreach ($errors as $error) : ?>
                        <li><?= escapeHtml((string) $error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="booking.php#booking" class="booking-form">
                <div class="booking-grid">
                    <label class="field">
                        <span class="field-label">Guest name</span>
                        <input
                            class="field-control"
                            type="text"
                            name="guest_name"
                            value="<?= isset($_POST['guest_name']) ? escapeHtml((string) $_POST['guest_name']) : '' ?>">
                    </label>

                    <label class="field">
                        <span class="field-label">Transfer code</span>
                        <input
                            class="field-control"
                            type="text"
                            name="transfer_code"
                            placeholder="xxxx-xxxx-xxxx"
                            value="<?= isset($_POST['transfer_code']) ? escapeHtml((string) $_POST['transfer_code']) : '' ?>">
                    </label>

                    <label class="field field-full">
                        <span class="field-label">Room</span>
                        <select class="field-control" name="room_slug" required>
                            <option value="">Choose a room</option>
                            <?php foreach ($rooms as $room) : ?>
                                <option
                                    value="<?= escapeHtml((string) $room['slug']) ?>"
                                    <?= ((string) $room['slug'] === $roomSlug) ? 'selected' : '' ?>>
                                    <?= escapeHtml((string) $room['name']) ?> (<?= (int) $room['price_per_night'] ?> €/night)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="field">
                        <span class="field-label">Arrival date</span>
                        <input
                            class="field-control"
                            type="date"
                            name="arrival_date"
                            required
                            min="2026-01-01"
                            max="2026-01-31"
                            value="<?= escapeHtml($arrivalDate) ?>">
                    </label>

                    <label class="field">
                        <span class="field-label">Departure date</span>
                        <input
                            class="field-control"
                            type="date"
                            name="departure_date"
                            required
                            min="2026-01-01"
                            max="2026-01-31"
                            value="<?= escapeHtml($departureDate) ?>">
                    </label>
                </div>

                <div class="features1">
                    <h3 class="features1-title">
                        Choose island features
                    </h3>

                    <div class="features1-grid">
                        <?php foreach ($features as $feature) : ?>
                            <?php $id = (int) $feature['id']; ?>
                            <label class="features1-item">
                                <input
                                    class="features1-checkbox"
                                    type="checkbox"
                                    name="features[]"
                                    value="<?= $id ?>"
                                    <?= in_array($id, $selectedFeatureIds, true) ? 'checked' : '' ?>>
                                <span class="features1-text">
                                    <?= escapeHtml((string) $feature['name']) ?>
                                    <span class="features1-meta">
                                        <span class="tier-badge tier-badge--<?= escapeHtml((string) $feature['tier']) ?>">
                                            <?= escapeHtml((string) $feature['tier']) ?>
                                        </span>
                                        <span class="features1-price">(<?= (int) $feature['cost'] ?> €)</span>
                                    </span>
                                </span>

                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <section class="offer-box">
                    <div class="offer-box-inner">
                        <h3 class="offer-title">20% off with Collector’s Offer</h3>

                        <p class="offer-text">
                            Collect a full tier set: one feature from each category in the same tier — and receive 20% off your total stay.
                        </p>

                        <p class="offer-note">
                            The discount is applied automatically when the conditions are met.
                        </p>
                    </div>
                </section>

                <div class="actions">
                    <button class="btn-primary" id="bookingButton" type="submit">
                        Calculate total
                    </button>

                    <a
                        class="btn-secondary"
                        href="https://www.yrgopelag.se/centralbank/"
                        target="_blank">
                        Get transfer code
                    </a>
                </div>
            </form>

            <?php if ($totalCost !== null && $nights !== null) : ?>
                <section class="summary" aria-label="Booking summary">
                    <h3 class="summary-title">Summary</h3>

                    <dl class="summary-list">
                        <div class="summary-row">
                            <dt>Nights</dt>
                            <dd><?= (int) $nights ?></dd>
                        </div>

                        <?php if ($discountPercent > 0) : ?>
                            <div class="summary-row">
                                <dt>Discount</dt>
                                <dd>
                                    <?= escapeHtml((string) $discountLabel) ?>
                                    −<?= (int) $discountAmount ?> €
                                </dd>
                            </div>
                        <?php endif; ?>

                        <div class="summary-row">
                            <dt>Total cost</dt>
                            <dd><?= (int) $totalCost ?> €</dd>
                        </div>
                    </dl>
                </section>
            <?php endif; ?>

        </section>
    </div>
</section>

<?php require __DIR__ . '/src/footer.php'; ?>