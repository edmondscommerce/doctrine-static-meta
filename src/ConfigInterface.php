<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

interface ConfigInterface
{
    const paramDbUser = 'dbUser';
    const paramDbPass = 'dbPass';
    const paramDbHost = 'dbHost';
    const paramDbName = 'dbName';
    const paramDbEntitiesPath = 'dbEntitiesPath';

    const params = [
        self::paramDbUser => self::paramDbUser,
        self::paramDbPass => self::paramDbPass,
        self::paramDbHost => self::paramDbHost,
        self::paramDbName => self::paramDbName,
        self::paramDbEntitiesPath => self::paramDbEntitiesPath
    ];

    public function get(string $key): string;
}
