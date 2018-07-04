<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use PHPUnit\Framework\TestCase;

class SimpleEnvTest extends TestCase
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/unit/SimpleEnvTest';

    public static function setUpBeforeClass()
    {
/* The :void return type declaration that should be here would cause a BC issue */
        if (!is_dir(self::WORK_DIR)) {
            mkdir(self::WORK_DIR, 0777, true);
        }
    }

    public function testParseEnvWithExport(): void
    {
        $envPath = self::WORK_DIR.'/'.__FUNCTION__;
        file_put_contents(
            $envPath,
            "export dbUser=root\nexport dbPass=cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    /**
     * @param string $envPath
     *
     * @throws Exception\ConfigException
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

    public function testParseEnvWithoutExport(): void
    {
        $envPath = self::WORK_DIR.'/'.__FUNCTION__;
        file_put_contents(
            $envPath,
            "dbUser=root\ndbPass=cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    public function testParseEnvWithExcessWhitespace(): void
    {
        $envPath = self::WORK_DIR.'/'.__FUNCTION__;
        file_put_contents(
            $envPath,
            "\t\tdbUser=root\n      dbPass = cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    public function testParseEnvWithShebang(): void
    {
        $envPath = self::WORK_DIR.'/'.__FUNCTION__;
        file_put_contents(
            $envPath,
            "#!/bin/bash\ndbUser=root\ndbPass=cheese"
        );
        $this->assertParsedCorrectly($envPath);
    }

    public function testParseEnvWithEmptyLines(): void
    {
        $envPath = self::WORK_DIR.'/'.__FUNCTION__;
        file_put_contents(
            $envPath,
            "\n\n\ndbUser=root\ndbPass=cheese\n\n\n"
        );
        $this->assertParsedCorrectly($envPath);
    }
}
