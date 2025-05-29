<?php

declare(strict_types=1);

namespace App\Core;

use Contributte\ApiRouter\ApiRoute;
use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList;

        $apiModule = new RouteList('Api');
        $apiModule[] = new ApiRoute('/api/offers[/<id>]', 'Offer', [
            'methods' => [
                'GET'  => 'default',
                'POST' => 'default',
                'PUT'  => 'default'
            ],
        ]);

        $router[] = $apiModule;

        return $router;
    }
}
