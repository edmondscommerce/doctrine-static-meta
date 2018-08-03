<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Builder;

use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @param string $envPath
 *
 * @return ContainerBuilder
 * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
 * @SuppressWarnings(PHPMD)
 */
function getContainer(string $envPath): ContainerBuilder
{
    SimpleEnv::setEnv($envPath);
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->autowire(Builder::class)->setPublic(true);
    (new Container())->addConfiguration($containerBuilder, $_SERVER);
    $containerBuilder->compile();

    return $containerBuilder;
}

function getBuilder(string $envPath): Builder
{
    /**
     * @var Builder
     */
    $builder = getContainer($envPath)->get(Builder::class);

    return $builder;
}
