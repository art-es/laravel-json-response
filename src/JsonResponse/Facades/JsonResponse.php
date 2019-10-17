<?php

namespace Artes\JsonResponse\Facades;

use Artes\JsonResponse\JsonResponseService;
use Illuminate\Support\Facades\Facade;

class JsonResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return JsonResponseService::class;
    }
}
