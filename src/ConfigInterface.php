<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

interface ConfigInterface
{
    const noDefaultValue = 'noDefaultValue';

    const paramDbUser = 'dbUser';
    const paramDbPass = 'dbPass';
    const paramDbHost = 'dbHost';
    const paramDbName = 'dbName';
    const paramEntitiesPath = 'entitiesPath';

    const requiredParams = [
        self::paramDbUser => self::paramDbUser,
        self::paramDbPass => self::paramDbPass,
        self::paramDbHost => self::paramDbHost,
        self::paramDbName => self::paramDbName,
    ];

    const paramDbDebug = 'dbDebug';
    const paramDbDevMode = 'dbDevMode';

    const optionalParamsWithDefaults = [
        self::paramDbDebug => false,
        self::paramDbDevMode => false
    ];

    //these parameters have defaults which are calculated by calling a method
    const optionalParamsWithCalculatedDefaults = [
        self::paramEntitiesPath => 'calculateEntitiesPath'
    ];

    /**
     * Get a config item by key, optionally with a default value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = self::noDefaultValue);

    /**
     * Get the absolute path to the root of the current project
     *
     * @return string
     */
    public static function getProjectRootDirectory(): string;
}
