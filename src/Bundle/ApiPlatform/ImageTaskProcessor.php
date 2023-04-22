<?php

declare(strict_types=1);

namespace App\Bundle\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Bundle\Message\RetryDownloadImage;
use Manyou\Mango\ApiPlatform\DtoInitializerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ImageTaskProcessor implements ProcessorInterface, DtoInitializerInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function initialize(string $inputClass, array $attributes): ?object
    {
        return new RetryDownloadImage($attributes['previous_data']);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof RetryDownloadImage) {
            $this->messageBus->dispatch($data);
        }

        return $data;
    }
}
