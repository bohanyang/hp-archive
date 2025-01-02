<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\HttpHandlerRunner\RequestHandlerRunnerInterface;
use Mezzio\Response\ServerRequestErrorResponseGenerator;
use Mezzio\Swoole\Command\ReloadCommand;
use Mezzio\Swoole\Command\ReloadCommandFactory;
use Mezzio\Swoole\Command\StartCommand;
use Mezzio\Swoole\Command\StartCommandFactory;
use Mezzio\Swoole\Command\StatusCommand;
use Mezzio\Swoole\Command\StatusCommandFactory;
use Mezzio\Swoole\Command\StopCommand;
use Mezzio\Swoole\Command\StopCommandFactory;
use Mezzio\Swoole\Event\EventDispatcherInterface;
use Mezzio\Swoole\Event\HotCodeReloaderWorkerStartListener;
use Mezzio\Swoole\Event\HotCodeReloaderWorkerStartListenerFactory;
use Mezzio\Swoole\Event\RequestEvent;
use Mezzio\Swoole\Event\RequestHandlerRequestListener;
use Mezzio\Swoole\Event\RequestHandlerRequestListenerFactory;
use Mezzio\Swoole\Event\ServerShutdownEvent;
use Mezzio\Swoole\Event\ServerShutdownListener;
use Mezzio\Swoole\Event\ServerShutdownListenerFactory;
use Mezzio\Swoole\Event\ServerStartEvent;
use Mezzio\Swoole\Event\ServerStartListener;
use Mezzio\Swoole\Event\ServerStartListenerFactory;
use Mezzio\Swoole\Event\StaticResourceRequestListener;
use Mezzio\Swoole\Event\StaticResourceRequestListenerFactory;
use Mezzio\Swoole\Event\SwooleListenerProvider;
use Mezzio\Swoole\Event\SwooleListenerProviderFactory;
use Mezzio\Swoole\Event\WorkerStartEvent;
use Mezzio\Swoole\Event\WorkerStartListener;
use Mezzio\Swoole\Event\WorkerStartListenerFactory;
use Mezzio\Swoole\HotCodeReload\FileWatcher\InotifyFileWatcher;
use Mezzio\Swoole\HotCodeReload\FileWatcherInterface;
use Mezzio\Swoole\HttpServerFactory;
use Mezzio\Swoole\Log\AccessLogFactory;
use Mezzio\Swoole\Log\AccessLogInterface;
use Mezzio\Swoole\Log\SwooleLoggerFactory;
use Mezzio\Swoole\PidManager;
use Mezzio\Swoole\PidManagerFactory;
use Mezzio\Swoole\ServerRequestSwooleFactory;
use Mezzio\Swoole\StaticMappedResourceHandler;
use Mezzio\Swoole\StaticMappedResourceHandlerFactory;
use Mezzio\Swoole\StaticResourceHandler;
use Mezzio\Swoole\StaticResourceHandler\FileLocationRepository;
use Mezzio\Swoole\StaticResourceHandler\FileLocationRepositoryFactory;
use Mezzio\Swoole\StaticResourceHandler\FileLocationRepositoryInterface;
use Mezzio\Swoole\StaticResourceHandlerFactory;
use Mezzio\Swoole\StaticResourceHandlerInterface;
use Mezzio\Swoole\SwooleRequestHandlerRunner;
use Mezzio\Swoole\SwooleRequestHandlerRunnerFactory;
use Mezzio\Swoole\Task\TaskEventDispatchListener;
use Mezzio\Swoole\Task\TaskEventDispatchListenerFactory;
use Mezzio\Swoole\Task\TaskInvokerListener;
use Mezzio\Swoole\Task\TaskInvokerListenerFactory;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Rainbowedge\SwooleRuntimeBundle\LaminasRunner;
use Rainbowedge\SwooleRuntimeBundle\Runtime;
use Rainbowedge\SwooleRuntimeBundle\SymfonyRequestHandler;
use Swoole\Http\Server as SwooleHttpServer;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('mezzio_swoole.config', [
        'application_root'   => '%kernel.project_dir%',
        'enable_coroutine'   => false,
        'hot-code-reload'    => [
            // Interval, in ms, that the InotifyFileWatcher should use to
            // check for changes.
            'interval' => 1000,
            // Paths to watch for changes. These may be files or
            // directories.
            'paths' => [
                '%kernel.project_dir%/src',
                '%kernel.project_dir%/composer.lock',
                '%kernel.project_dir%/symfony.lock',
                '%kernel.project_dir%/config',
            ],
        ],
        'swoole-http-server' => [
            'host' => '::',
            'port' => 80,
            // A prefix for the process name of the master process and workers.
            // By default the master process will be named `mezzio-master`,
            // each http worker `mezzio-worker-n` and each task worker
            // `mezzio-task-worker-n` where n is the id of the worker
            'process-name' => SwooleRequestHandlerRunner::DEFAULT_PROCESS_NAME,
            'options'      => [
                // We set a default for this. Without one, Swoole\Http\Server
                // defaults to the value of `ulimit -n`. Unfortunately, in
                // virtualized or containerized environments, this often
                // reports higher than the host container allows. 1024 is a
                // sane default; users should check their host system, however,
                // and set a production value to match.
                'max_conn' => 1024,
                'worker_num' => 8,
                'reload_async' => true,
                'max_wait_time' => 10,
                'max_request' => 50000,
            ],
            'static-files' => [
                'document-root'   => '%kernel.project_dir%/public',
                'etag-type' => 'weak',
                'gzip' => [
                    'level' => 4, // Integer between 1 and 9 indicating compression level to use.
                                  // Values less than 1 disable compression.
                ],
                'directives' => [
                 // Rules governing which server-side caching headers are emitted.
                 // Each key must be a valid regular expression, and should match
                 // typically only file extensions, but potentially full paths.
                 // When a static resource matches, all associated rules will apply.
                    '`^/build/`' => [
                        'cache-control' => [
                            'public',
                            'max-age=31536000',
                        ],
                        'last-modified' => true, // Emit a Last-Modified header?
                        'etag' => true, // Emit an ETag header?
                    ],
                ],
            ],
        ],
    ]);

    $services = $containerConfigurator->services();
    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $factories = [
        ReloadCommand::class                      => ReloadCommandFactory::class,
        StartCommand::class                       => StartCommandFactory::class,
        StatusCommand::class                      => StatusCommandFactory::class,
        StopCommand::class                        => StopCommandFactory::class,
        // EventDispatcherInterface::class           => EventDispatcherFactory::class,
        HotCodeReloaderWorkerStartListener::class => HotCodeReloaderWorkerStartListenerFactory::class,
        RequestHandlerRequestListener::class      => RequestHandlerRequestListenerFactory::class,
        ServerShutdownListener::class             => ServerShutdownListenerFactory::class,
        ServerStartListener::class                => ServerStartListenerFactory::class,
        StaticResourceRequestListener::class      => StaticResourceRequestListenerFactory::class,
        SwooleListenerProvider::class             => SwooleListenerProviderFactory::class,
        WorkerStartListener::class                => WorkerStartListenerFactory::class,
        AccessLogInterface::class                 => AccessLogFactory::class,
        SwooleLoggerFactory::SWOOLE_LOGGER        => SwooleLoggerFactory::class,
        PidManager::class                         => PidManagerFactory::class,
        SwooleRequestHandlerRunner::class         => SwooleRequestHandlerRunnerFactory::class,
        ServerRequestInterface::class             => ServerRequestSwooleFactory::class,
        StaticResourceHandler::class              => StaticResourceHandlerFactory::class,
        StaticMappedResourceHandler::class        => StaticMappedResourceHandlerFactory::class,
        SwooleHttpServer::class                   => HttpServerFactory::class,
        TaskEventDispatchListener::class          => TaskEventDispatchListenerFactory::class,
        TaskInvokerListener::class                => TaskInvokerListenerFactory::class,
        FileLocationRepository::class             => FileLocationRepositoryFactory::class,
    ];

    foreach ($factories as $instanceClass => $factoryClass) {
        $services->set($factoryClass);
        $services->set($instanceClass)
            ->factory(service($factoryClass))
            ->args([service('mezzio_swoole.container')]);
    }

    $invokables = [
        InotifyFileWatcher::class => InotifyFileWatcher::class,
        ServerRequestErrorResponseGenerator::class => ServerRequestErrorResponseGenerator::class,
    ];

    foreach ($invokables as $serviceId => $invokable) {
        $services->set($serviceId, $invokable);
    }

    // Event listeners
    $services->get(ServerStartListener::class)->tag('kernel.event_listener', ['event' => ServerStartEvent::class]);

    if ($containerConfigurator->env() === 'dev') {
        $services->get(HotCodeReloaderWorkerStartListener::class)->tag('kernel.event_listener', ['event' => WorkerStartEvent::class]);
    }

    $services->get(WorkerStartListener::class)->tag('kernel.event_listener', ['event' => WorkerStartEvent::class]);
    $services->get(StaticResourceRequestListener::class)->tag('kernel.event_listener', ['event' => RequestEvent::class, 'priority' => 10]);
    $services->get(RequestHandlerRequestListener::class)->tag('kernel.event_listener', ['event' => RequestEvent::class, 'priority' => 0]);
    $services->get(ServerShutdownListener::class)->tag('kernel.event_listener', ['event' => ServerShutdownEvent::class]);

    $aliases = [
        RequestHandlerRunner::class            => SwooleRequestHandlerRunner::class,
        StaticResourceHandlerInterface::class  => StaticResourceHandler::class,
        FileWatcherInterface::class            => InotifyFileWatcher::class,
        FileLocationRepositoryInterface::class => FileLocationRepository::class,
        EventDispatcherInterface::class        => PsrEventDispatcherInterface::class,
    ];

    foreach ($aliases as $alias => $serviceId) {
        $services->alias($alias, $serviceId);
    }

    $services->alias(RequestHandlerRunnerInterface::class, RequestHandlerRunner::class);

    $services->set(Runtime::RUNNER_SERVICE, LaminasRunner::class)->public();

    $services->set(SymfonyRequestHandler::class);

    $services->set(ServerRequestErrorResponseGenerator::class)
        ->args([
            service(ResponseFactoryInterface::class),
            param('%kernel.debug%'),
        ]);

    $services->set(HttpFoundationFactory::class);
    $services->alias(HttpFoundationFactoryInterface::class, HttpFoundationFactory::class);

    $services->set(PsrHttpFactory::class);
    $services->alias(HttpMessageFactoryInterface::class, PsrHttpFactory::class);

    $services->alias('mezzio_swoole.logger', LoggerInterface::class);

    $scopedContainer = [
        'config' => ['mezzio-swoole' => param('mezzio_swoole.config')],
        SwooleHttpServer::class => service(SwooleHttpServer::class),
        EventDispatcherInterface::class => service(EventDispatcherInterface::class),
        PidManager::class => service(PidManager::class),
        AccessLogInterface::class => service(AccessLogInterface::class),
        StaticResourceHandlerInterface::class => service(StaticResourceHandlerInterface::class),
        SwooleLoggerFactory::SWOOLE_LOGGER => service(SwooleLoggerFactory::SWOOLE_LOGGER),
        LoggerInterface::class => service('mezzio_swoole.logger'),
        'Mezzio\ApplicationPipeline' => service(SymfonyRequestHandler::class),
        ServerRequestErrorResponseGenerator::class => service(ServerRequestErrorResponseGenerator::class),
        FileWatcherInterface::class => service(FileWatcherInterface::class),
        ServerRequestInterface::class => service(ServerRequestInterface::class),
    ];

    $services->set('mezzio_swoole.container', ServiceLocator::class)
        ->args([$scopedContainer]);
};
