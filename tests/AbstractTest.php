<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
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
abstract class AbstractTest extends TestCase
{
    public const VAR_PATH                    = __DIR__.'/../var';
    public const WORK_DIR                    = 'override me';
    public const TEST_PROJECT_ROOT_NAMESPACE = 'DSM\\Test\\Project';

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
        $this->setupContainer();
        $this->clearWorkDir();
        $this->extendAutoloader();
    }

    /**
     * @throws Exception\ConfigException
     * @throws Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setupContainer()
    {
        SimpleEnv::setEnv(Config::getProjectRootDirectory().'/.env');
        $testConfig                                       = $_SERVER;
        $testConfig[ConfigInterface::PARAM_ENTITIES_PATH] = $this->entitiesPath;
        $testConfig[ConfigInterface::PARAM_DB_NAME]       .= '_test';
        $testConfig[ConfigInterface::PARAM_DEVMODE]       = true;
        $this->container                                  = new Container();
        $this->container->buildSymfonyContainer($testConfig);
        $this->container->get(Database::class)->drop(true)->create(true);
    }


    /**
     * Accesses the standard Composer autoloader
     *
     * Extends the class and also providing an entry point for xdebugging
     *
     * Extends this with a PSR4 root for our WORK_DIR
     **/
    protected function extendAutoloader()
    {
        $loader = new class extends ClassLoader
        {
            public function loadClass($class)
            {
                if (false === strpos($class, AbstractTest::TEST_PROJECT_ROOT_NAMESPACE)) {
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
        $loader->addPsr4(
            static::TEST_PROJECT_ROOT_NAMESPACE.'\\',
            static::WORK_DIR.'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
        );
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
        $this->assertFileExists($createdFile);
        $contents = file_get_contents($createdFile);
        $this->assertNotContains(
            'template',
            $contents,
            'Found the word "template" (case insensitive) in the created file '.$createdFile,
            true
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
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function qaGeneratedCode(): void
    {
        if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            return;
        }
        //lint
        $path       = static::WORK_DIR;
        $exclude    = ['vendor'];
        $extensions = ['php'];

        $linter  = new Linter($path, $exclude, $extensions);
        $lint    = $linter->lint([], false);
        $message = str_replace($path, '', print_r($lint, true));
        $this->assertEmpty($lint, "\n\nPHP Syntax Errors in $path\n\n$message\n\n");
    }
}
