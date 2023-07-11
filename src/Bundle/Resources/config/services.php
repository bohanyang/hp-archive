<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Manyou\BingHomepage\Client\CalendarUrlBasePrefixStrategy;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\Client\ImageArchiveClient;
use Manyou\BingHomepage\Client\MediaContentClient;
use Manyou\BingHomepage\Client\UrlBasePrefixStrategy;
use Manyou\LeanStorage\LeanStorageClient;
use Manyou\PromiseHttpClient\PromiseHttpClient;
use Manyou\PromiseHttpClient\PromiseHttpClientInterface;
use Manyou\PromiseHttpClient\RetryableHttpClient;

use function dirname;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Bundle\\', dirname(__DIR__, 2) . '/')
        ->exclude(dirname(__DIR__, 2) . '/{Resources,AppBundle.php}');

    $services->set(CalendarUrlBasePrefixStrategy::class);
    $services->alias(UrlBasePrefixStrategy::class, CalendarUrlBasePrefixStrategy::class);

    $services->set(PromiseHttpClientInterface::class, PromiseHttpClient::class);
    $services->set(RetryableHttpClient::class)
        ->decorate(PromiseHttpClientInterface::class)->args([service('.inner')]);

    $services->set(MediaContentClient::class);
    $services->set(ImageArchiveClient::class);
    $services->alias(ClientInterface::class, ImageArchiveClient::class);

    $services->set(LeanStorageClient::class)
        ->arg('$endpoint', env('LEANCLOUD_API_SERVER') . '/1.1/')
        ->arg('$appId', env('LEANCLOUD_APP_ID'))
        ->arg('$appKey', env('LEANCLOUD_APP_KEY'))
        ->arg('$sessionToken', env('LEANCLOUD_SESSION_TOKEN'));

    $services->set('doctrine.dbal.import_connection.configuration')
        ->parent('doctrine.dbal.connection.configuration');

    $services->set('doctrine.dbal.import_connection.event_manager')
        ->parent('doctrine.dbal.connection.event_manager');

    $services->set('doctrine.dbal.import_connection')->public()
        ->parent('doctrine.dbal.connection')
        ->args([
            ['url' => env('DATABASE_URL')->resolve()],
            service('doctrine.dbal.import_connection.configuration'),
            service('doctrine.dbal.import_connection.event_manager'),
        ]);
};
