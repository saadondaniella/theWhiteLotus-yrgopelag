<?php

declare(strict_types=1);

require __DIR__ . '/../src/database.php';

$database->exec(
    'CREATE TABLE IF NOT EXISTS rooms (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug VARCHAR(50) NOT NULL UNIQUE,
        name VARCHAR(50) NOT NULL,
        price_per_night INTEGER NOT NULL
    )'
);

$database->exec(
    'CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        room_id INTEGER NOT NULL,
        guest_name VARCHAR(100) NOT NULL,
        arrival_date VARCHAR(10) NOT NULL,
        departure_date VARCHAR(10) NOT NULL,
        transfer_code VARCHAR(60),
        total_cost INTEGER NOT NULL DEFAULT 0,
        created_at VARCHAR(25) NOT NULL,
        FOREIGN KEY (room_id) REFERENCES rooms(id)
    )'
);

$database->exec(
    'CREATE TABLE IF NOT EXISTS features (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        activity VARCHAR(30) NOT NULL,
        tier VARCHAR(20) NOT NULL,
        name VARCHAR(100) NOT NULL,
        cost INTEGER NOT NULL,
        is_active INTEGER NOT NULL DEFAULT 1,
        UNIQUE(activity, tier)
    )'
);

$database->exec(
    'CREATE TABLE IF NOT EXISTS booking_features (
        booking_id INTEGER NOT NULL,
        feature_id INTEGER NOT NULL,
        PRIMARY KEY (booking_id, feature_id),
        FOREIGN KEY (booking_id) REFERENCES bookings(id),
        FOREIGN KEY (feature_id) REFERENCES features(id)
    )'
);

echo 'Tables created!';
