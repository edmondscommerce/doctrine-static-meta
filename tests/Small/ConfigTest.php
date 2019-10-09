<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small;

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Small
 * @covers   \EdmondsCommerce\DoctrineStaticMeta\Config
 */
class ConfigTest extends TestCase
{
    public const SERVER = [
        ConfigInterface::PARAM_DB_USER => 'Value-' . ConfigInterface::PARAM_DB_USER,
        ConfigInterface::PARAM_DB_PASS => 'Value-' . ConfigInterface::PARAM_DB_PASS,
        ConfigInterface::PARAM_DB_HOST => 'Value-' . ConfigInterface::PARAM_DB_HOST,
        ConfigInterface::PARAM_DB_NAME => 'Value-' . ConfigInterface::PARAM_DB_NAME,
    ];

    /**
     * @test
     * @small
     *      */
    public function itThrowsAnExceptionRequiredParamNotSet(): void
    {
        $this->expectException(ConfigException::class);
        new Config([]);
    }

    /**
     * @test
     * @small
     *      */
    public function itThrowsAnExceptionIfParamIsIncorrectType(): void
    {
        $this->expectException(ConfigException::class);
        $server[ConfigInterface::PARAM_DB_USER] = true;
        new Config([$server]);
    }

    /**
     * @test
     * @small
     *      */
    public function itCanHandleIntoToBoolConversion(): void
    {
        $server                                  = self::SERVER;
        $server[ConfigInterface::PARAM_DEVMODE]  = 0;
        $server[ConfigInterface::PARAM_DB_DEBUG] = 1;
        $config                                  = new Config($server);
        self::assertFalse($config->get(ConfigInterface::PARAM_DEVMODE));
        self::assertTrue($config->get(ConfigInterface::PARAM_DB_DEBUG));
    }

    /**
     * @test
     * @small
     *      */
    public function getParam(): void
    {
        $config   = new Config(self::SERVER);
        $expected = self::SERVER[ConfigInterface::PARAM_DB_NAME];
        $actual   = $config->get(ConfigInterface::PARAM_DB_NAME);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     *      */
    public function getDefaultParam(): void
    {
        $config   = new Config(self::SERVER);
        $expected = ConfigInterface::OPTIONAL_PARAMS_WITH_DEFAULTS[ConfigInterface::PARAM_DB_DEBUG];
        $actual   = $config->get(ConfigInterface::PARAM_DB_DEBUG);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     *      */
    public function getProjectRootDirectory(): void
    {
        $config   = new Config(self::SERVER);
        $expected = realpath(__DIR__ . '/../../');
        $actual   = $config::getProjectRootDirectory();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     *      *      */
    public function getCalculatedDefaultParam(): void
    {
        $config   = new Config(self::SERVER);
        $expected = realpath(__DIR__ . '/../../') . '/src/Entities';
        $actual   = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     *      */
    public function getConfiguredNotDefaultParam(): void
    {
        $server                                       = self::SERVER;
        $server[ConfigInterface::PARAM_ENTITIES_PATH] = realpath(__DIR__ . '/../../') . '/var/src/Entities';
        $config                                       = new Config($server);
        $expected                                     = $server[ConfigInterface::PARAM_ENTITIES_PATH];
        $actual                                       = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @coversNothing
     */
    public function paramsContainsAll(): void
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
