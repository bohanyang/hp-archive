<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class NoContentController
{
    public function __invoke(): void
    {
    }
}
