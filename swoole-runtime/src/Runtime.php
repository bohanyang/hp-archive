<?php

declare(strict_types=1);

namespace Rainbowedge\SwooleRuntime;

use Laminas\HttpHandlerRunner\RequestHandlerRunnerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

/**
 * A runtime for Swoole.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Runtime extends SymfonyRuntime
{
    public function __construct(protected array $options = [])
    {
        parent::__construct($options);
    }

    public function getRunner(?object $application): RunnerInterface
    {
        if (! $application instanceof KernelInterface) {
            return parent::getRunner($application);
        }

        $application->boot();
        $container = $application->getContainer();

        return new LaminasRunner($container->get(RequestHandlerRunnerInterface::class));
    }
}
