<?php

declare(strict_types=1);

namespace App;

use App\Doctrine\Type\BingMarketType;
use App\Doctrine\Type\JsonType;
use App\Doctrine\Type\ObjectIdType;
use App\Message\DoctrinePingConnectionMiddleware;
use Doctrine\DBAL\Types\Types;
use Mango\DependencyInjection\DoctrineConnectionPass;
use Mango\DependencyInjection\DoctrineTypePass;
use Mango\DependencyInjection\MessengerMiddlewarePass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function process(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DoctrineTypePass([
            BingMarketType::NAME => BingMarketType::class,
            Types::JSON => JsonType::class,
            ObjectIdType::NAME => ObjectIdType::class,
        ]));

        $container->addCompilerPass(
            new DoctrineConnectionPass(['import' => 'doctrine.dbal.import_connection']),
            priority: 1,
        );
        $container->addCompilerPass(
            new MessengerMiddlewarePass(['id' => DoctrinePingConnectionMiddleware::class]),
            priority: 1,
        );
    }
}
