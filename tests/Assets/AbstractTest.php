<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use Closure;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\RepositoryFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFillerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use EdmondsCommerce\PHPQA\Constants;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use ts\Reflection\ReflectionClass;

use function get_class;
use function spl_autoload_functions;
use function spl_autoload_unregister;
use function str_replace;

/**
 * Class AbstractTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class AbstractTest extends TestCase
{
    public const TEST_TYPE_SMALL              = 'Small';
    public const TEST_TYPE_MEDIUM             = 'Medium';
    public const TEST_TYPE_LARGE              = 'Large';
    public const VAR_PATH                     = __DIR__ . '/../../var/testOutput/';
    public const WORK_DIR                     = 'override me';
    public const TEST_PROJECT_ROOT_NAMESPACE  = 'My\\Test\\Project';
    public const TEST_ENTITIES_ROOT_NAMESPACE = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' .
                                                AbstractGenerator::ENTITIES_FOLDER_NAME;
    protected static $buildOnce = false;
    protected static $built     = false;
    /**
     * @var Container
     */
    protected static $containerStaticRef;
    /**
     * The absolute path to the Entities folder, eg:
     * /var/www/vhosts/doctrine-static-meta/var/{testWorkDir}/Entities
     *
     * @var string
     */
    protected $entitiesPath = '';
    /**
     * The absolute path to the EntityRelations folder, eg:
     * /var/www/vhosts/doctrine-static-meta/var/{testWorkDir}/Entity/Relations
     *
     * @var string
     */
    protected $entityRelationsPath = '';
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var string|null
     */
    protected $copiedWorkDir;
    /**
     * @var string|null
     */
    protected $copiedRootNamespace;

    /**
     * Ensure built steps are set to false when test class is instantiated
     */
    public static function setUpBeforeClass()
    {
        self::$built   = false;
        static::$built = false;
    }

    public static function tearDownAfterClass()
    {
        if (!(self::$containerStaticRef instanceof Container)) {
            return;
        }
        $entityManager = static::$containerStaticRef->get(EntityManagerInterface::class);
        $connection    = $entityManager->getConnection();

        $entityManager->close();
        $connection->close();
        static::$containerStaticRef = null;
    }


    /**
     * Prepare working directory, ensure its empty, create entities folder and set up env variables
     *
     * The order of these actions is critical
     */
    public function setUp()
    {
        if (false !== stripos(static::WORK_DIR, self::WORK_DIR)) {
            throw new RuntimeException(
                "You must set a `public const WORK_DIR=AbstractTest::VAR_PATH.'/'"
                . ".self::TEST_TYPE.'/folderName/';` in your test class"
            );
        }
        if (
            false === strpos(static::WORK_DIR, '/' . static::TEST_TYPE_SMALL)
            && false === strpos(static::WORK_DIR, '/' . static::TEST_TYPE_MEDIUM)
            && false === strpos(static::WORK_DIR, '/' . static::TEST_TYPE_LARGE)
        ) {
            throw new RuntimeException(
                'Your WORK_DIR is missing the test type, should look like: '
                . "`public const WORK_DIR=AbstractTest::VAR_PATH.'/'"
                . ".self::TEST_TYPE_(SMALL|MEDIUM|LARGE).'/folderName/';` in your test class"
            );
        }
        $this->copiedWorkDir       = null;
        $this->copiedRootNamespace = null;
        $this->entitiesPath        = static::WORK_DIR
                                     . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                     . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME;
        $this->entityRelationsPath = static::WORK_DIR
                                     . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                     . '/' . AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME;
        $this->clearWorkDir();
        $this->setupContainer($this->entitiesPath);
        $this->clearCache();
        $this->extendAutoloader(
            static::TEST_PROJECT_ROOT_NAMESPACE . '\\',
            static::WORK_DIR
        );
    }

    protected function clearWorkDir(): void
    {
        if (true === static::$buildOnce && true === static::$built) {
            $this->entitiesPath = $this->getRealPath($this->entitiesPath);

            return;
        }
        $this->getFileSystem()->mkdir(static::WORK_DIR);
        $this->emptyDirectory(static::WORK_DIR);
        $this->getFileSystem()->mkdir($this->entitiesPath);
        $this->entitiesPath = $this->getRealPath($this->entitiesPath);

        $this->getFileSystem()->mkdir($this->entityRelationsPath);
        $this->entityRelationsPath = realpath($this->entityRelationsPath);
    }

    protected function getRealPath(string $path)
    {
        $realpath = realpath($path);
        if (false === $realpath) {
            throw new RuntimeException('Failed getting realpath for path: ' . $path);
        }

        return $realpath;
    }

    protected function getFileSystem(): Filesystem
    {
        if (null === $this->filesystem) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    protected function emptyDirectory(string $path): void
    {
        $fileSystem = $this->getFileSystem();
        $fileSystem->remove($path);
        $fileSystem->mkdir($path);
    }

    /**
     * @param string $entitiesPath
     *
     * @throws ConfigException
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setupContainer(string $entitiesPath): void
    {
        SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
        $testConfig                                               = $_SERVER;
        $testConfig[ConfigInterface::PARAM_ENTITIES_PATH]         = $entitiesPath;
        $testConfig[ConfigInterface::PARAM_DB_NAME]               .= '_test';
        $testConfig[ConfigInterface::PARAM_DEVMODE]               = true;
        $testConfig[ConfigInterface::PARAM_FILESYSTEM_CACHE_PATH] = static::WORK_DIR . '/cache/dsm';
        $this->container                                          = new Container();
        $this->container->buildSymfonyContainer($testConfig);
        static::$containerStaticRef = $this->container;
    }

    /**
     * Clear the Doctrine Cache
     *
     * @throws DoctrineStaticMetaException
     */
    protected function clearCache(): void
    {
        $cache = $this->getEntityManager()
                      ->getConfiguration()
                      ->getMetadataCacheImpl();
        if ($cache instanceof CacheProvider) {
            $cache->deleteAll();
        }
    }

    /**
     * @return EntityManagerInterface
     * @throws DoctrineStaticMetaException
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->container->get(EntityManagerInterface::class);
    }

    /**
     * Accesses the standard Composer autoloader
     *
     * Extends the class and also providing an entry point for xdebugging
     *
     * Extends this with a PSR4 root for our WORK_DIR
     *
     * @param string $namespace
     * @param string $path
     *
     * @throws ReflectionException
     */
    protected function extendAutoloader(string $namespace, string $path): void
    {
        //Unregister any previously set extension first
        $registered = spl_autoload_functions();
        foreach ($registered as $loader) {
            if ($loader instanceof Closure) {
                continue;
            }
            if ((new  ReflectionClass(get_class($loader[0])))->isAnonymous()) {
                spl_autoload_unregister($loader);
            }
        }
        //Then build a new extension and register it
        $namespace  = rtrim($namespace, '\\') . '\\';
        $testLoader = new class ($namespace) extends ClassLoader
        {
            /**
             * @var string
             */
            protected $namespace;

            public function __construct(string $namespace)
            {
                $this->namespace = $namespace;
            }

            public function loadClass($class)
            {
                if (false === strpos($class, $this->namespace)) {
                    return false;
                }
                $found = parent::loadClass($class);
                if (false === $found || null === $found) {
                    //good point to set a breakpoint
                    return $found;
                }

                return $found;
            }
        };
        $testLoader->addPsr4($namespace, $path . '/src', true);
        $testLoader->addPsr4($namespace, $path . '/tests', true);
        $testLoader->register();
    }

    /**
     * Run QA tools against the generated code
     *
     * Can specify a custom namespace root if required
     *
     * Will run:
     *
     * - PHP linting
     * - PHPStan
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param null|string $namespaceRoot
     *
     * @return bool
     */
    public function qaGeneratedCode(?string $namespaceRoot = null): bool
    {
        if ($this->isQuickTests()) {
            self::assertTrue(true);

            return true;
        }
        $workDir       = static::WORK_DIR;
        $namespaceRoot = trim($namespaceRoot ?? static::TEST_PROJECT_ROOT_NAMESPACE, '\\');
        if (null !== $this->copiedRootNamespace) {
            $workDir       = $this->copiedWorkDir;
            $namespaceRoot = trim($this->copiedRootNamespace, '\\');
        }
        static $codeValidator;
        if (null === $codeValidator) {
            $codeValidator = new CodeValidator();
        }
        $errors = $codeValidator($workDir, $namespaceRoot);
        self::assertNull($errors);

        return true;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function isQuickTests(): bool
    {
        if (
            isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            return true;
        }

        return false;
    }

    public function getDataFillerFactory(): FakerDataFillerFactory
    {
        /**
         * @var FakerDataFillerFactory $factory
         */
        $factory         = $this->container->get(FakerDataFillerFactory::class);
        $abstractTestFqn =
            ($this->copiedRootNamespace ?? self::TEST_PROJECT_ROOT_NAMESPACE) . '\\Entities\\AbstractEntityTest';
        $factory->setFakerDataProviders($abstractTestFqn::FAKER_DATA_PROVIDERS);

        return $factory;
    }

    protected function getUuidFactory(): UuidFactory
    {
        return $this->container->get(UuidFactory::class);
    }

    protected function generateTestCode(): void
    {
        if (false === static::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(static::WORK_DIR);
            static::$built = true;
        }
    }

    protected function getTestCodeGenerator(): TestCodeGenerator
    {
        return $this->container->get(TestCodeGenerator::class);
    }

    protected function recreateDtos(): void
    {
        /**
         * @var CreateDtosForAllEntitiesAction $dtoAction
         */
        $dtoAction = $this->container->get(CreateDtosForAllEntitiesAction::class);
        $dtoAction->setProjectRootNamespace($this->copiedRootNamespace)
                  ->setProjectRootDirectory($this->copiedWorkDir)
                  ->run();
    }

    protected function tearDown()
    {
        $entityManager = $this->getEntityManager();
        $connection    = $entityManager->getConnection();

        $entityManager->close();
        $connection->close();
    }

    protected function getRepositoryFactory(): RepositoryFactory
    {
        return $this->container->get(RepositoryFactory::class);
    }

    protected function getNamespaceHelper(): NamespaceHelper
    {
        return $this->container->get(NamespaceHelper::class);
    }

    protected function dump(EntityInterface $entity): string
    {
        return (new EntityDebugDumper())->dump($entity, $this->getEntityManager());
    }

    /**
     * If PHP loads any files whilst generating, then subsequent changes to those files will not have any effect
     *
     * To resolve this, we need to clone the copied code into a new namespace before running it
     *
     * We only allow copying to a new work dir once per test run, different extras must be used
     *
     * @return string $copiedWorkDir
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    protected function setupCopiedWorkDir(): string
    {
        $copiedNamespaceRoot       = $this->getCopiedNamespaceRoot();
        $this->copiedWorkDir       = rtrim(static::WORK_DIR, '/') . 'Copies/' . $copiedNamespaceRoot . '/';
        $this->entitiesPath        = $this->copiedWorkDir . '/src/Entities/';
        $this->copiedRootNamespace = $copiedNamespaceRoot;
        if (is_dir($this->copiedWorkDir)) {
            throw new RuntimeException(
                'The Copied WorkDir ' . $this->copiedWorkDir . ' Already Exists'
            );
        }
        if (is_dir($this->copiedWorkDir)) {
            $this->getFileSystem()->remove($this->copiedWorkDir);
        }
        $codeCopier = new CodeCopier(
            $this->getFileSystem(),
            $this->container->get(FindAndReplaceHelper::class)
        );
        $codeCopier->copy(
            static::WORK_DIR,
            $this->copiedWorkDir,
            self::TEST_PROJECT_ROOT_NAMESPACE,
            $copiedNamespaceRoot
        );
        $this->extendAutoloader(
            $this->copiedRootNamespace . '\\',
            $this->copiedWorkDir
        );
        $this->clearCache();
        $this->setupContainer(
            $this->copiedWorkDir
            . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
            . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME
        );

        $this->loadAllEntityMetaData();

        return $this->copiedWorkDir;
    }

    /**
     * Get the namespace root to use in a copied work dir
     *
     * @return string
     * @throws ReflectionException
     */
    protected function getCopiedNamespaceRoot(): string
    {
        $name          = ucwords($this->getName());
        $namespaceName = preg_replace('%[^a-z0-9]+%i', '_', $name);

        return (new  ReflectionClass(static::class))->getShortName() . '_' . $namespaceName . '_';
    }

    private function loadAllEntityMetaData(): void
    {
        $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
    }

    /**
     * When working with a copied work dir, use this function to translate the FQN of any Entities etc
     *
     * This will replace both the raw TestCodeGenerator root namespace and the test level root namespace
     *
     * @param string $fqn
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    protected function getCopiedFqn(string $fqn): string
    {
        $copiedNamespaceRoot = $this->getCopiedNamespaceRoot();

        $currentRootRemoved = str_replace(
            static::TEST_PROJECT_ROOT_NAMESPACE,
            '',
            $fqn
        );
        $currentRootRemoved = ltrim(
            $currentRootRemoved,
            '\\'
        );

        return $this->container
            ->get(NamespaceHelper::class)
            ->tidy('\\' . $copiedNamespaceRoot . '\\' . $currentRootRemoved);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function isTravis(): bool
    {
        return isset($_SERVER['TRAVIS']);
    }

    protected function getEntityEmbeddableSetter(): EntityEmbeddableSetter
    {
        $setter = $this->container->get(EntityEmbeddableSetter::class);
        $setter->setPathToProjectRoot($this->copiedWorkDir ?? static::WORK_DIR);
        $setter->setProjectRootNamespace($this->copiedRootNamespace ?? self::TEST_PROJECT_ROOT_NAMESPACE);

        return $setter;
    }

    /**
     * @return ArchetypeEmbeddableGenerator
     * @throws DoctrineStaticMetaException
     */
    protected function getArchetypeEmbeddableGenerator(): ArchetypeEmbeddableGenerator
    {
        /**
         * @var ArchetypeEmbeddableGenerator $generator
         */
        $generator = $this->container->get(ArchetypeEmbeddableGenerator::class);
        $generator->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
                  ->setPathToProjectRoot(static::WORK_DIR);

        return $generator;
    }

    protected function assertNoMissedReplacements(string $createdFile, array $checkFor = []): void
    {
        $createdFile = $this->getPathHelper()->resolvePath($createdFile);
        self::assertFileExists($createdFile);
        $contents   = file_get_contents($createdFile);
        $checkFor[] = 'template';
        foreach ($checkFor as $check) {
            self::assertNotRegExp(
                '%[^a-z]' . $check . '[^a-z]%i',
                $contents,
                'Found the word "' . $check . '" (case insensitive) in the created file ' . $createdFile
            );
        }
    }

    protected function getPathHelper(): PathHelper
    {
        return $this->container->get(PathHelper::class);
    }

    protected function getTestEntityGeneratorFactory(): TestEntityGeneratorFactory
    {
        return $this->container->get(TestEntityGeneratorFactory::class);
    }

    protected function assertFileContains(string $createdFile, string $needle): void
    {
        $createdFile = $this->getPathHelper()->resolvePath($createdFile);
        self::assertFileExists($createdFile);
        $contents = file_get_contents($createdFile);
        self::assertContains(
            $needle,
            $contents,
            "Missing '$needle' in file '$createdFile'"
        );
    }

    protected function getEntityGenerator(): EntityGenerator
    {
        /**
         * @var EntityGenerator $entityGenerator
         */
        $entityGenerator = $this->container->get(EntityGenerator::class);
        $entityGenerator->setPathToProjectRoot($this->copiedWorkDir ?? static::WORK_DIR)
                        ->setProjectRootNamespace($this->copiedRootNamespace ?? static::TEST_PROJECT_ROOT_NAMESPACE);

        return $entityGenerator;
    }

    protected function getRelationsGenerator(): RelationsGenerator
    {
        /**
         * @var RelationsGenerator $relationsGenerator
         */
        $relationsGenerator = $this->container->get(RelationsGenerator::class);
        $relationsGenerator->setPathToProjectRoot($this->copiedWorkDir ?? static::WORK_DIR)
                           ->setProjectRootNamespace($this->copiedRootNamespace ?? static::TEST_PROJECT_ROOT_NAMESPACE);

        return $relationsGenerator;
    }

    protected function getFieldGenerator(): FieldGenerator
    {
        /**
         * @var FieldGenerator $fieldGenerator
         */
        $fieldGenerator = $this->container->get(FieldGenerator::class);
        $fieldGenerator->setPathToProjectRoot($this->copiedWorkDir ?? static::WORK_DIR)
                       ->setProjectRootNamespace($this->copiedRootNamespace ?? static::TEST_PROJECT_ROOT_NAMESPACE);

        return $fieldGenerator;
    }

    protected function getFieldSetter(): EntityFieldSetter
    {
        $fieldSetter = $this->container->get(EntityFieldSetter::class);
        $fieldSetter->setPathToProjectRoot($this->copiedWorkDir ?? static::WORK_DIR)
                    ->setProjectRootNamespace($this->copiedRootNamespace ?? static::TEST_PROJECT_ROOT_NAMESPACE);

        return $fieldSetter;
    }

    protected function getSchema(): Schema
    {
        return $this->container->get(Schema::class);
    }

    protected function getCodeHelper(): CodeHelper
    {
        return $this->container->get(CodeHelper::class);
    }

    protected function getUnusedRelationsRemover(): UnusedRelationsRemover
    {
        return $this->container->get(UnusedRelationsRemover::class);
    }

    protected function createEntity(string $entityFqn, DataTransferObjectInterface $dto = null): EntityInterface
    {
        return $this->getEntityFactory()->create($entityFqn, $dto);
    }

    protected function getEntityFactory(): EntityFactoryInterface
    {
        $factory = $this->container->get(EntityFactory::class);
        $factory->setEntityManager($this->getEntityManager());

        return $factory;
    }

    protected function getEntityDtoFactory(): DtoFactory
    {
        return $this->container->get(DtoFactory::class);
    }
}
