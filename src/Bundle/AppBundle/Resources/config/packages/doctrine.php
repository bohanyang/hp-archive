<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\AppBundle\Doctrine\Type\BingMarketType;
use App\Bundle\AppBundle\Doctrine\Type\JsonTextType;
use App\Bundle\AppBundle\Doctrine\Type\ObjectIdType;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine): void {
    $dbal = $doctrine->dbal();
    $dbal->type(BingMarketType::NAME, BingMarketType::class);
    $dbal->type(JsonTextType::NAME, JsonTextType::class);
    $dbal->type(ObjectIdType::NAME, ObjectIdType::class);
};
