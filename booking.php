<?php

declare(strict_types=1);

$roomContent = [
    'luxury' => [
        'text' => 'This is the ultimate overnight stay. Effortless luxury, calming details, and a seamless flow between room and ocean. You could save money — or you could be happy. Don’t be cheap. Book it. Your health is at stake. There may or may not be a freezer full of ice cream. Do not forget to add pool in the features.',
        'bullets' => [
            'Total Area 191 sqm',
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

require_once __DIR__ . '/src/database.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/header.php';
require_once __DIR__ . '/src/centralbank.php';

$errors = [];
$successMessage = null;

$roomsStatement = $database->query('SELECT id, slug, name, price_per_night FROM rooms ORDER BY price_per_night DESC');
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

    $arrival = new DateTime($booking['arrival_date']);
    $departure = new DateTime($booking['departure_date']);

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
?>

<main class="booking">

    <section class="booking-rooms">
        <h2 class="booking-title">OUR ROOMS</h2>

        <div class="booking-rooms-grid">

            <?php foreach ($rooms as $room): ?>
                <?php
                $slug = $room['slug'];
                ?>

                <article class="booking-room-card">
                    <div class="booking-room-image">
                        <img
                            src="pictures/room-<?= escapeHtml($slug) ?>.png"
                            alt="<?= escapeHtml($room['name']) ?>">
                    </div>

                    <h3 class="booking-room-name">
                        <?= escapeHtml($room['name']) ?> <?= (int)$room['price_per_night'] ?>€
                    </h3>

                    <p class="booking-room-text">
                        <?= escapeHtml($roomContent[$slug]['text']) ?>
                    </p>

                    <ul class="booking-room-features">
                        <?php foreach ($roomContent[$slug]['bullets'] as $bullet): ?>
                            <li><?= escapeHtml($bullet) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>

            <?php endforeach; ?>

        </div>
    </section>

    <div class="availability-grid-outer">
        <section class="availability">
            <h2 class="availability-heading">CHECK OUT AVAILABILITY</h2>

            <div class="availability-grid-outer">
                <?php foreach ($rooms as $room): ?>
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
    </div>
</main>
<?php

$featuresStatement = $database->query('SELECT id, name, cost, activity, tier FROM features WHERE is_active = 1 ORDER BY cost ASC');
$features = $featuresStatement->fetchAll(PDO::FETCH_ASSOC);

$arrivalDate = '2026-01-01';
$departureDate = '2026-01-02';
$roomSlug = '';
$selectedFeatureIds = [];

$totalCost = null;
$nights = null;

if (isset($_POST['guest_name'], $_POST['room_slug'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['transfer_code'])) {
    $guestName = trim((string) $_POST['guest_name']);
    $roomSlug = (string) $_POST['room_slug'];
    $arrivalDate = (string) $_POST['arrival_date'];
    $departureDate = (string) $_POST['departure_date'];
    $transferCode = trim((string) $_POST['transfer_code']);

    if (isset($_POST['features']) && is_array($_POST['features'])) {
        $selectedFeatureIds = array_map('intval', $_POST['features']);
    } else {
        $selectedFeatureIds = [];
    }

    if ($guestName === '') {
        $errors[] = 'Guest name is required.';
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
        if ($room['slug'] === $roomSlug) {
            $selectedRoom = $room;
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
            AND NOT (departure_date <= :arrival_date OR arrival_date>= :departure_date)
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
    }

    if ($errors === [] && $selectedRoom !== null && $totalCost !== null) {
        $validation = centralbankValidateTransferCode($transferCode, $totalCost);

        if (!$validation['ok']) {
            $errors[] = 'Invalid transfer code: ' . ($validation['error'] ?? 'Unknown error');
        }
    }

    if ($errors === [] && $selectedRoom !== null && $totalCost !== null) {
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

            $database->commit();

            // Send receipt
            $featuresUsed = [];
            foreach ($features as $feature) {
                if (in_array((int) $feature['id'], $selectedFeatureIds, true)) {
                    $featuresUsed[] = ['activity' => $feature['activity'], 'tier' => $feature['tier']];
                }
            }

            $receipt = centralbankSendReceipt(
                $hotelOwnerUser,
                $centralBankApiKey,
                $guestName,
                $arrivalDate,
                $departureDate,
                $featuresUsed,
                $hotelRating
            );

            if (!$receipt['ok']) {
                // Log error but don't fail booking
            }

            $successMessage = 'Booking confirmed and payment processed!';
        } catch (Throwable $e) {
            $database->rollBack();
            $errors[] = 'Could not save booking.';
        }
    }
}

?>

<main>
    <h2>Book a room</h>
        <p>Check-in 15:00 · Check-out 11:00 · January 2026</p>

        <?php if ($successMessage !== null) : ?>
            <p role="status"><?= escapeHtml($successMessage) ?></p>
        <?php endif; ?>

        <?php if ($errors !== []) : ?>
            <ul role="alert">
                <?php foreach ($errors as $error) : ?>
                    <li><?= escapeHtml($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="booking.php">
            <label>
                Guest name
                <input type="text" name="guest_name" required
                    value="<?= isset($_POST['guest_name']) ? escapeHtml((string) $_POST['guest_name']) : '' ?>">
            </label>
            <label>
                Transfer code
                <input
                    type="text"
                    name="transfer_code"
                    placeholder="xxxx-xxxx-xxxx"
                    required>
            </label>

            <label>
                Room
                <select name="room_slug" required>
                    <option value="">Choose a room</option>
                    <?php foreach ($rooms as $room) : ?>
                        <option value="<?= escapeHtml((string) $room['slug']) ?>"
                            <?= ((string) $room['slug'] === $roomSlug) ? 'selected' : '' ?>>
                            <?= escapeHtml((string) $room['name']) ?> (<?= (int) $room['price_per_night'] ?> €/night)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Arrival date
                <input type="date" name="arrival_date" required min="2026-01-01" max="2026-01-31"
                    value="<?= escapeHtml($arrivalDate) ?>">
            </label>

            <label>
                Departure date
                <input type="date" name="departure_date" required min="2026-01-01" max="2026-01-31"
                    value="<?= escapeHtml($departureDate) ?>">
            </label>

            <fieldset>
                <legend>Features (optional)</legend>

                <?php foreach ($features as $feature) : ?>
                    <?php $id = (int) $feature['id']; ?>
                    <label>
                        <input type="checkbox" name="features[]" value="<?= $id ?>"
                            <?= in_array($id, $selectedFeatureIds, true) ? 'checked' : '' ?>>
                        <?= escapeHtml((string) $feature['name']) ?> (<?= (int) $feature['cost'] ?> €)
                    </label>
                    <br>
                <?php endforeach; ?>
            </fieldset>

            <button type="submit">Save booking</button>
        </form>

        <?php if ($totalCost !== null && $nights !== null) : ?>
            <h2>Summary</h2>
            <p>Nights: <?= (int) $nights ?></p>
            <p>Total cost: <?= (int) $totalCost ?> €</p>
        <?php endif; ?>
</main>

<?php require __DIR__ . '/src/footer.php'; ?>