<?php

declare(strict_types=1);

require __DIR__ . '/../src/database.php';

$statement = $database->query('SELECT COUNT(*) FROM rooms');
$count = (int) $statement->fetchColumn();

if ($count > 0) {
    echo 'Rooms already exist.';
    exit;
}

$statement = $database->prepare('INSERT INTO rooms (slug, name, price_per_night) VALUES (:slug, :name, :price)');

$rooms = [
    ['budget', 'Budget', 2],
    ['standard', 'Standard', 5],
    ['luxury', 'Luxury', 8],
];

foreach ($rooms as $room) {
    $statement->execute([
        ':slug' => $room[0],
        ':name' => $room[1],
        ':price' => $room[2],
    ]);
}

echo 'Rooms inserted!';
