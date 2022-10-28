<?php

declare(strict_types=1);

namespace App\Bundle\RemoveDataCollectorBundle;

use App\Bundle\RemoveDataCollectorBundle\DependencyInjection\RemoveDataCollectorCompilerPass;
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
