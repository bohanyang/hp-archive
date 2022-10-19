<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Doctrine\Type\OrderStatusType;
use Doctrine\DBAL\Connection;

use function assert;
use function random_int;

class OrderProcessor implements ProcessorInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        assert($data instanceof NewOrder);
        $q = $this->connection->createQueryBuilder();
        $q->insert('orders')->values([
            'id' => $q->createPositionalParameter($id = random_int(1000, 4294967295)),
            'name' => $q->createPositionalParameter($data->name),
            'status' => $q->createPositionalParameter(OrderStatusType::NEW, OrderStatusType::NAME),
        ])->executeStatement();

        return new Order($id, $data->name, OrderStatusType::NEW);
    }
}
