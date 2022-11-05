<?php

declare(strict_types=1);

namespace App\Bundle\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Bundle\Message\RetryCollectRecord;
use Manyou\Mango\ApiPlatform\DtoInitializerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class RecordOperationProcessor implements ProcessorInterface, DtoInitializerInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function initialize(string $inputClass, array $attributes): ?object
    {
        return new RetryCollectRecord($attributes['previous_data']);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof RetryCollectRecord) {
            $this->messageBus->dispatch($data);
        }

        return $data;
    }
}
