<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Doctrine\OracleSessionInitSubscriber;
use App\Doctrine\Type\BingMarketType;
use App\Doctrine\Type\JsonTextType;
use App\Doctrine\Type\ObjectIdType;
use Symfony\Config\DoctrineConfig;
use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $containerConfigurator, DoctrineConfig $doctrine, FrameworkConfig $framework): void {
    $services = $containerConfigurator->services();
    $services
        ->set('oracle.listener', OracleSessionInitSubscriber::class)
        ->tag('doctrine.event_listener', ['event' => 'postConnect']);

    $dbal = $doctrine->dbal();

    $connection = $dbal->connection('default');
    $connection->url(env('DATABASE_URL')->resolve());
    // IMPORTANT: You MUST configure your server version,
    // either here or in the DATABASE_URL env var (see .env file)
    // $connection->serverVersion('13');

    $dbal->type(BingMarketType::NAME, BingMarketType::class);
    $dbal->type(JsonTextType::NAME, JsonTextType::class);
    $dbal->type(ObjectIdType::NAME, ObjectIdType::class);

    if ('test' === $containerConfigurator->env()) {
        $connection
            // "TEST_TOKEN" is typically set by ParaTest
            ->dbnameSuffix('_test' . env('TEST_TOKEN')->default(''));
    }

    if ('prod' === $containerConfigurator->env()) {
        ($cache = $framework->cache())
            ->pool('doctrine.result_cache_pool')
            ->adapters(['cache.app']);

        $cache
            ->pool('doctrine.system_cache_pool')
            ->adapters(['cache.system']);
    }
};
