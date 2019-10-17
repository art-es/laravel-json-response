<?php

namespace Artes\JsonResponse;

use Artes\JsonResponse\Facades\JsonResponse;
use Illuminate\Support\ServiceProvider;

class JsonResponseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(JsonResponse::class, function () {
            return (new JsonResponseService())->success();
        });
    }
}
