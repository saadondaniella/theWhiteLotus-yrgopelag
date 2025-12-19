<?php

declare(strict_types=1);

const CENTRALBANK_BASE_URL = 'https://yrgopelag.se/';

function centralbankPost(string $endpoint, array $payload): array
{
    $url = rtrim(CENTRALBANK_BASE_URL, '/') . '/' . ltrim($endpoint, '/');

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
            'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'ignore_errors' => true,
            'timeout' => 10,
        ],
    ]);

    $raw = file_get_contents($url, false, $context);

    $statusCode = 0;
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
        $statusCode = (int) $m[1];
    }

    if ($raw === false) {
        return [
            'ok' => false,
            'status' => $statusCode,
            'error' => 'Could not reach central bank.',
            'data' => null,
        ];
    }

    $data = json_decode($raw, true);

    if (!is_array($data)) {
        return [
            'ok' => false,
            'status' => $statusCode,
            'error' => 'Central bank returned invalid JSON (status ' . $statusCode . '). Raw response: ' . substr($raw, 0, 500) . '...',
            'data' => $raw,
        ];
    }

    $ok = ($statusCode >= 200 && $statusCode < 300);

    return [
        'ok' => $ok,
        'status' => $statusCode,
        'error' => $ok ? null : ($data['error'] ?? 'Central bank error.'),
        'data' => $data,
    ];
}

function centralbankValidateTransferCode(string $transferCode, int $totalCost): array
{
    return centralbankPost('/centralbank/transferCode', [
        'transferCode' => $transferCode,
        'totalCost' => $totalCost,
    ]);
}

function centralbankDeposit(string $hotelOwnerUser, string $transferCode): array
{
    return centralbankPost('/centralbank/deposit', [
        'user' => $hotelOwnerUser,
        'transferCode' => $transferCode,
    ]);
}

function centralbankSendReceipt(
    string $user,
    string $apiKey,
    string $guestName,
    string $arrivalDate,
    string $departureDate,
    array $featuresUsed,
    int $starRating
): array {
    return centralbankPost('/centralbank/receipt', [
        'user' => $user,
        'api_key' => $apiKey,
        'guest_name' => $guestName,
        'arrival_date' => $arrivalDate,
        'departure_date' => $departureDate,
        'features_used' => $featuresUsed,
        'star_rating' => $starRating,
    ]);
}
