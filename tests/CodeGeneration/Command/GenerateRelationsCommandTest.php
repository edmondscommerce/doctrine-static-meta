<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateRelationsCommandTest extends TestCase
{

    const WORK_DIR = __DIR__.'/../../../var/'.__CLASS__;

    public function setUp()
    {
        if (!is_dir(self::WORK_DIR)) {
            mkdir(self::WORK_DIR, 0777, true);
        }
        $this->emptyDirectory(self::WORK_DIR);
    }

    protected function emptyDirectory(string $path)
    {
        array_map(
            function ($in) {
                if (is_dir($in)) {
                    $this->emptyDirectory($in);
                } else {
                    array_map('unlink', glob("$in/*"));
                }
            },
            glob($path.'/*')
        );
    }

    public function testGenerateRelationsForExample()
    {
        $src = __DIR__.'/../../../example';
        $dest = self::WORK_DIR;
        exec("cp -r $src $dest", $output, $exitCode);
        if ($exitCode) {
            throw new \Exception('Failed copying files: '.implode("\n", $output));
        }
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
