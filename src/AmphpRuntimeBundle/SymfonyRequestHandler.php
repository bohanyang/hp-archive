<?php

declare(strict_types=1);

namespace App\AmphpRuntimeBundle;

use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use App\AmphpRuntimeBundle\PsrHttpBridge\MessageConverter;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

use function Amp\async;

class SymfonyRequestHandler implements RequestHandler
{
    public function __construct(
        private HttpKernelInterface $kernel,
        private HttpFoundationFactoryInterface $httpFoundationFactory,
        private HttpMessageFactoryInterface $httpMessageFactory,
        private MessageConverter $ampMessageConverter,
    ) {
    }

    public function handleRequest(Request $request): Response
    {
        $psrRequest  = $this->ampMessageConverter->convertRequest($request);
        $sfRequest   = $this->httpFoundationFactory->createRequest($psrRequest);
        $sfResponse  = $this->kernel->handle($sfRequest);
        $psrResponse = $this->httpMessageFactory->createResponse($sfResponse);
        $response    = $this->ampMessageConverter->convertResponse($psrResponse);

        if ($this->kernel instanceof TerminableInterface) {
            $kernel = $this->kernel;
            async(static function () use ($kernel, $sfRequest, $sfResponse) {
                $kernel->terminate($sfRequest, $sfResponse);
            });
        }

        return $response;
    }
}
