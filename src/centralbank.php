<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

const CENTRALBANK_BASE_URL = 'https://yrgopelag.se/centralbank/';

function centralbankPost(string $endpoint, array $payload): array
{
    $url = rtrim(CENTRALBANK_BASE_URL, '/') . '/' . ltrim($endpoint, '/');

    $client = new Client();

    try {
        $response = $client->post($url, [
            'json' => $payload,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ],
            'timeout' => 10,
        ]);

        $statusCode = $response->getStatusCode();
        $raw = $response->getBody()->getContents();

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
    } catch (RequestException $e) {
        $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
        $errorMessage = $e->getMessage();

        return [
            'ok' => false,
            'status' => $statusCode,
            'error' => 'Could not reach central bank: ' . $errorMessage,
            'data' => null,
        ];
    }
}

function centralbankValidateTransferCode(string $transferCode, int $totalCost): array
{
    return centralbankPost('transferCode', [
        'transferCode' => $transferCode,
        'totalCost' => $totalCost,
    ]);
}

function centralbankDeposit(string $hotelOwnerUser, string $transferCode): array
{
    return centralbankPost('deposit', [
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
    return centralbankPost('receipt', [
        'user' => $user,
        'api_key' => $apiKey,
        'guest_name' => $guestName,
        'arrival_date' => $arrivalDate,
        'departure_date' => $departureDate,
        'features_used' => $featuresUsed,
        'star_rating' => $starRating,
    ]);
}
