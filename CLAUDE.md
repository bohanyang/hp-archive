# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

HP Archive is a Symfony 7.3 application that collects and stores Bing homepage images with a React/Inertia frontend. The application uses RoadRunner as the PHP application server with built-in scheduler support for background tasks.

## Architecture

### Backend Stack
- **Framework**: Symfony 7.3 with RoadRunner microkernel
- **PHP Runtime**: RoadRunner (not traditional PHP-FPM)
- **Database**: Doctrine ORM with support for multiple connections (default, import, source)
- **Message Queue**: Symfony Messenger with async, sync, and scheduler transports
- **Scheduler**: Symfony Scheduler with RoadRunner integration
- **Storage**: Supports multiple storage backends (Filesystem, S3, BunnyCDN) with replicated storage capability

### Frontend Stack
- **Build Tool**: Vite with Symfony integration
- **Framework**: React 18 with Inertia.js for server-side rendering
- **Styling**: Bootstrap 3 with Flat UI theme
- **State Management**: Jotai for React state
- **i18n**: react-i18next with language detection

### Key Architectural Patterns
- **Repository Pattern**: DoctrineRepository and LeanCloudRepository for data access
- **Message Bus**: Command/Handler pattern for async operations (SaveRecord, DownloadImage, CollectRecords, etc.)
- **Storage Abstraction**: StorageFactory creates appropriate storage implementations based on configuration
- **Image Specifications**: ImageSpec hierarchy for different image resolutions (Generic, Gd, Uhd)

## Common Development Commands

### Build & Development
```bash
# Frontend development with hot reload
pnpm dev

# Build frontend assets for production
pnpm build

# Start RoadRunner server (development)
rr serve -c .rr.local.yaml

# Start RoadRunner with worker processes (includes scheduler)
rr serve -c .rr.worker.yaml
```

### Symfony Console Commands
```bash
# Clear cache
php bin/console cache:clear

# Run database migrations
php bin/console doctrine:migrations:migrate

# Message queue consumers
php bin/console messenger:consume scheduler_default async

# Custom commands for data management
php bin/console app:collect-records
php bin/console app:save-record
php bin/console app:import-from-leancloud
php bin/console app:export-to-leancloud
php bin/console app:import-from-sql
```

### Code Quality
```bash
# Run PHP CodeSniffer with Doctrine coding standards
vendor/bin/phpcs

# Fix coding standard violations
vendor/bin/phpcbf

# Run PHPUnit tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/Path/To/TestClass.php
```

### Docker
```bash
# Build Docker image
docker build -t hp-archive .

# Run container
docker run -p 8080:8080 hp-archive
```

## Configuration

### Environment Variables
- `APP_ENV`: Environment (dev/prod/test)
- `APP_RUNTIME`: Set to `FluffyDiscord\RoadRunnerBundle\Runtime\Runtime` for RoadRunner
- Database connections configured in Doctrine DBAL
- Storage backends configured via Symfony parameters

### RoadRunner Configuration
- `.rr.yaml`: Production configuration with HTTP server on port 8080
- `.rr.worker.yaml`: Includes service workers for message consumption
- `.rr.local.yaml`: Development configuration (if exists)
- HTTP workers: 8 processes with 60s execution timeout
- Static file serving enabled for JS, CSS, WOFF, WOFF2, SVG, EOT

### Message Queue Configuration
- **async**: For image download and processing tasks
- **sync**: For immediate processing
- **scheduler_default**: For scheduled tasks via Symfony Scheduler
- **failed**: For failed message handling with retry strategy

## Testing

Tests are located in `tests/` directory. The project uses PHPUnit 9.5 with Symfony test utilities. Bootstrap file handles test environment setup.

## Deployment

The application is containerized with a multi-stage Dockerfile:
1. Node stage builds frontend assets with Vite
2. PHP stage installs dependencies and configures RoadRunner
3. Final image includes compiled assets and runs on port 8080

Production deployment uses:
- PHP 8.4 with OPcache and APCu enabled
- Composer optimized autoloader
- Environment variables compiled for performance
- RoadRunner for high-performance HTTP handling