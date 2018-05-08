<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Cache\ArrayCache;

interface ConfigInterface
{
    /**
     * The parameters keys
     */
    public const PARAM_DB_DEBUG              = 'dbDebug';
    public const PARAM_DB_HOST               = 'dbHost';
    public const PARAM_DB_NAME               = 'dbName';
    public const PARAM_DB_PASS               = 'dbPass';
    public const PARAM_DB_USER               = 'dbUser';
    public const PARAM_DEVMODE               = 'devMode';
    public const PARAM_DOCTRINE_PROXY_DIR    = 'doctrineProxyDir';
    public const PARAM_ENTITIES_PATH         = 'entitiesPath';
    public const PARAM_DOCTRINE_CACHE_DRIVER = 'doctrineCacheDriver';
    public const PARAM_DOCTRINE_NAMING_STRATEGY = 'doctrineNamingStrategy';

    public const DEFAULT_DB_DEBUG              = false;
    public const DEFAULT_DEVMODE               = false;
    public const DEFAULT_DOCTRINE_CACHE_DRIVER = ArrayCache::class;
    public const DEFAULT_DOCTRINE_NAMING_STRATEGY = 'underscore';

    /**
     * A list of all parameters
     */
    public const PARAMS = [
        self::PARAM_DB_DEBUG,
        self::PARAM_DB_HOST,
        self::PARAM_DB_NAME,
        self::PARAM_DB_PASS,
        self::PARAM_DB_USER,
        self::PARAM_DEVMODE,
        self::PARAM_DOCTRINE_PROXY_DIR,
        self::PARAM_ENTITIES_PATH,
        self::PARAM_DOCTRINE_CACHE_DRIVER,
        self::PARAM_DOCTRINE_NAMING_STRATEGY
    ];

    /**
     * Required parameters
     */
    public const REQUIRED_PARAMS = [
        self::PARAM_DB_HOST => self::PARAM_DB_HOST,
        self::PARAM_DB_NAME => self::PARAM_DB_NAME,
        self::PARAM_DB_PASS => self::PARAM_DB_PASS,
        self::PARAM_DB_USER => self::PARAM_DB_USER,
    ];

    /**
     * Parameters with scalar defaults
     */
    public const OPTIONAL_PARAMS_WITH_DEFAULTS = [
        self::PARAM_DB_DEBUG              => self::DEFAULT_DB_DEBUG,
        self::PARAM_DEVMODE               => self::DEFAULT_DEVMODE,
        self::PARAM_DOCTRINE_CACHE_DRIVER => self::DEFAULT_DOCTRINE_CACHE_DRIVER,
    ];

    /**
     * Parameters with dynamically calculated defaults
     */
    public const OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS = [
        self::PARAM_ENTITIES_PATH      => 'calculateEntitiesPath',
        self::PARAM_DOCTRINE_PROXY_DIR => 'calculateProxyDir',
        self::PARAM_DOCTRINE_NAMING_STRATEGY => 'getUnderscoreNamingStrategy'
    ];

    /**
     * A specially defined value to clearly describe no default
     */
    public const NO_DEFAULT_VALUE = 'noDefaultValue';

    /**
     * Get a config item by key, optionally with a default value
     *
     * Uses the special "No Default" value as a default to cleary indicate that there is no default.
     * Allows defaults to be falsey
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = self::NO_DEFAULT_VALUE);

    /**
     * Get the absolute path to the root of the current project
     *
     * @return string
     */
    public static function getProjectRootDirectory(): string;
}
