<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TestContainerFactory
{
    private static $container;

    public static function getContainerSingleton(array $config): ContainerInterface
    {
        if (null === self::$container) {
            self::$container = self::getContainer($config);
        }

        return self::$container;
    }

    public static function getContainer(array $config): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        (new Container())->addConfiguration($containerBuilder, $config);
        $containerBuilder->getDefinition(EntityManagerInterface::class)->setShared(false);
        $containerBuilder->compile();

        return $containerBuilder;
    }
}
