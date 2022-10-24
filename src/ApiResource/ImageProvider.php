<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\DoctrineRepository;

class ImageProvider implements ProviderInterface
{
    public function __construct(private DoctrineRepository $repository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (! isset($uriVariables['name'])) {
            return null;
        }

        return $this->repository->getImage($uriVariables['name']);
    }
}
