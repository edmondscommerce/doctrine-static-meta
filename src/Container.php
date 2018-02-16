<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\ValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Container
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Container implements ContainerInterface
{
    /**
     * This is the list of services managed by this container
     *
     * This list is used to also generate a PHPStorm meta data file which assists with dynamic type hinting when using
     * the container as a service locator
     *
     * @see ../../.phpstorm.meta.php/container.meta.php
     */
    public const SERVICES = [
        Config::class,
        Database::class,
        EntityGenerator::class,
        EntityManager::class,
        EntityManagerFactory::class,
        FileCreationTransaction::class,
        Filesystem::class,
        GenerateEntityCommand::class,
        GenerateRelationsCommand::class,
        NamespaceHelper::class,
        RelationsGenerator::class,
        RelationsGenerator::class,
        Schema::class,
        SchemaTool::class,
        SchemaValidator::class,
        SetRelationCommand::class,
        CodeHelper::class,
        ValidatorFactory::class,
        DoctrineCache::class,
        ValidatorInterface::class,
    ];

    /**
     * The directory that container cache files will be stored
     */
    public const CACHE_PATH = __DIR__.'/../cache/';

    public const SYMFONY_CACHE_PATH = self::CACHE_PATH.'/container.symfony.php';

    /**
     * @var bool
     */
    private $useCache = false;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param bool $useCache
     *
     * @return Container
     */
    public function setUseCache(bool $useCache): Container
    {
        $this->useCache = $useCache;

        return $this;
    }


    /**
     * Set a container instance
     *
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Take the $server array, normally a copy of $_SERVER, and pull out just the bits required by config
     *
     * @param array $server
     *
     * @return array
     */
    protected function configVars(array $server): array
    {
        $return = array_intersect_key(
            $server,
            array_flip(ConfigInterface::PARAMS)
        );

        return $return;
    }

    /**
     * @param array $server - normally you would pass in $_SERVER
     *
     * @throws DoctrineStaticMetaException
     */
    public function buildSymfonyContainer(array $server)
    {
        if (true === $this->useCache && file_exists(self::SYMFONY_CACHE_PATH)) {
            /** @noinspection PhpIncludeInspection */
            require self::SYMFONY_CACHE_PATH;
            $this->setContainer(new \ProjectServiceContainer());

            return;
        }

        try {
            $container = new ContainerBuilder();
            $this->addConfiguration($container, $server);
            $container->compile();
            $this->setContainer($container);
            $dumper = new PhpDumper($container);
            file_put_contents(self::SYMFONY_CACHE_PATH, $dumper->dump());
        } catch (ServiceNotFoundException|InvalidArgumentException $e) {
            throw new DoctrineStaticMetaException(
                'Exception building the container: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Build all the definitions, alias and other configuration for this container
     *
     * @param ContainerBuilder $container
     * @param array            $server
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function addConfiguration(ContainerBuilder $container, array $server): void
    {
        foreach (self::SERVICES as $class) {
            $container->autowire($class, $class)->setPublic(true);
        }

        $cacheDriver = $server[Config::PARAM_DOCTRINE_CACHE_DRIVER] ?? Config::DEFAULT_DOCTRINE_CACHE_DRIVER;
        $container->autowire($cacheDriver);

        $container->getDefinition(Config::class)
                  ->setArgument('$server', $this->configVars($server));

        /**
         * Which Cache Driver is used for the Cache Inteface?
         *
         * If Dev mode, we always use the Array Cache
         *
         * Otherwise, we use the Configured Cache driver (which defaults to Array Cache)
         */
        $container->setAlias(Cache::class, ($server[Config::PARAM_DEVMODE] ?? false) ?
            ArrayCache::class
            : $cacheDriver
        );

        $container->getDefinition(EntityManager::class)
                  ->addArgument(new Reference(Config::class))
                  ->setFactory(
                      [
                          new Reference(EntityManagerFactory::class),
                          'getEntityManager',
                      ]
                  );

        $container->setAlias(ConfigInterface::class, Config::class);

        $container->setAlias(EntityManagerInterface::class, EntityManager::class);

        $container->getDefinition(DoctrineCache::class)->addArgument(new Reference($cacheDriver));

        $container->setAlias(CacheInterface::class, DoctrineCache::class);

        $container->getDefinition(ValidatorInterface::class)
                  ->setFactory(
                      [
                          new Reference(ValidatorFactory::class),
                          'getValidator',
                      ]
                  );
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @throws DoctrineStaticMetaException
     */
    public function get($id)
    {
        try {
            return $this->container->get($id);
        } catch (ContainerExceptionInterface|NotFoundExceptionInterface $e) {
            throw new DoctrineStaticMetaException('Exception getting service '.$id, $e->getCode(), $e);
        }
    }

    /**
     * @param string $id
     * @SuppressWarnings(PHPMD.ShortVariable)
     *
     * @return bool|void
     */
    public function has($id)
    {
        $this->container->has($id);
    }
}
