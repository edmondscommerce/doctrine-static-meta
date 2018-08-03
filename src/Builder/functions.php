<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Builder;

use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Symfony\Component\DependencyInjection\ContainerBuilder;

function getContainer(): ContainerBuilder
{
    SimpleEnv::setEnv(__DIR__.'/../.env');
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->autowire(Builder::class)->setPublic(true);
    (new Container())->addConfiguration($containerBuilder, $_SERVER);
    $containerBuilder->compile();

    return $containerBuilder;
}

function getBuilder(): Builder
{
    return getContainer()->get(Builder::class);
}
