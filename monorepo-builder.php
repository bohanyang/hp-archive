<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([
        __DIR__ . '/packages/manyou/packages/bing-homepage',
        __DIR__ . '/packages/manyou/packages/leanstorage',
        __DIR__ . '/packages/manyou/packages/mango',
        __DIR__ . '/packages/manyou/packages/promise-http-client',
        __DIR__ . '/packages/manyou/packages/clear-service-tags-bundle',
        __DIR__ . '/packages/manyou/packages/workerman-symfony-runtime',
    ]);
    $mbConfig->defaultBranch('main');
};
