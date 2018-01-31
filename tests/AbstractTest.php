<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\SchemaBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractTest extends TestCase
{
    const WORK_DIR                      = 'override me';
    const CHECKED_OUT_PROJECT_ROOT_PATH = '/tmp/doctrine-static-meta-test-project/';
    const TEST_PROJECT_ROOT_NAMESPACE   = 'DSM\\Test\\Project';
    const TEST_PROJECT_ENTITIES_FOLDER  = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER;
    const TEST_NAMESPACE                = self::TEST_PROJECT_ROOT_NAMESPACE;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The absolute path to the Entities folder, eg /var/www/vhosts/doctrine-static-meta/var/{testWorkDir}/Entities
     *
     * @var string
     */
    protected $entitiesPath = '';

    /**
     * Prepare working directory, ensure its empty, create entities folder and set up env variables
     */
    public function setup()
    {
        $this->clearWorkDir();
        $this->entitiesPath                          = static::WORK_DIR
            . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
            . '/' . static::TEST_PROJECT_ENTITIES_FOLDER;
        $this->getFileSystem()->mkdir($this->entitiesPath);
        $this->entitiesPath = realpath($this->entitiesPath);
        $_SERVER[ConfigInterface::paramEntitiesPath] = $this->entitiesPath;
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
                if (!$found) {
                    //good spot to set a break point ;)
                    return false;
                }
                return true;
            }
        };
        $loader->addPsr4(static::TEST_PROJECT_ROOT_NAMESPACE . '\\', static::WORK_DIR . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER);
        $loader->register();
    }

    protected function getTestEntityManager(bool $dropDb = true): EntityManager
    {
        SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
        $server                               = $_SERVER;
        $testClassName                        = (new \ReflectionClass($this))->getShortName();
        $server[ConfigInterface::paramDbName] .= '_' . strtolower($testClassName) . '_test';
        $config                               = new Config($server);
        $database                             = new Database($config);
        if ($dropDb) {
            $database->drop(true);
        }
        $database->create(true);
        return DevEntityManagerFactory::getEm($config, false);
    }

    protected function assertCanBuildSchema(EntityManager $entityManager)
    {
        $schemaBuilder = new SchemaBuilder($entityManager);
        $schemaBuilder->createTables();
        $this->assertTrue(true, 'Failed building schema');
    }

    protected function clearWorkDir()
    {
        if (static::WORK_DIR === self::WORK_DIR) {
            throw new \RuntimeException("You must set a `const WORK_DIR=VAR_PATH.'/folderName/';` in your test class");
        }
        $this->getFileSystem()->mkdir(static::WORK_DIR);
        $this->emptyDirectory(static::WORK_DIR);
    }

    protected function getFileSystem(): Filesystem
    {
        if (null === $this->filesystem) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    protected function emptyDirectory(string $path)
    {
        $fs = $this->getFileSystem();
        $fs->remove($path);
        $fs->mkdir($path);
    }

    protected function assertTemplateCorrect(string $createdFile)
    {
        $this->assertFileExists($createdFile);
        $contents = file_get_contents($createdFile);
        $this->assertNotContains('Template', $contents);
    }
}
