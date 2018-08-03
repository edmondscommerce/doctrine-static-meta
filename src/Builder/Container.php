<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Builder;

use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Container
{
    /**
     * @param string $envPath
     *
     * @return ContainerBuilder
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     * @SuppressWarnings(PHPMD)
     */
    public static function getContainer(string $envPath): ContainerBuilder
    {
        SimpleEnv::setEnv($envPath);
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->autowire(Builder::class)->setPublic(true);
        (new Container())->addConfiguration($containerBuilder, $_SERVER);
        $containerBuilder->compile();

        return $containerBuilder;
    }

    /**
     * @param string $envPath
     *
     * @return Builder
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     */
    public static function getBuilder(string $envPath): Builder
    {
        /**
         * @var Builder
         */
        $builder = self::getContainer($envPath)->get(Builder::class);

        return $builder;
    }

}
