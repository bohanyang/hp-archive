<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Controller\MainController;
use App\Downloader\ImageDownloader;
use App\Downloader\Storage\BunnyCDNStorage;
use App\Downloader\Storage\FilesystemStorage;
use App\Downloader\Storage\S3Storage;
use App\Downloader\Storage\Storage;
use App\Downloader\Storage\StorageFactory;
use App\Downloader\VideoDownloader;
use App\Message\ImportFromLeanCloudHandler;
use App\Message\ImportFromSqlHandler;
use App\Repository\DoctrineRepository;
use AsyncAws\S3\S3Client;
use Doctrine\DBAL\Connection;
use Mango\Doctrine\SchemaProvider;
use Manyou\BingHomepage\Client\CalendarUrlBasePrefixStrategy;
use Manyou\BingHomepage\Client\ClientInterface;
use Manyou\BingHomepage\Client\ImageArchiveClient;
use Manyou\BingHomepage\Client\MediaContentClient;
use Manyou\BingHomepage\Client\UrlBasePrefixStrategy;
use Manyou\BingHomepage\Market;
use Manyou\LeanStorage\LeanStorageClient;
use Manyou\PromiseHttpClient\PromiseHttpClient;
use Manyou\PromiseHttpClient\PromiseHttpClientInterface;
use Manyou\PromiseHttpClient\RetryableHttpClient;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('app.enabled_markets', [
        Market::US,
        Market::BR,
        Market::CA,
        Market::QC,
        Market::UK,
        Market::FR,
        Market::DE,
        Market::IN,
        Market::CN,
        Market::JP,
        Market::ES,
        Market::IT,
        Market::AU,
    ]);

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('App\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/Bundle/',
            __DIR__ . '/../src/AmphpRuntimeBundle/',
            __DIR__ . '/../src/DependencyInjection/',
            __DIR__ . '/../src/Entity/',
            __DIR__ . '/../src/Kernel.php',
        ]);

    $services->alias('mango.scheduler.transport', 'messenger.transport.async');
    $parameters->set('mango.scheduler.transport', 'async');

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

    $services->set(BunnyCDNStorage::class)
        ->abstract()
        ->arg('$baseUri', env('BUNNYCDN_ENDPOINT'))
        ->arg('$accessKey', env('BUNNYCDN_ACCESS_KEY'));

    $services->set('app.image_storage.bunny')
        ->parent(BunnyCDNStorage::class)
        ->arg('$prefix', 'a/');

    $services->set('app.video_storage.bunny')
        ->parent(BunnyCDNStorage::class)
        ->arg('$prefix', 'videocontent/');

    $services->set('app.storage_factory', StorageFactory::class)
        ->args([
            service_locator([
                'app.image_storage.local' => service('app.image_storage.local'),
                'app.image_storage.s3' => service('app.image_storage.s3'),
                'app.image_storage.bunny' => service('app.image_storage.bunny'),
                'app.video_storage.local' => service('app.video_storage.local'),
                'app.video_storage.s3' => service('app.video_storage.s3'),
                'app.video_storage.bunny' => service('app.video_storage.bunny'),
            ]),
        ]);

    $services->set('app.image_storage', Storage::class)
        ->factory([service('app.storage_factory'), 'create'])
        ->args(['app.image_storage.' . env('STORAGE_SERVICE')]);

    $services->set('app.video_storage', Storage::class)
        ->factory([service('app.storage_factory'), 'create'])
        ->args(['app.video_storage.' . env('STORAGE_SERVICE')]);

    $services->set(ImageDownloader::class)
        ->arg('$storage', service('app.image_storage'))
        ->arg('$prefixToRemove', '/a/');

    $services->set(VideoDownloader::class)
        ->arg('$storage', service('app.video_storage'));

    $services->set(MainController::class)
        ->arg('$origin', env('APP_ORIGIN'));

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
        ]);
};
