<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Doctrine\Type\BingMarketType;
use App\Repository\DoctrineRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Type;

use function assert;

class RecordProvider implements ProviderInterface
{
    public function __construct(private DoctrineRepository $repository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (
            ! isset($uriVariables['date'])
            || ! isset($uriVariables['market'])
            || false === $date = DateTimeImmutable::createFromFormat('Ymd', $uriVariables['date'])
        ) {
            return null;
        }

        $type = Type::getType(BingMarketType::NAME);
        assert($type instanceof BingMarketType);

        if (null === $type->valueToId($uriVariables['market'])) {
            return null;
        }

        return $this->repository->getRecord($uriVariables['market'], $date);
    }
}
