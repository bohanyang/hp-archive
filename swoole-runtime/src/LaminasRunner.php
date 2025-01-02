<?php

declare(strict_types=1);

namespace Rainbowedge\SwooleRuntime;

use Laminas\HttpHandlerRunner\RequestHandlerRunnerInterface;
use Symfony\Component\Runtime\RunnerInterface;

class LaminasRunner implements RunnerInterface
{
    public function __construct(private RequestHandlerRunnerInterface $runner)
    {
    }

    public function run(): int
    {
        $this->runner->run();

        return 0;
    }
}
