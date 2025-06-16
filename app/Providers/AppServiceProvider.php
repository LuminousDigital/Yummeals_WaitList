<?php

namespace App\Providers;

use App\Services\Api\V1\WaitlistService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WaitlistService::class, function ($app) {
            return new WaitlistService();
        });
    }

    public function boot(): void
    {
        //
    }
}
