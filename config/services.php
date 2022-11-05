<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\Message\ImportFromLeanCloudHandler;
use App\Bundle\Message\ImportFromSqlHandler;
use App\Bundle\Repository\DoctrineRepository;
use Doctrine\DBAL\Connection;
use Manyou\Mango\Doctrine\SchemaProvider;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('App\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/Bundle/',
            __DIR__ . '/../src/DependencyInjection/',
            __DIR__ . '/../src/Entity/',
            __DIR__ . '/../src/Kernel.php',
        ]);

    // Import source
    $services->set('app.doctrine.schema_provider.source')
        ->class(SchemaProvider::class)
        ->arg(Connection::class, service('doctrine.dbal.source_connection'));

    $services->set('app.repository.doctrine.source')
        ->class(DoctrineRepository::class)
        ->arg(SchemaProvider::class, service('app.doctrine.schema_provider.source'));

    // Import destination
    $services->set('app.doctrine.schema_provider.import')
        ->class(SchemaProvider::class)
        ->arg(Connection::class, service('doctrine.dbal.import_connection'));

    $services->set('app.repository.doctrine.import')
        ->class(DoctrineRepository::class)
        ->arg(SchemaProvider::class, service('app.doctrine.schema_provider.import'));

    $services->set(ImportFromSqlHandler::class)
        ->arg('$source', service('app.repository.doctrine.source'))
        ->arg('$destination', service('app.repository.doctrine.import'))
        ->public();

    $services->set(ImportFromLeanCloudHandler::class)
        ->arg(DoctrineRepository::class, service('app.repository.doctrine.import'));
};
