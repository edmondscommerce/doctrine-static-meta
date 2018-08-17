<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction
 */
class FileCreationTransactionTest extends AbstractTest
{
    public const WORK_DIR    = AbstractTest::VAR_PATH .
                               '/' .
                               self::TEST_TYPE .
                               '/FileCreationTransactionTest/';
    public const TEST_PATH_1 = self::WORK_DIR . '1.txt';
    public const TEST_PATH_2 = self::WORK_DIR . '2.txt';

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
     * @test
     * @medium
     * @covers ::getTransaction ::setPathCreated
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function canAddFile(): void
    {
        self::assertCount(2, FileCreationTransaction::getTransaction());
    }

    /**
     * @test
     * @medium
     * @covers ::setPathCreated ::getTransaction
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function pathsDeduplicated(): void
    {
        FileCreationTransaction::setPathCreated(self::TEST_PATH_1);
        self::assertCount(2, FileCreationTransaction::getTransaction());
    }

    /**
     * @test
     * @medium
     * @covers ::echoDirtyTransactionCleanupCommands
     */
    public function canEchoFindCommands(): void
    {
        $output = $this->getFindCommands();
        self::assertNotEmpty($output);
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
     * @test
     * @medium
     * @covers ::markTransactionSuccessful
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function markSuccessfulClearsTransaction(): void
    {
        FileCreationTransaction::markTransactionSuccessful();
        $output = $this->getFindCommands();
        self::assertEmpty($output);
    }
}
