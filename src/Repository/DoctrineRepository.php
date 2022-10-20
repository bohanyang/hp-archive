<?php

declare(strict_types=1);

namespace App\Repository;

use App\ApiResource\Order;
use App\Doctrine\SchemaProvider;
use Iterator;

class DoctrineRepository
{
    public function __construct(
        private SchemaProvider $schemaProvider,
    ) {
    }

    public function createOrder(Order $order): void
    {
        $this->schemaProvider->createQuery()->insert('orders', [
            'id' => $order->id,
            'name' => $order->name,
            'status' => $order->status,
        ])->executeStatement();
    }

    public function getOrderById(int $id): ?Order
    {
        $q = $this->schemaProvider->createQuery();

        $q->selectFrom('orders', 'id', 'name', 'status')
            ->where($q->eq('orders.id', $id))
            ->setMaxResults(1);

        if (false === $data = $q->executeQuery()->fetchAssociative()) {
            return null;
        }

        return new Order($data['id'], $data['name'], $data['status']);
    }

    public function listOrders(): Iterator
    {
        $q = $this->schemaProvider->createQuery();

        $result = $q->selectFrom('orders', 'id', 'name', 'status')
            ->where($q->neq('orders.status', 'pending'))
            ->executeQuery();

        while ($data = $result->fetchAssociative()) {
            yield new Order($data['id'], $data['name'], $data['status']);
        }
    }
}
