<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\SchemaBuilder;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

call_user_func(
    function () {
        SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
        $server                               = $_SERVER;
        $server[ConfigInterface::paramDbName] .= '_test';
        $config                               = new Config($server);
        (new Database($config))
            ->drop(true)
            ->create(true)
            ->close();
        $entityManager = DevEntityManagerFactory::getEm($config, false);
        $schemaBuilder = new SchemaBuilder($entityManager);
        $schemaBuilder->createTables();
        $schemaBuilder = null;
        $entityManager = null;
    }
);
