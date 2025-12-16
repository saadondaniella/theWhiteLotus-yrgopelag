<?php

declare(strict_types=1);

require __DIR__ . '/../src/database.php';

$statement = $database->query('SELECT COUNT(*) FROM features');
$count = (int) $statement->fetchColumn();

if ($count > 0) {
    echo 'Features already exist.';
    exit;
}

$features = [
    ['water', 'economy', 'pool', 2, 1],
    ['water', 'basic', 'scuba diving', 5, 1],
    ['water', 'premium', 'olympic pool', 10, 0],
    ['water', 'superior', 'waterpark with fire and minibar', 17, 0],
    ['games', 'economy', 'yahtzee', 2, 1],
    ['games', 'basic', 'ping pong table', 5, 0],
    ['games', 'premium', 'PS5', 10, 0],
    ['games', 'superior', 'casino', 17, 0],
    ['wheels', 'economy', 'unicycle', 2, 1],
    ['wheels', 'basic', 'bicycle', 5, 0],
    ['wheels', 'premium', 'trike', 10, 0],
    ['wheels', 'superior', 'four-wheeled motorized beast', 17, 0],
    ['rituals', 'economy', 'sunrise tea ceremony', 2, 0],
    ['rituals', 'basic', 'guidedsilence meditation hour', 5, 0],
    ['rituals', 'premium', 'library and fireplace', 10, 1],
    ['rituals', 'superior', 'midnight lantern procession', 17, 0],
];

$insert = $database->prepare(
    'INSERT INTO features (activity, tier, name, cost, is_active)
     VALUES (:activity, :tier, :name, :cost, :is_active)'
);

foreach ($features as $feature) {
    $insert->execute([
        ':activity' => $feature[0],
        ':tier' => $feature[1],
        ':name' => $feature[2],
        ':cost' => $feature[3],
        ':is_active' => $feature[4],
    ]);
}

echo 'Features inserted!';
