<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceProjectRootNamespaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\FindReplace;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceProjectRootNamespaceProcess
 * @small
 */
class ReplaceProjectRootNamespaceProcessTest extends TestCase
{
    /**
     * @test
     */
    public function itCanReplaceTheProjectRootNamespace()
    {
        $file = new File();
        $file->setContents(
            'namespace ' . ReplaceProjectRootNamespaceProcess::FIND_NAMESPACE . 'Blah;
        use ' . ReplaceProjectRootNamespaceProcess::FIND_NAMESPACE . 'Something'
        );
        $rootNamespace = 'My\\Test\\Project\\';
        $this->getProcess()->setProjectRootNamespace($rootNamespace)->run(new FindReplace($file));
        $expected = 'namespace ' . $rootNamespace . 'Blah;
        use ' . $rootNamespace . 'Something';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getProcess(): ReplaceProjectRootNamespaceProcess
    {
        return new ReplaceProjectRootNamespaceProcess();
    }

    /**
     * @test
     */
    public function throwsAnExceptionIfNoNamespaceSet()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must set the project root namespace ');
        $this->getProcess()->run(new FindReplace(new File()));
    }
}