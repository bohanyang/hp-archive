<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\DoctrineMigrationsConfig;

return static function (DoctrineMigrationsConfig $migrations): void {
    $migrations
        // namespace is arbitrary but should be different from App\Migrations
        // as migrations classes should NOT be autoloaded
        ->migrationsPath('DoctrineMigrations', param('kernel.project_dir') . '/migrations')
        ->enableProfiler(param('kernel.debug'))
        ->transactional(false);
};
