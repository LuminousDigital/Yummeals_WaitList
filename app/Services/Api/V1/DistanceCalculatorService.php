<?php

namespace App\Services\Api\V1;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DistanceCalculatorService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GOOGLE_MAPS_API_KEY');
    }

    public function calculateDistance(?string $origin, array $destinations): array
    {
        // Use default coordinates from .env if origin is not provided
        $origin = $origin ?: sprintf('%s,%s', env('DEFAULT_ORIGIN_LAT'), env('DEFAULT_ORIGIN_LNG'));

        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';
        $params = [
            'origins' => $origin,
            'destinations' => implode('|', $destinations), // Join with | for Google API
            'key' => $this->apiKey,
        ];

        $response = $this->client->get($url, ['query' => $params]);
        $data = json_decode($response->getBody(), true);

        if ($data['status'] !== 'OK') {
            Log::error('Google API response error', [
                'response' => $data,
                'origin' => $origin,
                'destinations' => $destinations,
            ]);
            throw new \Exception($data['error_message'] ?? 'Unable to calculate distance');
        }

        $results = [];
        foreach ($data['rows'][0]['elements'] as $index => $element) {
            if ($element['status'] !== 'OK') {
                Log::error('Google API element error', [
                    'element' => $element,
                    'destination' => $destinations[$index],
                ]);
                throw new \Exception('Unable to calculate distance for destination: ' . $destinations[$index]);
            }

            $distance = $element['distance']['value'] / 1000; // Convert meters to kilometers
            $results[] = [
                'destination' => $destinations[$index],
                'distance' => round($distance, 2),
                'unit' => 'km',
            ];
        }

        return $results;
    }
}
