<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Doctrine\Type\OrderStatusType;
use App\Repository\DoctrineRepository;

use function assert;
use function random_int;

class OrderProcessor implements ProcessorInterface
{
    public function __construct(private DoctrineRepository $repository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        assert($data instanceof NewOrder);
        $order = new Order(
            random_int(1000, 4294967295),
            $data->name,
            OrderStatusType::NEW,
        );
        $this->repository->createOrder($order);

        return $order;
    }
}
