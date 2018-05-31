<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use PHPUnit\Framework\TestCase;

class RelativePathTraceTraitTest extends TestCase
{
    public function testGetTraceAsStringRelativePath()
    {
        try {
            throw new DoctrineStaticMetaException('oh noes');
        } catch (DoctrineStaticMetaException $e) {
            $expected = '

#0 /vendor/phpunit/phpunit/src/Framework/TestCase.php(1145): EdmondsCommerce\DoctrineStaticMeta\Exception\Traits\RelativePathTraceTraitTest->testGetTraceAsStringRelativePath()
#1 /vendor/phpunit/phpunit/src/Framework/TestCase.php(840): PHPUnit\Framework\TestCase->runTest()
#2 /vendor/phpunit/phpunit/src/Framework/TestResult.php(645): PHPUnit\Framework\TestCase->runBare()
#3 /vendor/phpunit/phpunit/src/Framework/TestCase.php(798): PHPUnit\Framework\TestResult->run(Object(EdmondsCommerce\DoctrineStaticMeta\Exception\Traits\RelativePathTraceTraitTest))
#4 /vendor/phpunit/phpunit/src/Framework/TestSuite.php(776): PHPUnit\Framework\TestCase->run(Object(PHPUnit\Framework\TestResult))
#5 /vendor/phpunit/phpunit/src/TextUI/TestRunner.php(529): PHPUnit\Framework\TestSuite->run(Object(PHPUnit\Framework\TestResult))
#6 /vendor/phpunit/phpunit/src/TextUI/Command.php(198): PHPUnit\TextUI\TestRunner->doRun(Object(PHPUnit\Framework\TestSuite), Array, true)
#7 /vendor/phpunit/phpunit/src/TextUI/Command.php(151): PHPUnit\TextUI\Command->run(Array, true)
#8 /vendor/phpunit/phpunit/phpunit(53): PHPUnit\TextUI\Command::main()
#9 {main}

';
            $actual   = $e->getTraceAsStringRelativePath();
            $this->assertSame($expected, $actual);
        }
    }
}
