<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;

use function is_int;

class OrderProvider implements ProviderInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (! isset($uriVariables['id']) || ! is_int($uriVariables['id'])) {
            return null;
        }

        $q = $this->connection->createQueryBuilder();
        $q->select('id', 'name', 'status')->from('orders')->where(
            $q->expr()->eq('id', $q->createPositionalParameter($uriVariables['id'])),
        )->setMaxResults(1);

        return $q->executeQuery()->fetchAssociative() ?: null;
    }
}
