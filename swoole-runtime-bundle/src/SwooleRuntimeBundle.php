<?php

declare(strict_types=1);

namespace Rainbowedge\SwooleRuntimeBundle;

use Mezzio\Swoole\Exception\ExtensionNotLoadedException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function extension_loaded;

class SwooleRuntimeBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        if (! extension_loaded('swoole') && ! extension_loaded('openswoole')) {
            throw new ExtensionNotLoadedException(
                'One of either the Swoole (https://github.com/swoole/swoole-src) or'
                . ' Open Swoole (https://www.swoole.co.uk) extensions must be loaded'
                . ' to use mezzio/mezzio-swoole',
            );
        }
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/Resources/config/services.php');
    }

    public function getPath(): string
    {
        return __DIR__;
    }
}
