<?php

declare(strict_types=1);

require __DIR__ . '/src/header.php';

require __DIR__ . '/src/database.php';

$statement = $database->query('SELECT * FROM rooms');
$rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
print_r($rooms);
echo '</pre>';



require __DIR__ . '/src/footer.php';
