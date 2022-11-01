<?php

declare(strict_types=1);

namespace App\Bundle\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Bundle\Repository\DoctrineRepository;

class StateProcessor implements ProcessorInterface
{
    public function __construct(private DoctrineRepository $repository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof Image) {
            $this->repository->createImage($data);
        } elseif ($data instanceof Record) {
            $this->repository->createRecord($data);
        }

        return $data;
    }
}
