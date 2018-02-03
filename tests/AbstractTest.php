<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractTest extends TestCase
{
    const WORK_DIR                      = 'override me';
    const CHECKED_OUT_PROJECT_ROOT_PATH = '/tmp/doctrine-static-meta-test-project/';
    const TEST_PROJECT_ROOT_NAMESPACE   = 'DSM\\Test\\Project';
    const TEST_PROJECT_ENTITIES_FOLDER  = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER;

    /**
     * The absolute path to the Entities folder, eg /var/www/vhosts/doctrine-static-meta/var/{testWorkDir}/Entities
     *
     * @var string
     */
    protected $entitiesPath = '';

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
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function setup()
    {
        $this->entitiesPath = static::WORK_DIR
                              .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                              .'/'.static::TEST_PROJECT_ENTITIES_FOLDER;
        $this->getFileSystem()->mkdir($this->entitiesPath);
        $this->entitiesPath                            = realpath($this->entitiesPath);
        $_SERVER[ConfigInterface::PARAM_ENTITIES_PATH] = $this->entitiesPath;
        SimpleEnv::setEnv(Config::getProjectRootDirectory().'/.env');
        $_SERVER[ConfigInterface::PARAM_DB_NAME] .= '_test';
        $this->container                         = new Container();
        $this->container->buildSymfonyContainer($_SERVER);
        $this->container->get(Database::class)->drop(true)->create(true);
        $this->clearWorkDir();
        $this->extendAutoloader();
    }

    /**
     * Accesses the standard Composer autoloader
     *
     * Extends the class with an assertion that a class is found and also providing an entry point for xdebugging
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
                if (in_array(gettype($found), ['boolean', 'NULL'], true)) {
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
            throw new \RuntimeException("You must set a `const WORK_DIR=VAR_PATH.'/folderName/';` in your test class");
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

    protected function assertTemplateCorrect(string $createdFile)
    {
        $this->assertFileExists($createdFile);
        $contents = file_get_contents($createdFile);
        $this->assertNotContains('Template', $contents);
    }

    protected function getEntityGenerator(): EntityGenerator
    {
        /**
         * @var EntityGenerator $entityGenerator
         */
        $entityGenerator = $this->container->get(EntityGenerator::class);
        $entityGenerator->setPathToProjectSrcRoot(static::WORK_DIR)
                        ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
                        ->setEntitiesFolderName(static::TEST_PROJECT_ENTITIES_FOLDER);

        return $entityGenerator;
    }

    protected function getRelationsGenerator(): RelationsGenerator
    {
        /**
         * @var RelationsGenerator $relationsGenerator
         */
        $relationsGenerator = $this->container->get(RelationsGenerator::class);
        $relationsGenerator->setPathToProjectSrcRoot(static::WORK_DIR)
                           ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
                           ->setEntitiesFolderName(static::TEST_PROJECT_ENTITIES_FOLDER);

        return $relationsGenerator;
    }
}
