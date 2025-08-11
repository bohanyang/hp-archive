<?php

declare(strict_types=1);

namespace App;

use FluffyDiscord\RoadRunnerBundle\Kernel\RoadRunnerMicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use RoadRunnerMicroKernelTrait;
}
