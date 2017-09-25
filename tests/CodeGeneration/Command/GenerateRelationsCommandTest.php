<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class GenerateRelationsCommandTest extends TestCase
{

    const WORK_DIR = __DIR__.'/../../../var/GenerateRelationsCommandTest';

    protected $fs;

    public function setUp()
    {
        if (!is_dir(self::WORK_DIR)) {
            mkdir(self::WORK_DIR, 0777, true);
        }
        $this->emptyDirectory(self::WORK_DIR);
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

    public function testGenerateRelationsForExample()
    {
        $src = __DIR__.'/../../../example';
        $dest = self::WORK_DIR;
        $this->getFileSystem()->mirror($src, $dest);
        $this->emptyDirectory(self::WORK_DIR.'/ExampleEntities/Traits');
        $_SERVER['dbEntitiesPath'] = self::WORK_DIR.'/ExampleEntities';
        $application = new Application();
        $helperSet = require __DIR__.'/../../../cli-config.php';
        $application->setHelperSet($helperSet);
        $command = new GenerateRelationsCommand();
        $application->add($command);
        $tester = new CommandTester($command);
        $tester->execute(
            [
                GenerateRelationsCommand::ARG_PATH => self::WORK_DIR.'/ExampleEntities',
            ]
        );
    }
}
