<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Command\ImportFromSqlCommand;
use App\Doctrine\Contract\TableProvider;
use App\Doctrine\SchemaProvider;
use App\LeanCloud;
use App\Repository\DoctrineRepository;
use Doctrine\DBAL\Connection;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->instanceof(TableProvider::class)
        ->tag('app.doctrine.table_provider');

    $services
        ->load('App\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/Bundle/',
            __DIR__ . '/../src/DependencyInjection/',
            __DIR__ . '/../src/Entity/',
            __DIR__ . '/../src/Kernel.php',
        ]);

    $services->set(LeanCloud::class)
        ->arg('$endpoint', env('LEANCLOUD_API_SERVER') . '/1.1/')
        ->arg('$appId', env('LEANCLOUD_APP_ID'))
        ->arg('$appKey', env('LEANCLOUD_APP_KEY'))
        ->arg('$sessionToken', env('LEANCLOUD_SESSION_TOKEN'));

    $services->set(SchemaProvider::class . '_source')
        ->class(SchemaProvider::class)
        ->arg(Connection::class, service('doctrine.dbal.source_connection'));

    $services->set(DoctrineRepository::class . '_source')
        ->class(DoctrineRepository::class)
        ->arg(SchemaProvider::class, service(SchemaProvider::class . '_source'));

    $services->set(ImportFromSqlCommand::class)
        ->arg('$source', service(DoctrineRepository::class . '_source'))
        ->arg('$destination', service(DoctrineRepository::class))
        ->public();
};
