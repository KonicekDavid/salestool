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
        $router = new RouteList('Api');
        $route = new ApiRoute('/api/v1/calculations[/<id>]', 'Calculation', [
            'methods' => [
                'GET'  => 'read',
                'POST' => 'create',
                'PUT'  => 'update'
            ]
        ]);
        $route->setAutoBasePath(false);
        $router[] = $route;
        return $router;
    }
}
