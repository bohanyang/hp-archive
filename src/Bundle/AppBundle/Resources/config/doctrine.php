<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Doctrine\Type\BingMarketType;
use App\Doctrine\Type\JsonTextType;
use App\Doctrine\Type\ObjectIdType;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine): void {
    $dbal = $doctrine->dbal();
    $dbal->type(BingMarketType::NAME, BingMarketType::class);
    $dbal->type(JsonTextType::NAME, JsonTextType::class);
    $dbal->type(ObjectIdType::NAME, ObjectIdType::class);
};
