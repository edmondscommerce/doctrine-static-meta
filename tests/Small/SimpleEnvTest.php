<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small;

use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleEnvTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Small
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\SimpleEnv
 */
class SimpleEnvTest extends TestCase
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/Small/SimpleEnvTest';

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        /* The :void return type declaration that should be here would cause a BC issue */
        if (!is_dir(self::WORK_DIR)) {
            mkdir(self::WORK_DIR, 0777, true);
        }
    }

    /**
     * @test
     * @small
     * @covers ::setEnv ::processLine
     */
    public function parseEnvWithExport(): void
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "export dbUser=root\nexport dbPass=cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    /**
     * @param string $envPath
     *
     * @throws ConfigException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function assertParsedCorrectly(string $envPath): void
    {
        $server = [];
        $error  = print_r(
            [
                'envFile' => file_get_contents($envPath),
                '$server' => $server,
            ],
            true
        );
        SimpleEnv::setEnv($envPath, $server);
        self::assertArrayHasKey(
            ConfigInterface::PARAM_DB_USER,
            $server,
            $error
        );
        self::assertNotEmpty($server[ConfigInterface::PARAM_DB_USER]);

        self::assertArrayHasKey(
            ConfigInterface::PARAM_DB_PASS,
            $server,
            $error
        );
        self::assertNotEmpty($server[ConfigInterface::PARAM_DB_PASS]);
    }

    /**
     * @test
     * @small
     * @covers ::setEnv ::processLine
     */
    public function parseEnvWithoutExport(): void
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "dbUser=root\ndbPass=cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    /**
     * @test
     * @small
     * @covers ::setEnv ::processLine
     */
    public function parseEnvWithExcessWhitespace(): void
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "\t\tdbUser=root\n      dbPass = cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    /**
     * @test
     * @small
     * @covers ::setEnv ::processLine
     */
    public function parseEnvWithShebang(): void
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "#!/bin/bash\ndbUser=root\ndbPass=cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    /**
     * @test
     * @small
     * @covers ::setEnv ::processLine
     */
    public function parseEnvWithEmptyLines(): void
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "\n\n\ndbUser=root\ndbPass=cheese\n\n\n"
        );
        $this->assertParsedCorrectly($envPath);
    }
}
