<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    const SERVER = [
        ConfigInterface::paramDbUser => 'Value-' . ConfigInterface::paramDbUser,
        ConfigInterface::paramDbPass => 'Value-' . ConfigInterface::paramDbPass,
        ConfigInterface::paramDbHost => 'Value-' . ConfigInterface::paramDbHost,
        ConfigInterface::paramDbName => 'Value-' . ConfigInterface::paramDbName,
    ];

    public function testThrowExceptionRequiredParamNotSet()
    {
        $caughtException = null;
        $server = [];
        try {
            (new Config($server));
        } catch (ConfigException $e) {
            $caughtException = $e;
        }
        $this->assertInstanceOf(ConfigException::class, $caughtException);
    }

    public function testGetParam()
    {
        $config = new Config(self::SERVER);
        $expected = self::SERVER[ConfigInterface::paramDbName];
        $actual = $config->get(ConfigInterface::paramDbName);
        $this->assertEquals($expected, $actual);
    }

    public function testGetDefaultParam()
    {
        $config = new Config(self::SERVER);
        $expected = ConfigInterface::optionalParamsWithDefaults[ConfigInterface::paramDbDebug];
        $actual = $config->get(ConfigInterface::paramDbDebug);
        $this->assertEquals($expected, $actual);
    }

    public function testGetProjectRootDirectory()
    {
        $config = new Config(self::SERVER);
        $expected = realpath(__DIR__ . '/../');
        $actual = $config->getProjectRootDirectory();
        $this->assertEquals($expected, $actual);
    }

    public function testGetCalculatedDefaultParam()
    {
        $config = new Config(self::SERVER);
        $expected = realpath(__DIR__ . '/../') . '/src/Entities';
        $actual = $config->get(ConfigInterface::paramEntitiesPath);
        $this->assertEquals($expected, $actual);
    }

    public function testGetConfiguredNotDefaultParam()
    {
        $server = self::SERVER;
        $server[ConfigInterface::paramEntitiesPath] = realpath(__DIR__ . '/../') . '/var/src/Entities';
        $config = new Config($server);
        $expected = $server[ConfigInterface::paramEntitiesPath];
        $actual = $config->get(ConfigInterface::paramEntitiesPath);
        $this->assertEquals($expected, $actual);
    }
}
