<?php

declare(strict_types=1);

namespace App\DependencyInjection\RemoveDataCollectorBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RemoveDataCollectorBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveDataCollectorCompilerPass());
    }
}
