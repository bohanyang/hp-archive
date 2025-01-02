<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\Downloader\ImageDownloader;
use App\Bundle\Downloader\Storage\BunnyCDNStorage;
use App\Bundle\Downloader\Storage\ReplicatedStorage;
use App\Bundle\Downloader\VideoDownloader;
use App\Bundle\Message\ImportFromLeanCloudHandler;
use App\Bundle\Message\ImportFromSqlHandler;
use App\Bundle\Repository\DoctrineRepository;
use App\Controller\MainController;
use Doctrine\DBAL\Connection;
use Mango\Doctrine\SchemaProvider;
use Manyou\BingHomepage\Market;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // $parameters->set('jose.access_token.mandatory_claims', [
    //     'exp',
    //     'iat',
    //     'aud',
    //     'iss',
    //     'urn:zitadel:iam:org:id',
    //     'role',
    // ]);

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

    $services->alias('mezzio_swoole.logger', 'monolog.logger.swoole');

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

    // $services->set('app.image_storage.local', FilesystemStorage::class)->args([env('FS_PREFIX')->resolve() . 'a/']);
    // $services->set('app.video_storage.local', FilesystemStorage::class)->args([env('FS_PREFIX')->resolve() . 'videocontent/']);

    // $services->set('app.s3_client', S3Client::class)->arg('$configuration', [
    //     'endpoint' => env('S3_ENDPOINT'),
    //     'accessKeyId' => env('AWS_ACCESS_KEY_ID'),
    //     'accessKeySecret' => env('AWS_SECRET_ACCESS_KEY'),
    //     'region' => env('AWS_DEFAULT_REGION'),
    //     'pathStyleEndpoint' => true,
    //     'sendChunkedBody' => false,
    // ]);

    // $services->set('app.image_storage.s3', S3Storage::class)->args([
    //     service('app.s3_client'),
    //     env('S3_BUCKET'),
    //     'a/',
    //     [
    //         'CacheControl' => 'max-age=600',
    //         'ACL' => 'public-read',
    //     ],
    // ]);

    // $services->set('app.video_storage.s3', S3Storage::class)->args([
    //     service('app.s3_client'),
    //     env('S3_BUCKET'),
    //     'videocontent/',
    //     [
    //         'CacheControl' => 'max-age=600',
    //         'ACL' => 'public-read',
    //     ],
    // ]);

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

    $services->set('app.image_storage', ReplicatedStorage::class)
        ->args([
            // service('app.image_storage.local'),
            // service('app.image_storage.s3'),
            service('app.image_storage.bunny'),
        ]);

    $services->set('app.video_storage', ReplicatedStorage::class)
        ->args([
            // service('app.video_storage.local'),
            // service('app.video_storage.s3'),
            service('app.video_storage.bunny'),
        ]);

    $services->set(ImageDownloader::class)
        ->arg('$storage', service('app.image_storage'))
        ->arg('$prefixToRemove', '/a/');

    $services->set(VideoDownloader::class)
        ->arg('$storage', service('app.video_storage'));

    $services->set(MainController::class)
        ->arg('$origin', env('APP_ORIGIN'));

    // $services->set('jose.checker.claim.aud', AudienceChecker::class)
    //     ->args([env('ZITADEL_PROJECT_ID')])
    //     ->tag('jose.checker.claim', ['alias' => 'aud']);

    // $services->set('jose.checker.claim.iss', IssuerChecker::class)
    //     ->args([[env('OIDC_AUTHORITY')]])
    //     ->tag('jose.checker.claim', ['alias' => 'iss']);

    // $services->set('jose.checker.claim.zitadel_org_id', CustomClaimChecker::class)
    //     ->args(['urn:zitadel:iam:org:id', env('ZITADEL_ORGANIZATION_ID')])
    //     ->tag('jose.checker.claim', ['alias' => 'zitadel_org_id']);

    // $services->set('jose.checker.header.alg', AlgHeaderChecker::class)
    //     ->args(['RS256'])
    //     ->tag('jose.checker.header', ['alias' => 'alg']);
};
