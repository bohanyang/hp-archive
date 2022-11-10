<?php

declare(strict_types=1);

namespace App\Bundle\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Bundle\Doctrine\Type\BingMarketType;
use App\Bundle\Repository\DoctrineRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Type;

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
            || false === $date = DateTimeImmutable::createFromFormat('!Ymd', $uriVariables['date'])
        ) {
            return null;
        }

        /** @var BingMarketType */
        $type = Type::getType(BingMarketType::NAME);

        if (null === $type->valueToId($uriVariables['market'])) {
            return null;
        }

        return $this->repository->getRecord($uriVariables['market'], $date);
    }
}
