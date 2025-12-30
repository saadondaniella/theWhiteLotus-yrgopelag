<?php

declare(strict_types=1);

if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

$maxStars = 5;

$settingsPath = __DIR__ . '/storage/settings.json';
$hotelRating = 2;

if (file_exists($settingsPath)) {
    $settingsRaw = file_get_contents($settingsPath);
    $settingsData = json_decode((string) $settingsRaw, true);

    if (is_array($settingsData) && isset($settingsData['hotelRating'])) {
        $hotelRating = (int) $settingsData['hotelRating'];
    }
}

$hotelOwnerUser = 'Daniella';
$centralBankApiKey = getenv('API_KEY');
