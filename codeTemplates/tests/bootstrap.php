<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\SchemaBuilder;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

call_user_func(
/**
 * @throws ReflectionException
 * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
 * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
 */
    function () {
        SimpleEnv::setEnv(Config::getProjectRootDirectory().'/.env');
        $server                                 = $_SERVER;
        $server[ConfigInterface::PARAM_DB_NAME] .= '_test';
        $container                              = new Container();
        $container->buildSymfonyContainer($_SERVER);
        /**
         * @var $database Database
         */
        $database = $container->get(Database::class);
        $database->drop(true)->create(true)->close();
        $schema = $container->get(EdmondsCommerce\DoctrineStaticMeta\Schema\SchemaBuilder::class);
        /**
         * @var $schema SchemaBuilder
         */
        $schema->createTables();
    }
);
