<?php

declare(strict_types=1);

namespace App\Bundle;

use App\Bundle\Doctrine\Type\BingMarketType;
use App\Bundle\Doctrine\Type\JsonTextType;
use App\Bundle\Doctrine\Type\ObjectIdType;
use Mango\DependencyInjection\DoctrineConnectionPass;
use Mango\DependencyInjection\DoctrineTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AppBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DoctrineTypePass([
            BingMarketType::NAME => BingMarketType::class,
            JsonTextType::NAME => JsonTextType::class,
            ObjectIdType::NAME => ObjectIdType::class,
        ]));

        $container->addCompilerPass(
            new DoctrineConnectionPass(['import' => 'doctrine.dbal.import_connection']),
            priority: 1,
        );
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
