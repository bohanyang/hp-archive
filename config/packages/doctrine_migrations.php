<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Doctrine\SchemaProvider;
use Doctrine\Migrations\Provider\SchemaProvider as SchemaProviderInterface;
use Symfony\Config\DoctrineMigrationsConfig;

return static function (DoctrineMigrationsConfig $migrations): void {
    $migrations
        // namespace is arbitrary but should be different from App\Migrations
        // as migrations classes should NOT be autoloaded
        ->migrationsPath('DoctrineMigrations', param('kernel.project_dir') . '/' . env('DATABASE_URL')->url()->key('scheme')->string() . '_migrations')
        ->enableProfiler(param('kernel.debug'))
        ->transactional(false)
        ->services(SchemaProviderInterface::class, SchemaProvider::class);
};
