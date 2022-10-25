<?php

declare(strict_types=1);

namespace App\DependencyInjection\RemoveDataCollectorBundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveDataCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $collectorsToRemove = ['doctrine_migrations.migrations_collector'];

        foreach ($collectorsToRemove as $dataCollector) {
            if (! $container->hasDefinition($dataCollector)) {
                continue;
            }

            $definition = $container->getDefinition($dataCollector);

            $definition->clearTags();
        }
    }
}
