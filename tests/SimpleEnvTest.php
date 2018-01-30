<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

class SimpleEnvTest extends AbstractTest
{
    const WORK_DIR = VAR_PATH . '/SimpleEnvTest';

    public function testParseEnvWithExport()
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "export dbUser=root\nexport dbPass=cheese"
        );
        $this->asserParsedCorrectly($envPath);
    }

    public function testParseEnvWithoutExport()
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "dbUser=root\ndbPass=cheese"
        );
        $this->asserParsedCorrectly($envPath);
    }

    public function testParseEnvWithExcessWhitespace()
    {
        $envPath = self::WORK_DIR . '/' . __FUNCTION__;
        file_put_contents(
            $envPath,
            "\t\tdbUser=root\n      dbPass = cheese"
        );
        $this->asserParsedCorrectly($envPath);
    }

    protected function asserParsedCorrectly(string $envPath)
    {
        $server = [];
        $error  = print_r(
            [
                'envFile' => file_get_contents($envPath),
                '$server' => $server
            ],
            true
        );
        SimpleEnv::setEnv($envPath, $server);
        $this->assertArrayHasKey(
            ConfigInterface::paramDbUser,
            $server,
            $error
        );
        $this->assertArrayHasKey(
            ConfigInterface::paramDbPass,
            $server,
            $error
        );
    }
}
