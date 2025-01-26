<?php

declare(strict_types=1);

namespace App\Bundle\Downloader\Storage;

use Psr\Container\ContainerInterface;

class StorageFactory
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    public function create(string $serviceId): Storage
    {
        return $this->container->get($serviceId);
    }
}
