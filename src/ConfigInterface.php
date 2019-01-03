<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Mapping\NamingStrategy;

interface ConfigInterface
{
    public const DSM_ROOT_NAMESPACE = __NAMESPACE__;

    public const TYPE_STRING = 'string';
    public const TYPE_BOOL   = 'bool';

    /**
     * The parameters keys
     */
    public const PARAM_DB_DEBUG                 = 'dbDebug';
    public const PARAM_DB_HOST                  = 'dbHost';
    public const PARAM_DB_NAME                  = 'dbName';
    public const PARAM_DB_PASS                  = 'dbPass';
    public const PARAM_DB_USER                  = 'dbUser';
    public const PARAM_DEVMODE                  = 'devMode';
    public const PARAM_DOCTRINE_PROXY_DIR       = 'doctrineProxyDir';
    public const PARAM_ENTITIES_PATH            = 'entitiesPath';
    public const PARAM_DOCTRINE_CACHE_DRIVER    = 'doctrineCacheDriver';
    public const PARAM_FILESYSTEM_CACHE_PATH    = 'filesystemCachePath';
    public const PARAM_DOCTRINE_NAMING_STRATEGY = 'doctrineNamingStrategy';
    /**
     * The retry connection will attempt to reconnect if the connection is lost for any reason
     */
    public const PARAM_USE_RETRY_CONNECTION = 'useRetryConnection';

    public const PARAM_MIGRATIONS_DIRECTORY = 'migrationsDirectory';

    public const DEFAULT_DB_DEBUG                 = false;
    public const DEFAULT_DEVMODE                  = false;
    public const DEFAULT_DOCTRINE_CACHE_DRIVER    = FilesystemCache::class;
    public const DEFAULT_DOCTRINE_NAMING_STRATEGY = 'underscore';
    public const DEFAULT_USE_RETRY_CONNECTION     = true;

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
        self::PARAM_FILESYSTEM_CACHE_PATH,
        self::PARAM_DOCTRINE_NAMING_STRATEGY,
        self::PARAM_MIGRATIONS_DIRECTORY,
        self::PARAM_USE_RETRY_CONNECTION,
    ];

    /**
     * A list of all parameters and their types
     */
    public const PARAM_TYPES = [
        self::PARAM_DB_DEBUG                 => self::TYPE_BOOL,
        self::PARAM_DB_HOST                  => self::TYPE_STRING,
        self::PARAM_DB_NAME                  => self::TYPE_STRING,
        self::PARAM_DB_PASS                  => self::TYPE_STRING,
        self::PARAM_DB_USER                  => self::TYPE_STRING,
        self::PARAM_DEVMODE                  => self::TYPE_BOOL,
        self::PARAM_DOCTRINE_PROXY_DIR       => self::TYPE_STRING,
        self::PARAM_ENTITIES_PATH            => self::TYPE_STRING,
        self::PARAM_DOCTRINE_CACHE_DRIVER    => self::TYPE_STRING,
        self::PARAM_FILESYSTEM_CACHE_PATH    => self::TYPE_STRING,
        self::PARAM_DOCTRINE_NAMING_STRATEGY => NamingStrategy::class,
        self::PARAM_USE_RETRY_CONNECTION     => self::TYPE_BOOL,
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
        self::PARAM_USE_RETRY_CONNECTION  => self::DEFAULT_USE_RETRY_CONNECTION,
    ];

    /**
     * Parameters with dynamically calculated defaults
     */
    public const OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS = [
        self::PARAM_ENTITIES_PATH            => 'calculateEntitiesPath',
        self::PARAM_DOCTRINE_PROXY_DIR       => 'calculateProxyDir',
        self::PARAM_DOCTRINE_NAMING_STRATEGY => 'getUnderscoreNamingStrategy',
        self::PARAM_FILESYSTEM_CACHE_PATH    => 'getFilesystemCachePath',
        self::PARAM_MIGRATIONS_DIRECTORY     => 'calculateMigrationsDirectory',
    ];

    /**
     * A specially defined value to clearly describe no default
     */
    public const NO_DEFAULT_VALUE = 'noDefaultValue';

    /**
     * Get the absolute path to the root of the current project
     *
     * @return string
     */
    public static function getProjectRootDirectory(): string;

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
}
