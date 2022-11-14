<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([
        __DIR__ . '/lib/src/BingHomepage',
        __DIR__ . '/lib/src/LeanStorage',
        __DIR__ . '/lib/src/Mango',
        __DIR__ . '/lib/src/PromiseHttpClient',
        __DIR__ . '/lib/src/RemoveDataCollectorBundle',
        __DIR__ . '/lib/src/WorkermanSymfonyRuntime',
    ]);
    $mbConfig->defaultBranch('main');
};
