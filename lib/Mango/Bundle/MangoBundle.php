<?php

declare(strict_types=1);

namespace Manyou\Mango\Bundle;

use Doctrine\Migrations\Provider\SchemaProvider as SchemaProviderInterface;
use Manyou\Mango\DependencyInjection\DoctrineMigrationsDependencyPass;
use Manyou\Mango\DependencyInjection\DoctrineTypePass;
use Manyou\Mango\DependencyInjection\MessengerMiddlewarePass;
use Manyou\Mango\Doctrine\Contract\TableProvider;
use Manyou\Mango\Doctrine\SchemaProvider;
use Manyou\Mango\Doctrine\Type\OperationStatusType;
use Manyou\Mango\Operation\Messenger\Middleware\OperationMiddware;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MangoBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(TableProvider::class)
            ->addTag('mango.doctrine.table_provider');

        $container->addCompilerPass(
            new MessengerMiddlewarePass(['id' => OperationMiddware::class]),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1,
        );

        $container->addCompilerPass(
            new DoctrineTypePass([OperationStatusType::NAME => OperationStatusType::class]),
        );

        $container->addCompilerPass(
            new DoctrineMigrationsDependencyPass([SchemaProviderInterface::class => SchemaProvider::class]),
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
