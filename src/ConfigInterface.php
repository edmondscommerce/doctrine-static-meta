<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

interface ConfigInterface
{
    const NO_DEFAULT_VALUE = 'noDefaultValue';

    const PARAM_DB_USER       = 'dbUser';
    const PARAM_DB_PASS       = 'dbPass';
    const PARAM_DB_HOST       = 'dbHost';
    const PARAM_DB_NAME       = 'dbName';
    const PARAM_ENTITIES_PATH = 'entitiesPath';

    const requiredParams = [
        self::PARAM_DB_USER => self::PARAM_DB_USER,
        self::PARAM_DB_PASS => self::PARAM_DB_PASS,
        self::PARAM_DB_HOST => self::PARAM_DB_HOST,
        self::PARAM_DB_NAME => self::PARAM_DB_NAME,
    ];

    const PARAM_DB_DEBUG   = 'dbDebug';
    const PARAM_DB_DEVMODE = 'dbDevMode';

    const OPTIONAL_PARAMS_WITH_DEFAULTS = [
        self::PARAM_DB_DEBUG   => false,
        self::PARAM_DB_DEVMODE => false,
    ];

    //these parameters have defaults which are calculated by calling a method
    const OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS = [
        self::PARAM_ENTITIES_PATH => 'calculateEntitiesPath',
    ];

    /**
     * Get a config item by key, optionally with a default value
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
