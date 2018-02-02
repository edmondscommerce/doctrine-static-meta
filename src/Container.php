<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\SchemaBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Filesystem\Filesystem;

class Container implements ContainerInterface
{
    const SERVICES = [
        Config::class,
        Database::class,
        EntityGenerator::class,
        EntityManager::class,
        FileCreationTransaction::class,
        Filesystem::class,
        GenerateEntityCommand::class,
        GenerateRelationsCommand::class,
        MappingHelper::class,
        NamespaceHelper::class,
        RelationsGenerator::class,
        RelationsGenerator::class,
        SchemaBuilder::class,
        SchemaTool::class,
        SetRelationCommand::class,
    ];

    const CACHE_PATH = __DIR__.'/../cache/';

    const SYMFONY_CACHE_PATH = self::CACHE_PATH.'/container.symfony.php';

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
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
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
     */
    public function buildSymfonyContainer(array $server)
    {
        if (true === $this->useCache && file_exists(self::SYMFONY_CACHE_PATH)) {
            require self::SYMFONY_CACHE_PATH;
            $this->setContainer(new \ProjectServiceContainer());

            return;
        }
        $container = new ContainerBuilder();
        foreach (self::SERVICES as $class) {
            $container->autowire($class, $class)->setPublic(true);
        }

        $container->getDefinition(Config::class)
                  ->setArgument('$server', $this->configVars($server));
        $container->getDefinition(EntityManager::class)
                  ->setFactory(
                      [
                          DevEntityManagerFactory::class,
                          'getEm',
                      ]
                  );
        $container->setAlias(ConfigInterface::class, Config::class);
        $container->setAlias(EntityManagerInterface::class, EntityManager::class);
        $container->compile();
        $this->setContainer($container);
        $dumper = new PhpDumper($container);
        file_put_contents(self::SYMFONY_CACHE_PATH, $dumper->dump());
    }

    /**
     * @param string $id
     *
     * @return mixed|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * @param string $id
     *
     * @return bool|void
     */
    public function has($id)
    {
        $this->container->has($id);
    }


}
