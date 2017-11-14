<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\GeneratedCode;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractGeneratedCodeTest extends TestCase
{
    const WORK_DIR = __DIR__.'/../../var/CodeGenerationTest';

    const TEST_PROJECT_ROOT_NAMESPACE = 'DSM\\Test\\Project';
    const TEST_PROJECT_ENTITIES_NAMESPACE = AbstractCommand::DEFAULT_ENTITIES_ROOT_NAMESPACE;

    protected $fs;

    protected $entitiesPath = '';

    public function setUp()
    {
        $this->getFileSystem()->mkdir(self::WORK_DIR);
        $this->emptyDirectory(self::WORK_DIR);
        $this->entitiesPath        = self::WORK_DIR.'/'.self::TEST_PROJECT_ENTITIES_NAMESPACE;
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
