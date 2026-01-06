<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

const CENTRALBANK_BASE_URL = 'https://yrgopelag.se/centralbank';

function centralbankPost(string $endpoint, array $payload): array
{
    $url = rtrim(CENTRALBANK_BASE_URL, '/') . '/' . ltrim($endpoint, '/');

    try {
        $client = new Client();

        $response = $client->post($url, [
            'json' => $payload,
            'timeout' => 12,
            'connect_timeout' => 6,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Connection' => 'close',
            ],
        ]);

        $statusCode = $response->getStatusCode();
        $raw = (string) $response->getBody();

        $data = json_decode($raw, true);

        if (!is_array($data)) {
            $logDir = __DIR__ . '/../storage';
            if (is_dir($logDir) && is_writable($logDir)) {
                file_put_contents(
                    $logDir . '/centralbank_raw_response.log',
                    date('c') . "\nStatus: $statusCode\nURL: $url\nPayload: " . json_encode($payload) . "\nRaw response: $raw\n\n",
                    FILE_APPEND
                );
            }

            return [
                'ok' => false,
                'status' => $statusCode,
                'error' => 'Central bank returned invalid response (status ' . $statusCode . ').',
                'data' => $raw,
            ];
        }

        $ok = ($statusCode >= 200 && $statusCode < 300);

        return [
            'ok' => $ok,
            'status' => $statusCode,
            'error' => $ok ? null : (string) ($data['error'] ?? 'Central bank error.'),
            'data' => $data,
        ];
    } catch (Throwable $e) {
        return [
            'ok' => false,
            'status' => 0,
            'error' => 'Could not reach central bank: ' . $e->getMessage(),
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
