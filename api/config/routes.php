<?php

declare(strict_types=1);

use App\Application\Router\StaticRouteGroup as Group;
use App\UI\Http\Action;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
    $app->get('/', Action\HomeAction::class);

    $app->group('/V1/cart', new Group(static function (RouteCollectorProxy $group): void {
        $group->patch('/change-quantity', Action\V1\Cart\ChangeQuantityBatchAction::class);
    }));
};
