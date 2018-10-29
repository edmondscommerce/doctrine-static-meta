<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\B\Schema;

use EdmondsCommerce\DoctrineStaticMeta\Schema\UuidFunctionPolyfill;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Schema\UuidFunctionPolyfill
 */
class UuidFunctionPolyfillTest extends AbstractLargeTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/UuidFunctionPolyfillTest';

    /**
     * @var UuidFunctionPolyfill
     */
    private $polyfill;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->polyfill = new UuidFunctionPolyfill($this->getEntityManager());
    }

    /**
     * @test
     */
    public function itWillCreateTheFunctionIfItDoesNotExistAndRunAgainDoesNothing(): void
    {
        self::assertFalse($this->polyfill->checkProcedureExists(UuidFunctionPolyfill::UUID_TO_BIN));
        self::assertFalse($this->polyfill->checkProcedureExists(UuidFunctionPolyfill::BIN_TO_UUID));
        $this->polyfill->run();
        self::assertTrue($this->polyfill->checkProcedureExists(UuidFunctionPolyfill::UUID_TO_BIN));
        self::assertTrue($this->polyfill->checkProcedureExists(UuidFunctionPolyfill::BIN_TO_UUID));
        $this->polyfill->run();
        self::assertTrue($this->polyfill->checkProcedureExists(UuidFunctionPolyfill::UUID_TO_BIN));
        self::assertTrue($this->polyfill->checkProcedureExists(UuidFunctionPolyfill::BIN_TO_UUID));
    }
}
