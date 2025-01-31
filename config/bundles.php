<?php

declare(strict_types=1);

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use FluffyDiscord\RoadRunnerBundle\FluffyDiscordRoadRunnerBundle;
use Mango\Bundle\MangoBundle;
use Manyou\ClearServiceTagsBundle\ClearServiceTagsBundle;
use Pentatrion\ViteBundle\PentatrionViteBundle;
use Rompetomp\InertiaBundle\RompetompInertiaBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

return [
    FrameworkBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    WebProfilerBundle::class => ['dev' => true, 'test' => true],
    MonologBundle::class => ['all' => true],
    DebugBundle::class => ['dev' => true],
    MakerBundle::class => ['dev' => true],
    DoctrineBundle::class => ['all' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    TwigExtraBundle::class => ['all' => true],
    ClearServiceTagsBundle::class => ['dev' => true],
    MangoBundle::class => ['all' => true],
    PentatrionViteBundle::class => ['all' => true],
    RompetompInertiaBundle::class => ['all' => true],
    FluffyDiscordRoadRunnerBundle::class => ['all' => true],
];
