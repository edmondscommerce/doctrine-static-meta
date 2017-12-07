<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractTest extends TestCase
{
    const WORK_DIR = 'override me';
    const TEST_PROJECT_ROOT_NAMESPACE = 'DSM\\Test\\Project';
    const TEST_PROJECT_ENTITIES_NAMESPACE = AbstractCommand::DEFAULT_ENTITIES_ROOT_NAMESPACE;
    const TEST_NAMESPACE = self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\' . self::TEST_PROJECT_ROOT_NAMESPACE;

    protected $fs;

    protected $entitiesPath = '';

    /**
     * Prepare working directory, ensure its empty, create entities folder and set up env variables
     */
    public function setup()
    {
        $this->getFileSystem()->mkdir(static::WORK_DIR);
        $this->emptyDirectory(static::WORK_DIR);
        $this->entitiesPath = static::WORK_DIR
            . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
            . '/' . static::TEST_PROJECT_ENTITIES_NAMESPACE;
        $_SERVER['dbEntitiesPath'] = $this->entitiesPath;
        $this->getFileSystem()->mkdir($this->entitiesPath);
        $this->entitiesPath = realpath($this->entitiesPath);
    }

    protected function getFileSystem(): Filesystem
    {
        if (null === $this->fs) {
            $this->fs = new Filesystem();
        }

        return $this->fs;
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
