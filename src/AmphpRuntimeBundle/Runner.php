<?php

declare(strict_types=1);

namespace App\AmphpRuntimeBundle;

use Amp\ByteStream;
use Amp\Cluster\Cluster;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Driver\SocketClientFactory;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;
use Amp\Http\Server\SocketHttpServer;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Closure;
use InvalidArgumentException;
use Monolog\Logger;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

use function get_debug_type;
use function getmypid;
use function sprintf;
use function str_starts_with;

class Runner implements RunnerInterface
{
    public function __construct(
        private Closure $appFactory,
        private array $sockets,
        private ?string $documentRoot = null,
    ) {
    }

    private function createLogHandler(): StreamHandler
    {
        // Creating a log handler in this way allows the script to be run in a cluster or standalone.
        if (Cluster::isWorker()) {
            return Cluster::createLogHandler();
        }

        $handler = new StreamHandler(ByteStream\getStdout());
        $handler->setFormatter(new ConsoleFormatter());

        return $handler;
    }

    public function run(): int
    {
        $errorHandler = new DefaultErrorHandler();

        $logger = new Logger('worker-' . (Cluster::getContextId() ?? getmypid()));
        $logger->pushHandler($this->createLogHandler());
        $logger->useLoggingLoopDetection(false);
        $server = new SocketHttpServer(
            logger: $logger,
            serverSocketFactory: Cluster::getServerSocketFactory(),
            clientFactory: new SocketClientFactory($logger),
        );

        foreach ($this->sockets as $socket) {
            $server->expose($socket);
        }

        $kernel = ($this->appFactory)();

        if (! $kernel instanceof KernelInterface) {
            throw new InvalidArgumentException(sprintf('Expecting "%s" while given "%s".', KernelInterface::class, get_debug_type($kernel)));
        }

        $kernel->boot();
        $container = $kernel->getContainer();

        /** @var RequestHandler */
        $ampRequestHandler = $container->get(RequestHandler::class);

        $documentRoot = new DocumentRoot($server, $errorHandler, __DIR__ . '/../../public');

        // Start the HTTP server
        $server->start(new ClosureRequestHandler(static function (Request $request) use ($documentRoot, $ampRequestHandler) {
            if (str_starts_with($request->getUri()->getPath(), '/build/')) {
                return $documentRoot->handleRequest($request);
            }

            return $ampRequestHandler->handleRequest($request);
        }), new DefaultErrorHandler());

        // Stop the server when the worker is terminated.
        Cluster::awaitTermination();

        $server->stop();

        return 0;
    }
}
