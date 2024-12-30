<?php

declare(strict_types=1);

namespace App\AmphpRuntimeBundle\PsrHttpBridge;

use Amp\Http\Server\FormParser\FormParser;
use Amp\Http\Server\Request as AmpRequest;
use Amp\Http\Server\Response as AmpResponse;
use Amp\Socket\InternetAddress;
use League\Uri\Components\Query;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestFactoryInterface as PsrServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequest;

use function count;
use function iterator_to_array;
use function microtime;
use function preg_match;
use function strlen;
use function strncmp;
use function time;

final class PsrFactoryMessageConverter implements MessageConverter
{
    private const CHUNK_SIZE = 8192;

    public const DEFAULT_FIELD_COUNT_LIMIT = 1000;
    public const DEFAULT_BODY_SIZE_LIMIT   = 2 ** 20; // 1MB

    private FormParser $bodyParser;

    /** @var int */
    private $bodySizeLimit;

    public function __construct(
        private PsrServerRequestFactory $requestFactory,
        int $fieldCountLimit = self::DEFAULT_FIELD_COUNT_LIMIT,
        int $bodySizeLimit = self::DEFAULT_BODY_SIZE_LIMIT,
    ) {
        $this->bodyParser    = new FormParser($fieldCountLimit);
        $this->bodySizeLimit = $bodySizeLimit;
    }

    public function convertRequest(AmpRequest $request): PsrServerRequest
    {
        $uri           = $request->getUri();
        $client        = $request->getClient();
        $localAddress  = $client->getLocalAddress();
        $remoteAddress = $client->getRemoteAddress();

        $request->getBody()->increaseSizeLimit($this->bodySizeLimit);

        $server = [
            'HTTPS' => $client->getTlsInfo() !== null,
            'QUERY_STRING' => $uri->getQuery(),
            'REMOTE_ADDR' => $remoteAddress instanceof InternetAddress ? $remoteAddress->getAddress() : $remoteAddress->toString(),
            'REQUEST_METHOD' => $request->getMethod(),
            'REMOTE_USER' => $uri->getUserInfo(),
            'REMOTE_PORT' => $remoteAddress instanceof InternetAddress ? $remoteAddress->getPort() : null,
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
            'REQUEST_URI' => $uri->getPath(),
            'SERVER_ADDR' => $localAddress instanceof InternetAddress ? $localAddress->getAddress() : $localAddress->toString(),
            'SERVER_PORT' => $localAddress instanceof InternetAddress ? $localAddress->getAddress() : null,
            'SERVER_PROTOCOL' => 'HTTP/' . $request->getProtocolVersion(),
            'SERVER_SOFTWARE' => 'Amp HTTP Server',
        ];

        if ($request->hasHeader('accept')) {
            $server['HTTP_ACCEPT'] = $request->getHeader('accept');
        }

        if ($request->hasHeader('accept-charset')) {
            $server['HTTP_ACCEPT_CHARSET'] = $request->getHeader('accept-charset');
        }

        if ($request->hasHeader('accept-encoding')) {
            $server['HTTP_ACCEPT_ENCODING'] = $request->getHeader('accept-encoding');
        }

        if ($request->hasHeader('connection')) {
            $server['HTTP_CONNECTION'] = $request->getHeader('connection');
        }

        if ($request->hasHeader('referer')) {
            $server['HTTP_REFERER'] = $request->getHeader('referer');
        }

        if ($request->hasHeader('user-agent')) {
            $server['HTTP_USER_AGENT'] = $request->getHeader('user-agent');
        }

        if ($request->hasHeader('host')) {
            $server['HTTP_HOST'] = $request->getHeader('host');
        }

        $converted = $this->requestFactory->createServerRequest($request->getMethod(), $uri, $server);

        foreach ($request->getHeaders() as $field => $values) {
            $converted = $converted->withHeader($field, $values);
        }

        $cookies = [];
        foreach ($request->getCookies() as $cookie) {
            $cookies[$cookie->getName()] = $cookie->getValue();
        }

        $converted = $converted->withCookieParams($cookies);
        $converted = $converted->withQueryParams(iterator_to_array(Query::createFromRFC3986($uri->getQuery())->pairs()));
        $converted = $converted->withProtocolVersion($request->getProtocolVersion());

        $type = $request->getHeader('content-type');

        if (
            $type !== null
            && (
                strncmp($type, 'application/x-www-form-urlencoded', strlen('application/x-www-form-urlencoded')) === 0
                || preg_match('#^\s*multipart/(?:form-data|mixed)(?:\s*;\s*boundary\s*=\s*("?)([^"]*)\1)?$#', $type)
            )
        ) {
            $form       = $this->bodyParser->parseForm($request);
            $postValues = $form->getValues();
            foreach ($postValues as $key => $value) {
                if (count($value) === 1) {
                    $postValues[$key] = $value[0];
                }
            }

            $converted = $converted->withParsedBody($postValues);

            $files         = $form->getFiles();
            $uploadedFiles = [];
            foreach ($files as $fileset) {
                foreach ($fileset as $file) {
                    $uploadedFiles[] = new BufferedUploadedFile($file);
                }
            }

            $converted->withUploadedFiles($uploadedFiles);
        } else {
            $converted = $converted->withBody(new BufferedBody($request->getBody()->buffer()));
        }

        return $converted;
    }

    public function convertResponse(PsrResponse $response): AmpResponse
    {
        $body = $response->getBody();

        return new AmpResponse(
            $response->getStatusCode(),
            $response->getHeaders(),
            (string) $body,
        );
    }
}
