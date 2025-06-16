<?php

namespace App\Services\Api\V1;

use App\Http\Requests\Api\V1\WaitlistRequest;
use App\Models\Waitlist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class WaitlistService
{
    public function store(WaitlistRequest $request): Waitlist
    {
        return DB::transaction(function () use ($request) {
            return Waitlist::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'phone_number' => $request->phoneNumber,
                'personality_type' => $request->personalityType,
            ]);
        });
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Waitlist::all();
    }

    public function findById(int $id): Waitlist
    {
        $waitlist = Waitlist::find($id);
        if (!$waitlist) {
            throw new ModelNotFoundException('Waitlist entry not found');
        }
        return $waitlist;
    }
}
