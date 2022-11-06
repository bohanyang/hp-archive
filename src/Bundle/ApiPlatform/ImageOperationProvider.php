<?php

declare(strict_types=1);

namespace App\Bundle\ApiPlatform;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Bundle\Repository\DoctrineRepository;

use function iterator_to_array;

class ImageOperationProvider implements ProviderInterface
{
    public function __construct(private DoctrineRepository $repository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return iterator_to_array($this->repository->listImageOperations());
        }

        if (! isset($uriVariables['id'])) {
            return null;
        }

        return $this->repository->getImageOperation($uriVariables['id']);
    }
}
