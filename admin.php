<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/src/database.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/config.php';

$errors = [];
$success = null;

$adminPassword = (string) (getenv('ADMIN_PASSWORD') ?: '');
$isLoggedIn = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

$settingsPath = __DIR__ . '/storage/settings.json';

function saveHotelRating(string $settingsPath, int $hotelRating): bool
{
    $data = ['hotelRating' => $hotelRating];
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        return false;
    }

    return file_put_contents($settingsPath, $json) !== false;
}

if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (!$isLoggedIn && isset($_POST['action']) && $_POST['action'] === 'login') {
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($adminPassword === '') {
        $errors[] = 'ADMIN_PASSWORD is missing in .env';
    } elseif (!hash_equals($adminPassword, $password)) {
        $errors[] = 'Wrong password.';
    } else {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    }
}
if ($isLoggedIn && isset($_POST['action']) && $_POST['action'] === 'reset_bookings') {
    try {
        $database->beginTransaction();

        // Ta bort kopplingar först (FK-säkert)
        $database->exec('DELETE FROM booking_features');
        $database->exec('DELETE FROM bookings');

        // (Valfritt) nollställ autoincrement för bookings i SQLite
        $database->exec("DELETE FROM sqlite_sequence WHERE name = 'bookings'");

        $database->commit();
        $success = 'All bookings have been cleared.';
    } catch (Throwable $e) {
        if ($database->inTransaction()) {
            $database->rollBack();
        }
        error_log('Reset bookings error: ' . $e->getMessage());
        $errors[] = 'Could not clear bookings. Please try again.';
    }
}

if ($isLoggedIn && isset($_POST['action']) && $_POST['action'] === 'save') {
    $newRating = isset($_POST['hotel_rating']) ? (int) $_POST['hotel_rating'] : 3;
    if ($newRating < 1 || $newRating > 5) {
        $errors[] = 'Hotel rating must be between 1 and 5.';
    }

    $activeFeatureIds = [];
    if (isset($_POST['features']) && is_array($_POST['features'])) {
        $activeFeatureIds = array_map('intval', $_POST['features']);
    }

    $roomPrices = [];
    if (isset($_POST['room_prices']) && is_array($_POST['room_prices'])) {
        foreach ($_POST['room_prices'] as $roomId => $price) {
            $roomIdInt = (int) $roomId;
            $priceInt = (int) $price;

            if ($roomIdInt <= 0) {
                $errors[] = 'Invalid room id.';
                break;
            }

            if ($priceInt < 0) {
                $errors[] = 'Room price must be 0 or higher.';
                break;
            }

            $roomPrices[$roomIdInt] = $priceInt;
        }
    }

    if ($errors === []) {
        if (!saveHotelRating($settingsPath, $newRating)) {
            $errors[] = 'Could not save settings.json (check storage folder permissions).';
        }
    }

    if ($errors === []) {
        $database->beginTransaction();

        try {
            $database->exec('UPDATE features SET is_active = 0');

            if ($activeFeatureIds !== []) {
                $updateFeature = $database->prepare('UPDATE features SET is_active = 1 WHERE id = :id');

                foreach ($activeFeatureIds as $id) {
                    $updateFeature->execute([':id' => $id]);
                }
            }

            if ($roomPrices !== []) {
                $updateRoom = $database->prepare(
                    'UPDATE rooms
                     SET price_per_night = :price_per_night
                     WHERE id = :id'
                );

                foreach ($roomPrices as $roomIdInt => $priceInt) {
                    $updateRoom->execute([
                        ':price_per_night' => $priceInt,
                        ':id' => $roomIdInt,
                    ]);
                }
            }

            $database->commit();
            $success = 'All set, your changes has been updated.';
        } catch (Throwable $e) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            error_log('Admin save error: ' . $e->getMessage());
            $errors[] = 'Could not save changes. Please try again.';
        }
    }
}

$features = $database->query(
    'SELECT id, activity, tier, name, cost, is_active
     FROM features
     ORDER BY activity ASC, cost ASC'
)->fetchAll();

$rooms = $database->query(
    'SELECT id, slug, name, price_per_night
     FROM rooms
     ORDER BY price_per_night ASC'
)->fetchAll();

$bookings = $database->query(
    'SELECT guest_name, room_id, arrival_date, departure_date, total_cost, created_at
     FROM bookings
     ORDER BY created_at DESC'
)->fetchAll(PDO::FETCH_ASSOC);


$currentRating = (int) $hotelRating;

require __DIR__ . '/src/header.php';
?>

<section class="booking-hero">
    <div class="booking-inner">
        <section class="booking-card" style="max-width: 60rem;">
            <header class="booking-header">
                <h2 class="booking-title">Admin</h2>
                <p class="booking-subtitle" style="text-transform:none;">
                    Only for the hotel manager.
                </p>
            </header>

            <?php if ($errors !== []) : ?>
                <ul role="alert" class="notice notice-error">
                    <?php foreach ($errors as $error) : ?>
                        <li><?= escapeHtml((string) $error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($success !== null) : ?>
                <p role="status" class="notice notice-success"><?= escapeHtml($success) ?></p>
            <?php endif; ?>

            <?php if (!$isLoggedIn) : ?>
                <form method="post" class="booking-form">
                    <input type="hidden" name="action" value="login">

                    <label class="field">
                        <span class="field-label">Admin password</span>
                        <input class="field-control" type="password" name="password" required>
                    </label>

                    <div class="actions">
                        <button class="btn-primary" type="submit">Login</button>
                    </div>
                </form>
            <?php else : ?>
                <form method="post" class="booking-form">
                    <input type="hidden" name="action" value="save">

                    <label class="field field-full">
                        <span class="field-label">Hotel rating (stars)</span>
                        <select class="field-control" name="hotel_rating" required>
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <option value="<?= $i ?>" <?= $i === $currentRating ? 'selected' : '' ?>>
                                    <?= $i ?> star<?= $i === 1 ? '' : 's' ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </label>

                    <div class="features1" style="margin-top: 1.5rem;">
                        <h3 class="features1-title" style="text-transform:none;">
                            Room prices (€ / night)
                        </h3>

                        <div class="features1-grid">
                            <?php foreach ($rooms as $room) : ?>
                                <?php $roomId = (int) $room['id']; ?>
                                <label class="features1-item" style="text-transform:none; display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                                    <span class="features1-text" style="text-transform:none;">
                                        <?= escapeHtml((string) $room['name']) ?>
                                    </span>

                                    <input
                                        class="field-control"
                                        type="number"
                                        name="room_prices[<?= $roomId ?>]"
                                        min="0"
                                        step="1"
                                        style="max-width: 160px;"
                                        value="<?= (int) $room['price_per_night'] ?>">
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="features1" style="margin-top: 1.5rem;">
                        <h3 class="features1-title" style="text-transform:none;">
                            Active features (shown on booking page)
                        </h3>

                        <div class="features1-grid">
                            <?php foreach ($features as $feature) : ?>
                                <?php $id = (int) $feature['id']; ?>
                                <label class="features1-item" style="text-transform:none;">
                                    <input
                                        class="features1-checkbox"
                                        type="checkbox"
                                        name="features[]"
                                        value="<?= $id ?>"
                                        <?= ((int) $feature['is_active'] === 1) ? 'checked' : '' ?>>

                                    <span class="features1-text" style="text-transform:none;">
                                        <?= escapeHtml((string) $feature['activity']) ?> /
                                        <?= escapeHtml((string) $feature['tier']) ?> —
                                        <?= escapeHtml((string) $feature['name']) ?>
                                        <span class="features1-price">(<?= (int) $feature['cost'] ?> €)</span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn-primary" type="submit">Save changes</button>
                    </div>
                </form>

                <form method="post" style="margin-top: 1rem;">
                    <input type="hidden" name="action" value="logout">
                    <button class="btn-primary" type="submit" style="opacity:0.85;">Logout</button>
                </form>
            <?php endif; ?>

            <?php if ($isLoggedIn) : ?>
                <section class="booking-card" style="max-width: 60rem; margin-top: 3rem;">
                    <header class="booking-header">
                        <h2 class="booking-title">Bookings</h2>
                    </header>

                    <form method="post" style="margin-top: 1rem;">
                        <input type="hidden" name="action" value="reset_bookings">
                        <button
                            class="btn-primary"
                            type="submit"
                            style="opacity:0.85;"
                            onclick="return confirm('Clear ALL bookings? This cannot be undone.');">
                            Reset bookings
                        </button>
                    </form>
                    <?php if ($bookings === []) : ?>
                        <p>No bookings yet.</p>
                    <?php else : ?>
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:8px;">Guest</th>
                                    <th style="text-align:left; padding:8px;">Room ID</th>
                                    <th style="text-align:left; padding:8px;">Arrival</th>
                                    <th style="text-align:left; padding:8px;">Departure</th>
                                    <th style="text-align:left; padding:8px;">Total (€)</th>
                                    <th style="text-align:left; padding:8px;">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking) : ?>
                                    <tr>
                                        <td style="padding:8px;"><?= escapeHtml((string) $booking['guest_name']) ?></td>
                                        <td style="padding:8px;"><?= (int) $booking['room_id'] ?></td>
                                        <td style="padding:8px;"><?= escapeHtml((string) $booking['arrival_date']) ?></td>
                                        <td style="padding:8px;"><?= escapeHtml((string) $booking['departure_date']) ?></td>
                                        <td style="padding:8px;"><?= (int) $booking['total_cost'] ?></td>
                                        <td style="padding:8px;"><?= escapeHtml((string) $booking['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </section>
</section>
</div>

<?php require __DIR__ . '/src/footer.php'; ?>