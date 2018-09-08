<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small;

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Small
 * @coversDefaultClass   \EdmondsCommerce\DoctrineStaticMeta\Config
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
     * @covers ::__construct
     */
    public function itThrowsAnExceptionRequiredParamNotSet(): void
    {
        self::expectException(ConfigException::class);
        new Config([]);
    }

    /**
     * @test
     * @small
     * @covers ::validateConfig
     */
    public function itThrowsAnExceptionIfParamIsIncorrectType(): void
    {
        self::expectException(ConfigException::class);
        $server[ConfigInterface::PARAM_DB_USER] = true;
        new Config([$server]);
    }

    /**
     * @test
     * @small
     * @covers ::get
     */
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
     * @covers ::get
     */
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
     * @covers ::getProjectRootDirectory
     */
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
     * @covers ::calculateEntitiesPath
     * @covers ::get
     */
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
     * @covers ::get
     */
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
