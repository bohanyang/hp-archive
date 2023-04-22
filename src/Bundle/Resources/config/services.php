<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\ApiPlatform\ImageProvider;
use App\Bundle\ApiPlatform\ImageTaskProcessor;
use App\Bundle\ApiPlatform\ImageTaskProvider;
use App\Bundle\ApiPlatform\RecordProvider;
use App\Bundle\ApiPlatform\RecordTaskProcessor;
use App\Bundle\ApiPlatform\RecordTaskProvider;
use App\Bundle\Doctrine\Table\ImagesTable;
use App\Bundle\Doctrine\Table\ImageTasksTable;
use App\Bundle\Doctrine\Table\RecordsTable;
use App\Bundle\Doctrine\Table\RecordTasksTable;
use App\Bundle\ImageSpec\ImageSpecCollection;
use App\Bundle\Message\CollectRecordsHandler;
use App\Bundle\Message\DownloadImageHandler;
use App\Bundle\Message\RetryCollectRecord;
use App\Bundle\Message\RetryCollectRecordHandler;
use App\Bundle\Message\RetryDownloadImage;
use App\Bundle\Message\RetryDownloadImageHandler;
use App\Bundle\Message\SaveRecordHandler;
use App\Bundle\Repository\DoctrineRepository;
use App\Bundle\Repository\LeanCloudRepository;
use App\Bundle\Security\Doctrine\TableProvider\UsersTable;
use App\Bundle\Security\LogoutListener;
use App\Bundle\Security\UserProvider;
use Manyou\BingHomepage\Client\CalendarUrlBasePrefixStrategy;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\Client\ImageArchiveClient;
use Manyou\BingHomepage\Client\MediaContentClient;
use Manyou\BingHomepage\Client\UrlBasePrefixStrategy;
use Manyou\LeanStorage\LeanStorageClient;
use Manyou\PromiseHttpClient\PromiseHttpClient;
use Manyou\PromiseHttpClient\PromiseHttpClientInterface;
use Manyou\PromiseHttpClient\RetryableHttpClient;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Table Provider
    $services->set(ImagesTable::class);
    $services->set(RecordsTable::class);
    $services->set(RecordTasksTable::class);
    $services->set(ImageTasksTable::class);

    // Repository
    $services->set(DoctrineRepository::class);
    $services->set(LeanCloudRepository::class);

    // API Platform
    $services->set(RecordProvider::class);
    $services->set(ImageProvider::class);
    $services->set(RecordTaskProvider::class);
    $services->set(ImageTaskProvider::class);
    $services->set(RecordTaskProcessor::class)
        ->tag('mango.api_platform.dto_initializer', ['input_class' => RetryCollectRecord::class]);
    $services->set(ImageTaskProcessor::class)
        ->tag('mango.api_platform.dto_initializer', ['input_class' => RetryDownloadImage::class]);

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

    $services->set(SaveRecordHandler::class);
    $services->set(CollectRecordsHandler::class);
    $services->set(RetryCollectRecordHandler::class);
    $services->set(RetryDownloadImageHandler::class);
    $services->set(DownloadImageHandler::class);
    $services->set(ImageSpecCollection::class);

    $services->set(UsersTable::class);
    $services->set(UserProvider::class);
    $services->set(LogoutListener::class);
};
