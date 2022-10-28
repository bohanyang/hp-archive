<?php

declare(strict_types=1);

namespace App\Bundle\CoreBundle;

use App\Bundle\CoreBundle\Doctrine\Contract\TableProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class CoreBundle extends AbstractBundle
{
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/Resources/config/{packages}/*.php');
        $container->import(__DIR__ . '/Resources/config/services.php');
        
        $builder->registerForAutoconfiguration(TableProvider::class)->addTag('core.doctrine.table_provider');
    }
}
