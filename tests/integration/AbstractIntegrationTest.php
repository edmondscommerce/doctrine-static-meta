<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use Doctrine\ORM\EntityManagerInterface;
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
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\PHPQA\Constants;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AbstractTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractIntegrationTest extends TestCase
{
    public const TEST_TYPE                   = 'integration';
    public const VAR_PATH                    = __DIR__ . '/../../var/testOutput/';
    public const WORK_DIR                    = 'override me';
    public const TEST_PROJECT_ROOT_NAMESPACE = 'My\\IntegrationTest\\Project';

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
     * Prepare working directory, ensure its empty, create entities folder and set up env variables
     *
     * The order of these actions is critical
     */
    public function setup()
    {
        if (false !== stripos(static::WORK_DIR, self::WORK_DIR)) {
            throw new \RuntimeException(
                "You must set a `public const WORK_DIR=AbstractTest::VAR_PATH.'/'"
                . ".self::TEST_TYPE.'/folderName/';` in your test class"
            );
        }
        if (false === strpos(static::WORK_DIR, static::TEST_TYPE)) {
            throw new \RuntimeException(
                'Your WORK_DIR is missing the test type, should look like: '
                . "`public const WORK_DIR=AbstractTest::VAR_PATH.'/'"
                . ".self::TEST_TYPE.'/folderName/';` in your test class"
            );
        }
        $this->copiedWorkDir       = null;
        $this->copiedRootNamespace = null;
        $this->entitiesPath        = static::WORK_DIR
                                     . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                     . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME;
        $this->getFileSystem()->mkdir($this->entitiesPath);
        $this->entitiesPath        = realpath($this->entitiesPath);
        $this->entityRelationsPath = static::WORK_DIR
                                     . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                     . '/' . AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME;
        $this->getFileSystem()->mkdir($this->entityRelationsPath);
        $this->entityRelationsPath = realpath($this->entityRelationsPath);
        $this->setupContainer($this->entitiesPath);
        $this->clearWorkDir();
        $this->extendAutoloader(
            static::TEST_PROJECT_ROOT_NAMESPACE . '\\',
            static::WORK_DIR . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
        );
    }

    protected function getFileSystem(): Filesystem
    {
        if (null === $this->filesystem) {
            $this->filesystem = (null !== $this->container)
                ? $this->container->get(Filesystem::class)
                : new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * @param string $entitiesPath
     *
     * @throws Exception\ConfigException
     * @throws Exception\DoctrineStaticMetaException
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
    }

    protected function clearWorkDir(): void
    {
        $this->getFileSystem()->mkdir(static::WORK_DIR);
        $this->emptyDirectory(static::WORK_DIR);
        if (empty($this->entitiesPath)) {
            throw new \RuntimeException('$this->entitiesPath path is empty');
        }
        $this->getFileSystem()->mkdir($this->entitiesPath);
    }

    protected function emptyDirectory(string $path): void
    {
        $fileSystem = $this->getFileSystem();
        $fileSystem->remove($path);
        $fileSystem->mkdir($path);
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
     * @throws \ReflectionException
     */
    protected function extendAutoloader(string $namespace, string $path): void
    {
        //Unregister any previously set extension first
        $registered = \spl_autoload_functions();
        foreach ($registered as $loader) {
            if (\is_callable($loader)) {
                continue;
            }
            if ((new  \ts\Reflection\ReflectionClass($loader[0]))->isAnonymous()) {
                \spl_autoload_unregister($loader);
            }
        }
        //Then build a new extension and register it
        $namespace  = rtrim($namespace, '\\') . '\\';
        $testLoader = new class($namespace) extends ClassLoader
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
                if (\in_array(gettype($found), ['boolean', 'NULL'], true)) {
                    //good spot to set a break point ;)
                    return false;
                }

                return true;
            }
        };
        $testLoader->addPsr4($namespace, $path, true);
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
     * @throws Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function qaGeneratedCode(?string $namespaceRoot = null): bool
    {
        if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
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
     * If PHP loads any files whilst generating, then subsequent changes to those files will not have any effect
     *
     * To resolve this, we need to clone the copied code into a new namespace before running it
     *
     * We only allow copying to a new work dir once per test run, different extras must be used
     *
     * @return string $copiedWorkDir
     * @throws \ReflectionException
     * @throws Exception\DoctrineStaticMetaException
     */
    protected function setupCopiedWorkDir(): string
    {
        $copiedNamespaceRoot       = $this->getCopiedNamespaceRoot();
        $this->copiedWorkDir       = rtrim(static::WORK_DIR, '/') . 'Copies/' . $copiedNamespaceRoot . '/';
        $this->copiedRootNamespace = $copiedNamespaceRoot;
        if (is_dir($this->copiedWorkDir)) {
            throw new \RuntimeException(
                'The Copied WorkDir ' . $this->copiedWorkDir . ' Already Exists'
            );
        }
        $this->filesystem->mkdir($this->copiedWorkDir);
        $this->filesystem->mirror(static::WORK_DIR, $this->copiedWorkDir);
//        $nsRoot   = rtrim(
//            str_replace(
//                '\\\\',
//                '\\',
//                \substr(
//                    static::TEST_PROJECT_ROOT_NAMESPACE,
//                    0,
//                    strpos(static::TEST_PROJECT_ROOT_NAMESPACE, '\\')
//                )
//            ),
//            '\\'
//        );
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->copiedWorkDir));

        foreach ($iterator as $info) {
            /**
             * @var \SplFileInfo $info
             */
            if (false === $info->isFile()) {
                continue;
            }
            $contents = file_get_contents($info->getPathname());

            $updated = \preg_replace(
                '%(use|namespace)\s+?'
                . $this->container->get(FindAndReplaceHelper::class)
                                  ->escapeSlashesForRegex(static::TEST_PROJECT_ROOT_NAMESPACE)
                . '\\\\%',
                '$1 ' . $copiedNamespaceRoot . '\\',
                $contents
            );
            file_put_contents($info->getPathname(), $updated);
        }
        $this->extendAutoloader(
            $this->copiedRootNamespace . '\\',
            $this->copiedWorkDir . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
        );

        return $this->copiedWorkDir;
    }

    /**
     * Get the namespace root to use in a copied work dir
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getCopiedNamespaceRoot(): string
    {
        return (new  \ts\Reflection\ReflectionClass(static::class))->getShortName() . '_' . $this->getName() . '_';
    }

    /**
     * When working with a copied work dir, use this function to translate the FQN of any Entities etc
     *
     * @param string $fqn
     *
     * @return string
     * @throws Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    protected function getCopiedFqn(string $fqn): string
    {
        $copiedNamespaceRoot = $this->getCopiedNamespaceRoot();

        return $this->container
            ->get(NamespaceHelper::class)
            ->tidy('\\' . $copiedNamespaceRoot . '\\'
                   . ltrim(
                       \str_replace(static::TEST_PROJECT_ROOT_NAMESPACE, '', $fqn),
                       '\\'
                   ));
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
        return $this->container->get(EntityEmbeddableSetter::class);
    }

    /**
     * @return ArchetypeEmbeddableGenerator
     * @throws Exception\DoctrineStaticMetaException
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
        $entityGenerator->setPathToProjectRoot(static::WORK_DIR)
                        ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE);

        return $entityGenerator;
    }

    protected function getRelationsGenerator(): RelationsGenerator
    {
        /**
         * @var RelationsGenerator $relationsGenerator
         */
        $relationsGenerator = $this->container->get(RelationsGenerator::class);
        $relationsGenerator->setPathToProjectRoot(static::WORK_DIR)
                           ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE);

        return $relationsGenerator;
    }

    protected function getFieldGenerator(): FieldGenerator
    {
        /**
         * @var \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator $fieldGenerator
         */
        $fieldGenerator = $this->container->get(FieldGenerator::class);
        $fieldGenerator->setPathToProjectRoot(static::WORK_DIR)
                       ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE);

        return $fieldGenerator;
    }

    protected function getFieldSetter(): EntityFieldSetter
    {
        $fieldSetter = $this->container->get(EntityFieldSetter::class);
        $fieldSetter->setPathToProjectRoot(static::WORK_DIR);

        return $fieldSetter;
    }

    protected function isQuickTests(): bool
    {
        if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return EntityManagerInterface
     * @throws Exception\DoctrineStaticMetaException
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->container->get(EntityManagerInterface::class);
    }

    protected function getSchema(): Schema
    {
        return $this->container->get(Schema::class);
    }

    protected function getCodeHelper(): CodeHelper
    {
        return $this->container->get(CodeHelper::class);
    }

    /**
     * Deliberately not type hinting the return type as it makes PHPStan upset when working with test entities
     *
     * @param string $entityFqn
     *
     * @return EntityInterface
     * @throws Exception\DoctrineStaticMetaException
     */
    protected function createEntity(string $entityFqn): EntityInterface
    {
        return $this->container->get(EntityFactory::class)->create($entityFqn);
    }
}
