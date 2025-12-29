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

if ($isLoggedIn && isset($_POST['action']) && $_POST['action'] === 'save') {
    $newRating = isset($_POST['hotel_rating']) ? (int) $_POST['hotel_rating'] : 3;
    if ($newRating < 1 || $newRating > 5) {
        $errors[] = 'Hotel rating must be between 1 and 5.';
    }

    $activeFeatureIds = [];
    if (isset($_POST['features']) && is_array($_POST['features'])) {
        $activeFeatureIds = array_map('intval', $_POST['features']);
    }

    if ($errors === []) {
        if (!saveHotelRating($settingsPath, $newRating)) {
            $errors[] = 'Could not save settings.json (check storage folder permissions).';
        }

        $database->beginTransaction();
        try {
            $database->exec('UPDATE features SET is_active = 0');

            if ($activeFeatureIds !== []) {
                $update = $database->prepare('UPDATE features SET is_active = 1 WHERE id = :id');
                foreach ($activeFeatureIds as $id) {
                    $update->execute([':id' => $id]);
                }
            }

            $database->commit();
            $success = 'Saved! Rating + features updated.';
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

$currentRating = $hotelRating;

require __DIR__ . '/src/header.php';
?>

<main class="booking-hero">
    <div class="booking-inner">
        <section class="booking-card" style="max-width: 60rem;">
            <header class="booking-header">
                <h2 class="booking-title">Admin</h2>
                <p class="booking-subtitle" style="text-transform:none;">This on only for the hotel manager.</p>
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
        </section>
    </div>
</main>

<?php require __DIR__ . '/src/footer.php'; ?>