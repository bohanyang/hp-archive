<?php

declare(strict_types=1);

namespace Rainbowedge\SwooleRuntimeBundle;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class SymfonyRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private KernelInterface $kernel,
        private HttpFoundationFactoryInterface $httpFoundationFactory,
        private HttpMessageFactoryInterface $httpMessageFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $sfRequest  = $this->httpFoundationFactory->createRequest($request);
        $sfResponse = $this->kernel->handle($sfRequest);
        $response   = $this->httpMessageFactory->createResponse($sfResponse);

        if ($this->kernel instanceof TerminableInterface) {
            $this->kernel->terminate($sfRequest, $sfResponse);
        }

        return $response;
    }
}
