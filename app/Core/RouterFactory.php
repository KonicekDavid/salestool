<?php

declare(strict_types=1);

namespace App\Core;

use Contributte\ApiRouter\ApiRoute;
use Nette;
use Nette\Application\Routers\RouteList;


/**
 * RouterFactory
 */
final class RouterFactory
{
    use Nette\StaticClass;

    /**
     * @return RouteList
     */
    public static function createRouter(): RouteList
    {
        $router = new RouteList;

        $apiModule = new RouteList('Api');
        $apiModule[] = new ApiRoute('/api/calculations[/<id>]', 'Calculation', [
            'methods' => [
                'GET'  => 'default',
                'POST' => 'default',
                'PUT'  => 'default'
            ]
        ]);

        $router[] = $apiModule;

        return $router;
    }
}
