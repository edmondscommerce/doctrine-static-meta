<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

// phpcs:disable
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use EdmondsCommerce\DoctrineStaticMeta\Builder\Builder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEmbeddableAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CliConfigCommandFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateConstraintCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\FinaliseBuildCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableFromArchetypeCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableSkeletonCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverrideCreateCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverridesUpdateCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\RemoveUnusedRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetEmbeddableCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\FakerData\EmbeddableFakerDataCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\HasEmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\Objects\EmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Objects\EmbeddableCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Traits\HasEmbeddableTraitCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\AbstractEntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityDtoFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Interfaces\EntityInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\AbstractEntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\EntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntitySaverCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUnitOfWorkHelperCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUpserterCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\Entity\Fixtures\EntityFixtureCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\BootstrapCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\AbstractEntityTestCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\EntityTestCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\AbstractTestFakerDataProviderUpdater;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\StandardLibraryTestGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\CopyPhpstormMeta;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\EntityFormatter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityDataValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\RepositoryFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkSimpleEntityCreator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFillerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelperFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Initialiser;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\DoctrineStaticMeta\Schema\UuidFunctionPolyfill;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

// phpcs:enable

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
     * @see ./../../.phpstorm.meta.php/container.meta.php
     */
    public const SERVICES = [
        \Ramsey\Uuid\UuidFactory::class,
        AbstractEntityFactoryCreator::class,
        AbstractEntityRepositoryCreator::class,
        AbstractEntityTestCreator::class,
        AbstractTestFakerDataProviderUpdater::class,
        ArchetypeEmbeddableGenerator::class,
        ArrayCache::class,
        BootstrapCreator::class,
        Builder::class,
        BulkEntitySaver::class,
        BulkEntityUpdater::class,
        BulkSimpleEntityCreator::class,
        CliConfigCommandFactory::class,
        CodeHelper::class,
        Config::class,
        ContainerConstraintValidatorFactory::class,
        CopyPhpstormMeta::class,
        CreateConstraintAction::class,
        CreateConstraintCommand::class,
        CreateDtosForAllEntitiesAction::class,
        CreateEmbeddableAction::class,
        CreateEntityAction::class,
        Database::class,
        DiffCommand::class,
        DoctrineCache::class,
        DtoCreator::class,
        DtoFactory::class,
        EmbeddableCreator::class,
        EmbeddableFakerDataCreator::class,
        EmbeddableInterfaceCreator::class,
        EntityCreator::class,
        EntityDataValidator::class,
        EntityDataValidatorFactory::class,
        EntityDependencyInjector::class,
        EntityDtoFactoryCreator::class,
        EntityEmbeddableSetter::class,
        EntityFactory::class,
        EntityFactoryCreator::class,
        EntityFieldSetter::class,
        EntityFixtureCreator::class,
        EntityFormatter::class,
        EntityGenerator::class,
        EntityInterfaceCreator::class,
        EntityIsValidConstraintCreator::class,
        EntityIsValidConstraintValidatorCreator::class,
        EntityManagerFactory::class,
        EntityManagerInterface::class,
        EntityRepositoryCreator::class,
        EntitySaver::class,
        EntitySaverCreator::class,
        EntitySaverFactory::class,
        EntityTestCreator::class,
        EntityUnitOfWorkHelperCreator::class,
        EntityUpserterCreator::class,
        ExecuteCommand::class,
        FakerDataFillerFactory::class,
        FieldGenerator::class,
        FileCreationTransaction::class,
        FileFactory::class,
        FileOverrider::class,
        Filesystem::class,
        FilesystemCache::class,
        FinaliseBuildCommand::class,
        FindAndReplaceHelper::class,
        FindReplaceFactory::class,
        Finder::class,
        FixturesHelperFactory::class,
        GenerateCommand::class,
        GenerateEmbeddableFromArchetypeCommand::class,
        GenerateEmbeddableSkeletonCommand::class,
        GenerateEntityCommand::class,
        GenerateFieldCommand::class,
        GenerateRelationsCommand::class,
        HasEmbeddableInterfaceCreator::class,
        HasEmbeddableTraitCreator::class,
        IdTrait::class,
        LatestCommand::class,
        MigrateCommand::class,
        MysqliConnectionFactory::class,
        NamespaceHelper::class,
        OverrideCreateCommand::class,
        OverridesUpdateCommand::class,
        PathHelper::class,
        PropertyConstraintCreator::class,
        PropertyConstraintValidatorCreator::class,
        ReflectionHelper::class,
        RelationsGenerator::class,
        RemoveUnusedRelationsCommand::class,
        RepositoryFactory::class,
        Schema::class,
        SchemaTool::class,
        SchemaValidator::class,
        SetEmbeddableCommand::class,
        SetFieldCommand::class,
        SetRelationCommand::class,
        StandardLibraryTestGenerator::class,
        StatusCommand::class,
        TestCodeGenerator::class,
        TestEntityGeneratorFactory::class,
        TypeHelper::class,
        UnusedRelationsRemover::class,
        UpToDateCommand::class,
        UuidFactory::class,
        UuidFunctionPolyfill::class,
        VersionCommand::class,
        Writer::class,
        Initialiser::class,
    ];

    public const ALIASES = [
        EntityFactoryInterface::class              => EntityFactory::class,
        EntityDataValidatorInterface::class        => EntityDataValidator::class,
        ConstraintValidatorFactoryInterface::class => ContainerConstraintValidatorFactory::class,
    ];

    public const NOT_SHARED_SERVICES = [
    ];


    /**
     * The directory that container cache files will be stored
     */
    public const CACHE_PATH = __DIR__ . '/../cache/';

    public const SYMFONY_CACHE_PATH = self::CACHE_PATH . '/container.symfony.php';

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
     * @param array $server - normally you would pass in $_SERVER
     *
     * @throws DoctrineStaticMetaException
     */
    public function buildSymfonyContainer(array $server): void
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
        } catch (ServiceNotFoundException | InvalidArgumentException $e) {
            throw new DoctrineStaticMetaException(
                'Exception building the container: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Set a container instance
     *
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Build all the definitions, alias and other configuration for this container. Each of these steps need to be
     * carried out to allow the everything to work, however you may wish to change individual bits. Therefore this
     * method has been made final, but the individual methods can be overwritten if you extend off the class
     *
     * @param ContainerBuilder $containerBuilder
     * @param array            $server
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    final public function addConfiguration(ContainerBuilder $containerBuilder, array $server): void
    {
        $this->autoWireServices($containerBuilder);
        $this->defineConfig($containerBuilder, $server);
        $this->defineCache($containerBuilder, $server);
        $this->defineEntityManager($containerBuilder);
        $this->configureValidationComponents($containerBuilder);
        $this->defineAliases($containerBuilder);
        $this->registerCustomFakerDataFillers($containerBuilder);
    }

    /**
     * This takes every class from the getServices method, auto wires them and marks them as public. You may wish to
     * override this if you want to mark certain classes as private
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function autoWireServices(ContainerBuilder $containerBuilder): void
    {
        $services = $this->getServices();
        foreach ($services as $class) {
            $containerBuilder->autowire($class, $class)->setPublic(true);
        }
    }

    /**
     * This is a simple wrapper around the class constants. You can use this to add, remove, or replace individual
     * services that will be auto wired
     *
     * @return array
     */
    public function getServices(): array
    {
        return self::SERVICES;
    }

    /**
     * This is used to auto wire the config interface. It sets the $server param as a constructor argument and then
     * sets the concrete class as the implementation for the Interface. Override this if you wish to use different
     * logic for where the config comes from
     *
     * @param ContainerBuilder $containerBuilder
     * @param array            $server
     */
    public function defineConfig(ContainerBuilder $containerBuilder, array $server): void
    {
        $containerBuilder->getDefinition(Config::class)->setArgument('$server', $this->configVars($server));
        $containerBuilder->setAlias(ConfigInterface::class, Config::class);
    }

    /**
     * This is used to auto wire the doctrine cache. If we are in dev mode then this will always use the Array Cache,
     * if not then the cache will be set to what is in the $server array. Override this method if you wish to use
     * different logic to handle caching
     *
     * @param ContainerBuilder $containerBuilder
     * @param array            $server
     */
    public function defineCache(ContainerBuilder $containerBuilder, array $server): void
    {
        $cacheDriver = $server[Config::PARAM_DOCTRINE_CACHE_DRIVER] ?? Config::DEFAULT_DOCTRINE_CACHE_DRIVER;
        $containerBuilder->autowire($cacheDriver)->setPublic(true);
        $this->configureFilesystemCache($containerBuilder);
        /**
         * Which Cache Driver is used for the Cache Interface?
         *
         * If Dev mode, we always use the Array Cache
         *
         * Otherwise, we use the Configured Cache driver (which defaults to Array Cache)
         */
        $cache = ($server[Config::PARAM_DEVMODE] ?? false) ? ArrayCache::class : $cacheDriver;
        $containerBuilder->setAlias(Cache::class, $cache)->setPublic(true);
        $containerBuilder->getDefinition(DoctrineCache::class)->addArgument(new Reference($cache))->setPublic(true);
    }

    /**
     * This is used to auto wire the entity manager. It first adds the DSM factory as the factory for the class, and
     * sets the Entity Manager as the implementation of the interface. Overrider this if you want to use your own
     * factory to create and configure the entity manager
     *
     * @param ContainerBuilder $container
     */
    public function defineEntityManager(ContainerBuilder $container): void
    {
        $container->getDefinition(EntityManagerInterface::class)
                  ->addArgument(new Reference(Config::class))
                  ->setFactory(
                      [
                          new Reference(EntityManagerFactory::class),
                          'getEntityManager',
                      ]
                  );
    }

    /**
     * Ensure we are using the container constraint validator factory so that custom validators with dependencies can
     * simply declare them as normal. Note that you will need to define each custom validator as a service in your
     * container.
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function configureValidationComponents(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->getDefinition(EntityDataValidator::class)
                         ->setFactory(
                             [
                                 new Reference(EntityDataValidatorFactory::class),
                                 'buildEntityDataValidator',
                             ]
                         )->setShared(false);
    }

    public function defineAliases(ContainerBuilder $containerBuilder): void
    {
        foreach (self::ALIASES as $interface => $service) {
            $containerBuilder->setAlias($interface, $service)->setPublic(true);
        }
    }

    /**
     * Some service should not be Singletons (shared) but should always be a new instance
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function updateNotSharedServices(ContainerBuilder $containerBuilder): void
    {
        foreach (self::NOT_SHARED_SERVICES as $service) {
            $containerBuilder->getDefinition($service)->setShared(false);
        }
    }

    private function registerCustomFakerDataFillers(ContainerBuilder $containerBuilder): void
    {
        $config = $this->getConfig($containerBuilder);
        $path   = $config->get(Config::PARAM_ENTITIES_CUSTOM_DATA_FILLER_PATH);
        if (!is_dir($path)) {
            return;
        }
        /** @var Finder $finder */
        $finder = $containerBuilder->get(Finder::class);
        $files           = $finder->files()->name('*FakerDataFiller.php')->in($path);
        $baseNameSpace   = $config->get(Config::PARAM_PROJECT_ROOT_NAMESPACE);
        $mappings        = [];
        foreach ($files as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $dataFillerClassName = $baseNameSpace . '\\Assets\\Entity\\FakerDataFillers';
            $entityClassName     = $baseNameSpace . '\\Entities';
            $relativePath        = str_replace('/', '\\', $file->getRelativePath());
            if ($relativePath !== '') {
                $dataFillerClassName .= '\\' . $relativePath;
                $entityClassName     .= '\\' . $relativePath;
            }
            $fileName                   = $file->getBasename('.php');
            $dataFillerClassName        .= '\\' . $fileName;
            $entityClassName            .= '\\' . str_replace('FakerDataFiller', '', $fileName);
            $mappings[$entityClassName] = $dataFillerClassName;
        }

        $containerBuilder->getDefinition(FakerDataFillerFactory::class)
                         ->addMethodCall('setCustomFakerDataFillersFqns', [$mappings]);
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
        } catch (ContainerExceptionInterface | NotFoundExceptionInterface $e) {
            throw new DoctrineStaticMetaException('Exception getting service ' . $id, $e->getCode(), $e);
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
        return $this->container->has($id);
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

    private function configureFilesystemCache(ContainerBuilder $containerBuilder): void
    {
        $config = $this->getConfig($containerBuilder);
        $containerBuilder->getDefinition(FilesystemCache::class)
                         ->addArgument($config->get(Config::PARAM_FILESYSTEM_CACHE_PATH))
                         ->setPublic(true);
    }

    private function getConfig(ContainerBuilder $containerBuilder): Config
    {
        return $containerBuilder->get(Config::class);
    }
}
