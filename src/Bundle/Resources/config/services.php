<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\ApiPlatform\ImageOperationProcessor;
use App\Bundle\ApiPlatform\ImageOperationProvider;
use App\Bundle\ApiPlatform\ImageProvider;
use App\Bundle\ApiPlatform\RecordOperationProcessor;
use App\Bundle\ApiPlatform\RecordOperationProvider;
use App\Bundle\ApiPlatform\RecordProvider;
use App\Bundle\Doctrine\TableProvider\ImageOperationsTable;
use App\Bundle\Doctrine\TableProvider\ImagesTable;
use App\Bundle\Doctrine\TableProvider\RecordOperationsTable;
use App\Bundle\Doctrine\TableProvider\RecordsTable;
use App\Bundle\Downloader\ImageDownloader;
use App\Bundle\Downloader\Storage\BunnyCDNStorage;
use App\Bundle\Downloader\Storage\FilesystemStorage;
use App\Bundle\Downloader\Storage\ReplicatedStorage;
use App\Bundle\Downloader\Storage\S3Storage;
use App\Bundle\Downloader\VideoDownloader;
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
use AsyncAws\S3\S3Client;
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
    $services->set(ImageOperationsTable::class);

    // Repository
    $services->set(DoctrineRepository::class);
    $services->set(LeanCloudRepository::class);

    // API Platform
    $services->set(RecordProvider::class);
    $services->set(ImageProvider::class);
    $services->set(RecordOperationProvider::class);
    $services->set(ImageOperationProvider::class);
    $services->set(RecordOperationProcessor::class)
        ->tag('mango.api_platform.dto_initializer', ['input_class' => RetryCollectRecord::class]);
    $services->set(ImageOperationProcessor::class)
        ->tag('mango.api_platform.dto_initializer', ['input_class' => RetryDownloadImage::class]);

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
    $services->set(RetryDownloadImageHandler::class);
    $services->set(DownloadImageHandler::class);
    $services->set(ImageSpecCollection::class);

    $services->set('app.image_storage.local', FilesystemStorage::class)->args([env('FS_PREFIX')->resolve() . 'a/']);
    $services->set('app.video_storage.local', FilesystemStorage::class)->args([env('FS_PREFIX')->resolve() . 'videocontent/']);

    $services->set('app.s3_client', S3Client::class)->arg('$configuration', [
        'endpoint' => env('S3_ENDPOINT'),
        'accessKeyId' => env('AWS_ACCESS_KEY_ID'),
        'accessKeySecret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'pathStyleEndpoint' => true,
        'sendChunkedBody' => false,
    ]);

    $services->set('app.image_storage.s3', S3Storage::class)->args([
        service('app.s3_client'),
        env('S3_BUCKET'),
        'a/',
        [
            'CacheControl' => 'max-age=600',
            'ACL' => 'public-read',
        ],
    ]);

    $services->set('app.video_storage.s3', S3Storage::class)->args([
        service('app.s3_client'),
        env('S3_BUCKET'),
        'videocontent/',
        [
            'CacheControl' => 'max-age=600',
            'ACL' => 'public-read',
        ],
    ]);

    $services->set('app.image_storage.bunny', BunnyCDNStorage::class)->args([
        env('BUNNYCDN_ENDPOINT'),
        env('BUNNYCDN_ACCESS_KEY'),
        'a/',
        service(HttpClientInterface::class),
    ]);

    $services->set('app.video_storage.bunny', BunnyCDNStorage::class)->args([
        env('BUNNYCDN_ENDPOINT'),
        env('BUNNYCDN_ACCESS_KEY'),
        'videocontent/',
        service(HttpClientInterface::class),
    ]);

    $services->set('app.image_storage', ReplicatedStorage::class)
        ->args([
            service('app.image_storage.local'),
            service('app.image_storage.s3'),
            service('app.image_storage.bunny'),
        ]);

    $services->set('app.video_storage', ReplicatedStorage::class)
        ->args([
            service('app.video_storage.local'),
            service('app.video_storage.s3'),
            service('app.video_storage.bunny'),
        ]);

    $services->set(ImageDownloader::class)
        ->args([service(HttpClientInterface::class), service('app.image_storage'), '/a/']);

    $services->set(VideoDownloader::class)
        ->args([service(HttpClientInterface::class), service('app.video_storage')]);
};
