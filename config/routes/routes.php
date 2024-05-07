<?php

declare(strict_types=1);

use App\Bundle\Message\CollectRecords;
use App\Controller\MainController;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Loader\Configurator\RouteConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $get = static function (string $name, string|array $path) use ($routes) {
        return $routes->add($name, $path)
            ->methods(['GET', 'HEAD']);
    };

    $redirectTo = static function (string $route, RouteConfigurator $configurator) {
        return $configurator->controller(RedirectController::class)
            ->defaults([
                'route' => $route,
                'permanent' => true,
                'keepQueryParams' => false,
            ]);
    };

    $date = ['date' => '\d{8}'];
    $get('date', '/{date}')->requirements($date)
    ->controller([MainController::class, 'date']);

    $redirectTo('date', $get('legacy_date_1', '/d/{date}/')->requirements($date));
    $redirectTo('date', $get('legacy_date_2', '/Date/{date}/')->requirements($date));

    $record = $date + ['market' => implode('|', CollectRecords::DEFAULT_MARKETS)];
    $get('record', '/{market?zh-CN}/{date?}')->requirements($record)
    ->controller([MainController::class, 'record']);

    $redirectTo('record', $get('legacy_record_1', '/archives/{market}/{date}/')->requirements($record));
    $redirectTo('record', $get('legacy_record_2', '/Archive/{market}/{date}/')->requirements($record));

    $image = ['name' => '\w+'];
    $get('image', '/images/{name}')->requirements($image)
        ->controller([MainController::class, 'image']);

    $redirectTo('image', $get('legacy_image_1', '/Image/{name}/')->requirements($image));

    $get('browse', '/browse/{cursor?}')->requirements(['cursor' => '\d{8}'])
        ->controller([MainController::class, 'browse']);

    $get('healthcheck', '/_ping')
        ->controller([MainController::class, 'health']);
};
