<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;

class FileCreationTransactionIntegrationTest extends AbstractIntegrationTest
{
    public const WORK_DIR    = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/FileCreationTransactionTest/';
    public const TEST_PATH_1 = self::WORK_DIR.'1.txt';
    public const TEST_PATH_2 = self::WORK_DIR.'2.txt';

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testCanAddFile()
    {
        $this->assertCount(2, FileCreationTransaction::getTransaction());
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testPathsDeduplicated()
    {
        FileCreationTransaction::setPathCreated(self::TEST_PATH_1);
        $this->assertCount(2, FileCreationTransaction::getTransaction());
    }

    /**
     */
    public function testCanEchoFindCommands()
    {
        $output = $this->getFindCommands();
        $this->assertNotEmpty($output);
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getFindCommands(): string
    {
        $handle = fopen('php://memory', 'rwb');
        FileCreationTransaction::echoDirtyTransactionCleanupCommands($handle);
        rewind($handle);
        $output = stream_get_contents($handle);
        fclose($handle);

        return (string)$output;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testMarkSuccessfulClearsTransaction()
    {
        FileCreationTransaction::markTransactionSuccessful();
        $output = $this->getFindCommands();
        $this->assertEmpty($output);
    }
}
