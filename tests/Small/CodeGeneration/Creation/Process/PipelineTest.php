<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Pipeline;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use PHPUnit\Framework\TestCase;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Pipeline
 * @small
 */
class PipelineTest extends TestCase
{

    /**
     * @test
     */
    public function itCanRegisterANewProcess(): void
    {
        $pipeline = $this->getPipeline();
        $process  = new ReplaceNameProcess();
        $pipeline->register($process);
        $processes = $this->getRegisteredProcesses($pipeline);
        self::assertSame([$process], $processes);
    }

    private function getPipeline(): Pipeline
    {
        return new Pipeline(new FindReplaceFactory());
    }

    private function getRegisteredProcesses(Pipeline $pipeline): array
    {
        $reflection = new ReflectionClass(\get_class($pipeline));
        $property   = $reflection->getProperty('processes');
        $property->setAccessible(true);

        return $property->getValue($pipeline);
    }

    /**
     * @test
     */
    public function itCanRunAProcessPipelineOnAFile(): void
    {
        $file = new File();
        $file->setContents('blah');
        $process = new ReplaceNameProcess();
        $process->setArgs('blah', 'foo');
        $this->getPipeline()->register($process)->run($file);
        $expected = 'foo';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }
}
