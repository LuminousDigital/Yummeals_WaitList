<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CalculateDistanceRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Api\V1\DistanceCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DistanceController extends Controller
{
    protected $distanceService;

    public function __construct(DistanceCalculatorService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    public function calculate(CalculateDistanceRequest $request): JsonResponse
    {
        try {
            $origin = $request->input('origin'); 
            $results = $this->distanceService->calculateDistance(
                $origin,
                $request->input('destinations')
            );

            $usedOrigin = $origin ?: sprintf('%s,%s', env('DEFAULT_ORIGIN_LAT'), env('DEFAULT_ORIGIN_LNG'));

            $data = [
                'origin' => $usedOrigin,
                'distances' => array_map(function ($result) {
                    return [
                        'destination' => $result['destination'],
                        'distance' => $result['distance'],
                        'unit' => $result['unit'],
                        'isLocationCovered' => $result['distance'] <= 10,
                    ];
                }, $results),
            ];

            return ApiResponse::success($data, 'Distances calculated successfully', 200);
        } catch (\Exception $e) {
            Log::error('Failed to calculate distance', [
                'error' => $e->getMessage(),
                'origin' => $request->input('origin'),
                'destinations' => $request->input('destinations'),
            ]);

            $message = 'Failed to calculate distance';
            $status = 500;

            if (str_contains($e->getMessage(), 'REQUEST_DENIED')) {
                $message = 'Invalid or restricted API key. Ensure billing is enabled.';
                $status = 403;
            } elseif (str_contains($e->getMessage(), 'INVALID_REQUEST')) {
                $message = 'Invalid origin or destination format';
                $status = 400;
            } elseif (str_contains($e->getMessage(), 'OVER_QUERY_LIMIT')) {
                $message = 'API quota exceeded';
                $status = 429;
            }

            return ApiResponse::error($message, $status, ['error' => $e->getMessage()]);
        }
    }
}
