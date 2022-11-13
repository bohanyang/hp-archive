<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([
        __DIR__ . '/lib/BingHomepage',
        __DIR__ . '/lib/LeanStorage',
        __DIR__ . '/lib/Mango',
        __DIR__ . '/lib/PromiseHttpClient',
        __DIR__ . '/lib/RemoveDataCollectorBundle',
        __DIR__ . '/lib/WorkermanSymfonyRuntime',
    ]);
    $mbConfig->defaultBranch('main');
};
