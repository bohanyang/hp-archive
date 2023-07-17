<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\DoctrineConfig;
use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $containerConfigurator, DoctrineConfig $doctrine, FrameworkConfig $framework): void {
    $dbal = $doctrine->dbal();

    $connection = $dbal->connection('default');
    $connection->url(env('DATABASE_URL')->resolve());
    $connection->schemaFilter('`^(?!messenger_)`');
    $connection->schemaManagerFactory('doctrine.dbal.default_schema_manager_factory');
    // IMPORTANT: You MUST configure your server version,
    // either here or in the DATABASE_URL env var (see .env file)
    // $connection->serverVersion('13');

    $connection = $dbal->connection('source');
    $connection->url(env('SOURCE_DATABASE_URL')->resolve());

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
