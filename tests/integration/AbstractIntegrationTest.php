<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\GeneratedCode\GeneratedCodeTest;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\PHPQA\Constants;
use Overtrue\PHPLint\Linter;
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
    public const TEST_TYPE                   = 'intergation';
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
     * Prepare working directory, ensure its empty, create entities folder and set up env variables
     *
     * The order of these actions is critical
     */
    public function setup()
    {
        $this->entitiesPath = static::WORK_DIR
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
     * @param string $extra
     */
    protected function setupCopiedWorkDir(string $extra = 'Copied'): void
    {
        $copiedWorkDir = rtrim(static::WORK_DIR, '/').$extra.'/';
        if (is_dir($copiedWorkDir)) {
            exec('rm -rf '.$copiedWorkDir);
        }
        $this->filesystem->mkdir($copiedWorkDir);
        $this->filesystem->mirror(static::WORK_DIR, $copiedWorkDir);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($copiedWorkDir));
        foreach ($iterator as $info) {
            /**
             * @var \SplFileInfo $info
             */
            if (false === $info->isFile()) {
                continue;
            }
            $contents        = file_get_contents($info->getPathname());
            $copiedNameSpace = $extra.static::TEST_PROJECT_ROOT_NAMESPACE;
            $updated         = \str_replace(
                [
                    'namespace '.static::TEST_PROJECT_ROOT_NAMESPACE,
                    'use '.static::TEST_PROJECT_ROOT_NAMESPACE,
                ],
                [
                    'namespace '.$copiedNameSpace,
                    'use '.$copiedNameSpace,
                ],
                $contents
            );
            file_put_contents($info->getPathname(), $updated);
        }
        $this->extendAutoloader(
            $extra.static::TEST_PROJECT_ROOT_NAMESPACE.'\\',
            $copiedWorkDir.'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
        );
    }

    /**
     * When working with a copied work dir, use this function to translate the FQN of any Entities etc
     *
     * @param string $fqn
     * @param string $extra
     *
     * @return string
     * @throws Exception\DoctrineStaticMetaException
     */
    protected function getCopiedFqn(string $fqn, string $extra = 'Copied'): string
    {
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
        if (static::WORK_DIR === self::WORK_DIR) {
            throw new \RuntimeException(
                "You must set a `const WORK_DIR=AbstractTest::VAR_PATH.'/folderName/';` in your test class"
            );
        }
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
        $createdFile = $this->container->get(CodeHelper::class)->resolvePath($createdFile);
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
        $createdFile = $this->container->get(CodeHelper::class)->resolvePath($createdFile);
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
     * @return EntityManagerInterface
     * @throws Exception\DoctrineStaticMetaException
     */
    protected function getEntityManager(): EntityManagerInterface
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
     */
    public function qaGeneratedCode(?string $namespaceRoot = null): bool
    {
        if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            return true;
        }
        //lint
        $path       = static::WORK_DIR;
        $exclude    = ['vendor'];
        $extensions = ['php'];

        $linter  = new Linter($path, $exclude, $extensions);
        $lint    = $linter->lint([], false);
        $message = str_replace($path, '', print_r($lint, true));
        $this->assertEmpty($lint, "\n\nPHP Syntax Errors in $path\n\n$message\n\n");
        $namespaceRoot     = ltrim($namespaceRoot ?? static::TEST_PROJECT_ROOT_NAMESPACE, '\\');
        $phpstanNamespace  = $namespaceRoot.'\\\\';
        $phpstanFolder     = static::WORK_DIR.'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER;
        $phpstanAutoLoader = '<?php declare(strict_types=1);
require __DIR__."/../../../vendor/autoload.php";

use Composer\Autoload\ClassLoader;

$loader = new class extends ClassLoader
        {
            public function loadClass($class)
            {
                if (false === strpos($class, "'.$namespaceRoot.'")) {
                    return false;
                }
                $found = parent::loadClass($class);
                if (\in_array(gettype($found), [\'boolean\', \'NULL\'], true)) {
                    //good spot to set a break point ;)
                    return false;
                }

                return true;
            }
        };
        $loader->addPsr4(
            "'.$phpstanNamespace.'","'.$phpstanFolder.'"
        );
        $loader->register();
';
        file_put_contents(static::WORK_DIR.'/phpstan-autoloader.php', $phpstanAutoLoader);
        // A hunch that travis is not liking the no xdebug command
        $phpstanCommand = FullProjectBuildIntegrationTest::BASH_PHPNOXDEBUG_FUNCTION
                          ."\n\nphpNoXdebug bin/phpstan.phar analyse $path/src -l7 -a "
                          .static::WORK_DIR.'/phpstan-autoloader.php 2>&1';
        if ($this->isTravis()) {
            $phpstanCommand = "bin/phpstan.phar analyse $path/src -l7 -a "
                              .static::WORK_DIR.'/phpstan-autoloader.php 2>&1';
        }
        exec(
            $phpstanCommand,
            $output,
            $exitCode
        );
        if (0 !== $exitCode) {
            $this->fail('PHPStan errors found in generated code at '.$path
                        .':'."\n\n".implode("\n", $output));
        }

        return true;
    }

    protected function getSchema(): Schema
    {
        return $this->container->get(Schema::class);
    }
}
