<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\ApiPlatform\ImageProvider;
use App\Bundle\ApiPlatform\RecordOperationProcessor;
use App\Bundle\ApiPlatform\RecordOperationProvider;
use App\Bundle\ApiPlatform\RecordProvider;
use App\Bundle\Doctrine\TableProvider\ImagesTable;
use App\Bundle\Doctrine\TableProvider\RecordOperationsTable;
use App\Bundle\Doctrine\TableProvider\RecordsTable;
use App\Bundle\Message\CollectRecordsHandler;
use App\Bundle\Message\RetryCollectRecord;
use App\Bundle\Message\RetryCollectRecordHandler;
use App\Bundle\Message\SaveRecordHandler;
use App\Bundle\Repository\DoctrineRepository;
use App\Bundle\Repository\LeanCloudRepository;
use Manyou\BingHomepage\Client\CalendarUrlBasePrefixStrategy;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\Client\MediaContentClient;
use Manyou\BingHomepage\Client\UrlBasePrefixStrategy;
use Manyou\LeanStorage\LeanStorageClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Table Provider
    $services->set(ImagesTable::class);
    $services->set(RecordsTable::class);
    $services->set(RecordOperationsTable::class);

    // Repository
    $services->set(DoctrineRepository::class);
    $services->set(LeanCloudRepository::class);

    // API Platform
    $services->set(RecordProvider::class);
    $services->set(ImageProvider::class);
    $services->set(RecordOperationProvider::class);
    $services->set(RecordOperationProcessor::class)
        ->tag('mango.api_platform.dto_initializer', ['input_class' => RetryCollectRecord::class]);

    $services->set(CalendarUrlBasePrefixStrategy::class);
    $services->alias(UrlBasePrefixStrategy::class, CalendarUrlBasePrefixStrategy::class);

    $services->set(MediaContentClient::class)->public()
        ->arg('$httpClient', service(HttpClientInterface::class));

    $services->alias(ClientInterface::class, MediaContentClient::class);

    $services->set(LeanStorageClient::class)->public()
        ->args([
            service(HttpClientInterface::class),
            env('LEANCLOUD_API_SERVER') . '/1.1/',
            env('LEANCLOUD_APP_ID'),
            env('LEANCLOUD_APP_KEY'),
            env('LEANCLOUD_SESSION_TOKEN'),
        ]);

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

    $services->set(SaveRecordHandler::class);
    $services->set(CollectRecordsHandler::class);
    $services->set(RetryCollectRecordHandler::class);
};
