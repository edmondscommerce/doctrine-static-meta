<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\FindReplace;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess
 * @small
 */
class ReplaceNameProcessTest extends TestCase
{
    /**
     * @test
     */
    public function itCanReplaceAName(): void
    {
        $file = new File();
        $file->setContents('Blah blah blahs Blahs BLAH BLAHS');
        $process = $this->getReplaceNameProcess();
        $process->setArgs('blah', 'foo');
        $process->run(new FindReplace($file));
        $expected = 'Foo foo foos Foos FOO FOOS';
        self::assertSame($expected, $file->getContents());
    }

    private function getReplaceNameProcess(): ReplaceNameProcess
    {
        return new ReplaceNameProcess();
    }

    /**
     * @test
     */
    public function itForcesAnEssOnTheWordSheep(): void
    {
        $file = new File();
        $file->setContents('Blah blah blahs Blahs BLAH BLAHS');
        $process = $this->getReplaceNameProcess();
        $process->setArgs('blah', 'sheep');
        $process->run(new FindReplace($file));
        $expected = 'Sheep sheep sheeps Sheeps SHEEP SHEEPS';
        self::assertSame($expected, $file->getContents());
    }
}
