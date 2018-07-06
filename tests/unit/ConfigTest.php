<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public const SERVER = [
        ConfigInterface::PARAM_DB_USER => 'Value-'.ConfigInterface::PARAM_DB_USER,
        ConfigInterface::PARAM_DB_PASS => 'Value-'.ConfigInterface::PARAM_DB_PASS,
        ConfigInterface::PARAM_DB_HOST => 'Value-'.ConfigInterface::PARAM_DB_HOST,
        ConfigInterface::PARAM_DB_NAME => 'Value-'.ConfigInterface::PARAM_DB_NAME,
    ];

    public function testThrowExceptionRequiredParamNotSet(): void
    {
        $caughtException = null;
        $server          = [];
        try {
            new Config($server);
        } catch (ConfigException $e) {
            $caughtException = $e;
        }
        self::assertInstanceOf(ConfigException::class, $caughtException);
    }

    public function testGetParam(): void
    {
        $config   = new Config(self::SERVER);
        $expected = self::SERVER[ConfigInterface::PARAM_DB_NAME];
        $actual   = $config->get(ConfigInterface::PARAM_DB_NAME);
        self::assertSame($expected, $actual);
    }

    public function testGetDefaultParam(): void
    {
        $config   = new Config(self::SERVER);
        $expected = ConfigInterface::OPTIONAL_PARAMS_WITH_DEFAULTS[ConfigInterface::PARAM_DB_DEBUG];
        $actual   = $config->get(ConfigInterface::PARAM_DB_DEBUG);
        self::assertSame($expected, $actual);
    }

    public function testGetProjectRootDirectory(): void
    {
        $config   = new Config(self::SERVER);
        $expected = realpath(__DIR__.'/../../');
        $actual   = $config::getProjectRootDirectory();
        self::assertSame($expected, $actual);
    }

    public function testGetCalculatedDefaultParam(): void
    {
        $config   = new Config(self::SERVER);
        $expected = realpath(__DIR__.'/../../').'/src/Entities';
        $actual   = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);
        self::assertSame($expected, $actual);
    }

    public function testGetConfiguredNotDefaultParam(): void
    {
        $server                                       = self::SERVER;
        $server[ConfigInterface::PARAM_ENTITIES_PATH] = realpath(__DIR__.'/../../').'/var/src/Entities';
        $config                                       = new Config($server);
        $expected                                     = $server[ConfigInterface::PARAM_ENTITIES_PATH];
        $actual                                       = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);
        self::assertSame($expected, $actual);
    }

    public function testParamsContainsAll(): void
    {
        $countParams     = count(ConfigInterface::PARAMS);
        $aggregated      = array_merge(
            ConfigInterface::REQUIRED_PARAMS,
            ConfigInterface::OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS,
            ConfigInterface::OPTIONAL_PARAMS_WITH_DEFAULTS
        );
        $countAggregated = count($aggregated);
        self::assertSame($countAggregated, $countParams);
    }
}
