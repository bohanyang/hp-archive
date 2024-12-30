<?php

declare(strict_types=1);

namespace App\AmphpRuntimeBundle\PsrHttpBridge;

use Amp\Http\Server\Request as AmpRequest;
use Amp\Http\Server\Response as AmpResponse;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequest;

interface MessageConverter
{
    public function convertRequest(AmpRequest $request): PsrServerRequest;

    public function convertResponse(PsrResponse $response): AmpResponse;
}
