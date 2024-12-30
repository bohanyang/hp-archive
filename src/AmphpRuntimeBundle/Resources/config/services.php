<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Amp\Http\Server\FormParser\FormParser;
use Amp\Http\Server\RequestHandler;
use App\AmphpRuntimeBundle\PsrHttpBridge\MessageConverter;
use App\AmphpRuntimeBundle\PsrHttpBridge\PsrFactoryMessageConverter;
use App\AmphpRuntimeBundle\SymfonyRequestHandler;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $services = $containerConfigurator->services();
    $services
        ->defaults()
        ->autowire();

    $services->set(FormParser::class)
        ->arg('$fieldCountLimit', PsrFactoryMessageConverter::DEFAULT_FIELD_COUNT_LIMIT);

    // Symfony HttpFoundation to PSR messages
    $services->set(HttpMessageFactoryInterface::class, PsrHttpFactory::class);
    $services->set(HttpFoundationFactoryInterface::class, HttpFoundationFactory::class);

    // Amp HTTP messages to PSR messages
    $services->set(MessageConverter::class, PsrFactoryMessageConverter::class);

    $services->set(RequestHandler::class, SymfonyRequestHandler::class)
        ->arg(HttpKernelInterface::class, service('kernel'))
        ->public();
};
