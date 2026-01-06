<?php

declare(strict_types=1);

$database = new PDO('sqlite:' . __DIR__ . '/../storage/hotel.sqlite');

$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$database->exec('PRAGMA foreign_keys = ON');
