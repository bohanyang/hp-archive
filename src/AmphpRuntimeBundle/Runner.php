<?php

declare(strict_types=1);

namespace App\AmphpRuntimeBundle;

use Amp\ByteStream;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Driver\SocketClientFactory;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\SocketHttpServer;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Socket;
use Closure;
use InvalidArgumentException;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

use function Amp\trapSignal;
use function get_debug_type;
use function sprintf;

use const SIGHUP;
use const SIGINT;
use const SIGQUIT;
use const SIGTERM;

class Runner implements RunnerInterface
{
    public function __construct(
        private Closure $appFactory,
        private array $sockets,
    ) {
    }

    public function run(): int
    {
        $logHandler = new StreamHandler(ByteStream\getStdout());
        $logHandler->pushProcessor(new PsrLogMessageProcessor());
        $logHandler->setFormatter(new ConsoleFormatter());
        $logger = new Logger('server');
        $logger->pushHandler($logHandler);
        $logger->useLoggingLoopDetection(false);

        $server = new SocketHttpServer(
            $logger,
            new Socket\ResourceServerSocketFactory(),
            new SocketClientFactory($logger),
        );

        foreach ($this->sockets as $socket) {
            $server->expose($socket);
        }

        $errorHandler = new DefaultErrorHandler();

        $kernel = ($this->appFactory)();

        if (! $kernel instanceof KernelInterface) {
            throw new InvalidArgumentException(sprintf('Expecting "%s" while given "%s".', KernelInterface::class, get_debug_type($kernel)));
        }

        $kernel->boot();
        $container = $kernel->getContainer();

        /** @var RequestHandler */
        $ampRequestHandler = $container->get(RequestHandler::class);

        $documentRoot = new DocumentRoot($server, $errorHandler, __DIR__ . '/../../public');
        $documentRoot->setFallback($ampRequestHandler);

        $server->start($documentRoot, new DefaultErrorHandler());

        // Await a termination signal to be received.
        $signal = trapSignal([SIGHUP, SIGINT, SIGQUIT, SIGTERM]);

        $logger->info(sprintf('Received signal %d, stopping HTTP server', $signal));

        $server->stop();

        return 0;
    }
}
