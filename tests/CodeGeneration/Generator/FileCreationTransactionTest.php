<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;

class FileCreationTransactionTest extends AbstractTest
{
    const WORK_DIR    = VAR_PATH . '/FileCreationTransactionTest/';
    const TEST_PATH_1 = self::WORK_DIR . '1.txt';
    const TEST_PATH_2 = self::WORK_DIR . '2.txt';

    public function setup()
    {
        parent::setup();
        FileCreationTransaction::markTransactionSuccessful();
        foreach ([self::TEST_PATH_1, self::TEST_PATH_2] as $path) {
            file_put_contents($path, $path);
            FileCreationTransaction::setPathCreated($path);
        }
    }

    /**
     * @depends ContainerTest::testLoadServices
     */
    public function testCanAddFile()
    {
        $this->assertEquals(2, count(FileCreationTransaction::getTransaction()));
    }

    /**
     * @depends ContainerTest::testLoadServices
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function testPathsDeduplicated()
    {
        FileCreationTransaction::setPathCreated(self::TEST_PATH_1);
        $this->assertEquals(2, count(FileCreationTransaction::getTransaction()));
    }

    /**
     * @depends ContainerTest::testLoadServices
     */
    public function testCanEchoFindCommands()
    {
        $output = $this->getFindCommands();
        $this->assertNotEmpty($output);
    }

    /**
     * @depends ContainerTest::testLoadServices
     * @return string
     */
    protected function getFindCommands(): string
    {
        $handle = fopen('php://memory', 'rw');
        FileCreationTransaction::echoDirtyTransactionCleanupCommands($handle);
        rewind($handle);
        $output = stream_get_contents($handle);
        fclose($handle);
        return strval($output);
    }

    /**
     * @depends ContainerTest::testLoadServices
     */
    public function testMarkSuccessfulClearsTransaction()
    {
        FileCreationTransaction::markTransactionSuccessful();
        $output = $this->getFindCommands();
        $this->assertEmpty($output);
    }
}
