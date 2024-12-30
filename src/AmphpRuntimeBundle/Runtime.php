<?php

declare(strict_types=1);

namespace App\AmphpRuntimeBundle;

use ReflectionFunction;
use Symfony\Component\Runtime\ResolverInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class Runtime extends SymfonyRuntime
{
    private string $socket;

    public function __construct(array $options = [])
    {
        $this->socket = $options['socket']
            ?? $_SERVER['APP_RUNTIME_SOCKET']
            ?? $_ENV['APP_RUNTIME_SOCKET']
            ?? 'http://0.0.0.0:' . ($_SERVER['PORT'] ?? $_ENV['PORT'] ?? 3000);

        parent::__construct($options);
    }

    public function getRunner(?object $application): RunnerInterface
    {
        return new Runner($application, [$this->socket]);
    }

    public function getResolver(callable $callable, ?ReflectionFunction $reflector = null): ResolverInterface
    {
        $resolver = parent::getResolver($callable, $reflector);

        return new Resolver($resolver);
    }
}
