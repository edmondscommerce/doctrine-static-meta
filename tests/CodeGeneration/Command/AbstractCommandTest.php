<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractCommandTest extends TestCase
{
    const WORK_DIR = __DIR__.'/../../../var/GenerateRelationsCommandTest';

    const TEST_PROJECT_ROOT_NAMESPACE = 'DSM\\Test\\Project';
    const TEST_PROJECT_ENTITIES_NAMESPACE = AbstractCommand::DEFAULT_ENTITIES_ROOT_NAMESPACE;

    protected $fs;

    protected $entitiesPath = '';

    public function setUp()
    {
        if (!is_dir(self::WORK_DIR)) {
            mkdir(self::WORK_DIR, 0777, true);
        }
        $this->emptyDirectory(self::WORK_DIR);
        $this->entitiesPath = self::WORK_DIR.'/'.self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $_SERVER['dbEntitiesPath'] = $this->entitiesPath;
        $this->getFileSystem()->mkdir($this->entitiesPath);
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
}