<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

interface ConfigInterface
{
    /**
     * The parameters keys
     */
    public const PARAM_DB_DEBUG           = 'dbDebug';
    public const PARAM_DB_HOST            = 'dbHost';
    public const PARAM_DB_NAME            = 'dbName';
    public const PARAM_DB_PASS            = 'dbPass';
    public const PARAM_DB_USER            = 'dbUser';
    public const PARAM_DOCTRINE_DEVMODE   = 'doctrineDevMode';
    public const PARAM_DOCTRINE_PROXY_DIR = 'doctrineProxyDir';
    public const PARAM_ENTITIES_PATH      = 'entitiesPath';

    /**
     * A list of all parameters
     */
    public const PARAMS = [
        self::PARAM_DB_DEBUG,
        self::PARAM_DB_HOST,
        self::PARAM_DB_NAME,
        self::PARAM_DB_PASS,
        self::PARAM_DB_USER,
        self::PARAM_DOCTRINE_DEVMODE,
        self::PARAM_DOCTRINE_PROXY_DIR,
        self::PARAM_ENTITIES_PATH,
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
        self::PARAM_DB_DEBUG         => false,
        self::PARAM_DOCTRINE_DEVMODE => false,
    ];

    /**
     * Parameters with dynamically calculated defaults
     */
    public const OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS = [
        self::PARAM_ENTITIES_PATH      => 'calculateEntitiesPath',
        self::PARAM_DOCTRINE_PROXY_DIR => 'calculateProxyDir',
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
