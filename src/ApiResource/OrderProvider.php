<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\DoctrineRepository;

use function is_int;
use function iterator_to_array;

class OrderProvider implements ProviderInterface
{
    public function __construct(private DoctrineRepository $repository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return iterator_to_array($this->repository->listOrders());
        }

        if (! isset($uriVariables['id']) || ! is_int($uriVariables['id']) || 0 >= $uriVariables['id']) {
            return null;
        }

        return $this->repository->getOrderById($uriVariables['id']);
    }
}
