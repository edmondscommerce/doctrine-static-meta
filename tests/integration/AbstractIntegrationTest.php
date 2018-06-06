<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\PHPQA\Constants;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AbstractTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractIntegrationTest extends TestCase
{
    public const TEST_TYPE                   = 'integration';
    public const VAR_PATH                    = __DIR__.'/../../var/testOutput/';
    public const WORK_DIR                    = 'override me';
    public const TEST_PROJECT_ROOT_NAMESPACE = 'My\\Test\\Project';

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
                .".self::TEST_TYPE.'/folderName/';` in your test class"
            );
        }
        if (false === strpos(static::WORK_DIR, static::TEST_TYPE)) {
            throw new \RuntimeException(
                'Your WORK_DIR is missing the test type, should look like: '
                ."`public const WORK_DIR=AbstractTest::VAR_PATH.'/'"
                .".self::TEST_TYPE.'/folderName/';` in your test class"
            );
        }
        $this->copiedWorkDir = null;
        $this->entitiesPath  = static::WORK_DIR
                               .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                               .'/'.AbstractGenerator::ENTITIES_FOLDER_NAME;
        $this->getFileSystem()->mkdir($this->entitiesPath);
        $this->entitiesPath        = realpath($this->entitiesPath);
        $this->entityRelationsPath = static::WORK_DIR
                                     .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                     .'/'.AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME;
        $this->getFileSystem()->mkdir($this->entityRelationsPath);
        $this->entityRelationsPath = realpath($this->entityRelationsPath);
        $this->setupContainer($this->entitiesPath);
        $this->clearWorkDir();
        $this->extendAutoloader(
            static::TEST_PROJECT_ROOT_NAMESPACE.'\\',
            static::WORK_DIR.'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
        );
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
     */
    protected function setupCopiedWorkDir(): string
    {
        $extra                     = $this->getCopiedExtra();
        $this->copiedWorkDir       = rtrim(static::WORK_DIR, '/').'Copies/'.$extra.'/';
        $this->copiedRootNamespace = $extra.static::TEST_PROJECT_ROOT_NAMESPACE;
        if (is_dir($this->copiedWorkDir)) {
            throw new \RuntimeException(
                'The Copied WorkDir '.$this->copiedWorkDir
                .' Already Exists, please choose a different $extra than '.$extra
            );
        }
        $this->filesystem->mkdir($this->copiedWorkDir);
        $this->filesystem->mirror(static::WORK_DIR, $this->copiedWorkDir);
        $nsRoot   = rtrim(
            str_replace(
                '\\\\',
                '\\',
                \substr(
                    static::TEST_PROJECT_ROOT_NAMESPACE,
                    0,
                    strpos(static::TEST_PROJECT_ROOT_NAMESPACE, '\\')
                )
            ),
            '\\'
        );
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
                '%(use|namespace)\s+?'.$nsRoot.'\\\\%',
                '$1 '.$extra.$nsRoot.'\\',
                $contents
            );
            file_put_contents($info->getPathname(), $updated);
        }
        $this->extendAutoloader(
            $this->copiedRootNamespace.'\\',
            $this->copiedWorkDir.'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
        );

        return $this->copiedWorkDir;
    }

    /**
     * Get the extra bit we add to the copied work dir, based on the current class name and hte current test name
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getCopiedExtra(): string
    {
        return (new \ReflectionClass($this))->getShortName().'_'.$this->getName().'_';
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
        $extra = $this->getCopiedExtra();

        return $this->container
            ->get(NamespaceHelper::class)
            ->tidy('\\'.$extra.ltrim($fqn, '\\'));
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function isTravis(): bool
    {
        return isset($_SERVER['TRAVIS']);
    }

    /**
     * @param string $entitiesPath
     *
     * @throws Exception\ConfigException
     * @throws Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setupContainer(string $entitiesPath)
    {
        SimpleEnv::setEnv(Config::getProjectRootDirectory().'/.env');
        $testConfig                                       = $_SERVER;
        $testConfig[ConfigInterface::PARAM_ENTITIES_PATH] = $entitiesPath;
        $testConfig[ConfigInterface::PARAM_DB_NAME]       .= '_test';
        $testConfig[ConfigInterface::PARAM_DEVMODE]       = true;
        $this->container                                  = new Container();
        $this->container->buildSymfonyContainer($testConfig);
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
     */
    protected function extendAutoloader(string $namespace, string $path)
    {
        $namespace = rtrim($namespace, '\\').'\\';
        $loader    = new class($namespace) extends ClassLoader
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
        $loader->addPsr4($namespace, $path);
        $loader->register();
    }

    protected function clearWorkDir()
    {
        $this->getFileSystem()->mkdir(static::WORK_DIR);
        $this->emptyDirectory(static::WORK_DIR);
        if (empty($this->entitiesPath)) {
            throw new \RuntimeException('$this->entitiesPath path is empty');
        }
        $this->getFileSystem()->mkdir($this->entitiesPath);
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

    protected function emptyDirectory(string $path)
    {
        $fileSystem = $this->getFileSystem();
        $fileSystem->remove($path);
        $fileSystem->mkdir($path);
    }

    protected function assertNoMissedReplacements(string $createdFile)
    {
        $createdFile = $this->getCodeHelper()->resolvePath($createdFile);
        $this->assertFileExists($createdFile);
        $contents = file_get_contents($createdFile);
        $this->assertNotContains(
            'template',
            $contents,
            'Found the word "template" (case insensitive) in the created file '.$createdFile,
            true
        );
    }

    protected function assertFileContains(string $createdFile, string $needle)
    {
        $createdFile = $this->getCodeHelper()->resolvePath($createdFile);
        $this->assertFileExists($createdFile);
        $contents = file_get_contents($createdFile);
        $this->assertContains(
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
         * @var FieldGenerator $fieldGenerator
         */
        $fieldGenerator = $this->container->get(FieldGenerator::class);
        $fieldGenerator->setPathToProjectRoot(static::WORK_DIR)
                       ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE);

        return $fieldGenerator;
    }

    /**
     * @return EntityManager
     * @throws Exception\DoctrineStaticMetaException
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->container->get(EntityManagerInterface::class);
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
        $namespaceRoot = ltrim($namespaceRoot ?? static::TEST_PROJECT_ROOT_NAMESPACE, '\\');
        if (null !== $this->copiedWorkDir) {
            $workDir       = $this->copiedWorkDir;
            $namespaceRoot = ltrim($this->getCopiedFqn(static::TEST_PROJECT_ROOT_NAMESPACE), '\\');
        }
        static $codeValidator;
        if (null === $codeValidator) {
            $codeValidator = new CodeValidator();
        }
        $errors = $codeValidator($workDir, $namespaceRoot);
        $this->assertNull($errors);

        return true;
    }

    protected function getSchema(): Schema
    {
        return $this->container->get(Schema::class);
    }

    protected function getCodeHelper(): CodeHelper
    {
        return $this->container->get(CodeHelper::class);
    }
}
