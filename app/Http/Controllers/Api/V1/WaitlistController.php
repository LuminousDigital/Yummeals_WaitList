<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WaitlistRequest;
use App\Http\Resources\Api\V1\WaitlistResource;
use App\Http\Responses\ApiResponse;
use App\Services\Api\V1\WaitlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WaitlistController extends Controller
{
    protected $waitlistService;

    public function __construct(WaitlistService $waitlistService)
    {
        $this->waitlistService = $waitlistService;
    }

    public function store(WaitlistRequest $request): JsonResponse
    {
        try {
            $waitlist = $this->waitlistService->store($request);
            return ApiResponse::success(
                new WaitlistResource($waitlist),
                'Successfully joined waitlist',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to join waitlist',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function index(): JsonResponse
    {
        try {
            $waitlists = $this->waitlistService->getAll();
            return ApiResponse::success(
                WaitlistResource::collection($waitlists),
                'Waitlist retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to retrieve waitlist',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $waitlist = $this->waitlistService->findById($id);
            return ApiResponse::success(
                new WaitlistResource($waitlist),
                'Waitlist entry retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                'Waitlist entry not found',
                404
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to retrieve waitlist entry',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }
}
