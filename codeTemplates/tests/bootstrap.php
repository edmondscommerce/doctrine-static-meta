<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

/**
 * @throws ReflectionException
 * @throws ConfigException
 * @throws DoctrineStaticMetaException
 */
(static function (): void {
    SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
    $server                                 = $_SERVER;
    $server[ConfigInterface::PARAM_DB_NAME] .= '_test';
    $container                              = new Container();
    $container->buildSymfonyContainer($server);
    $database = $container->get(Database::class);
    $database->drop(true)->create(true)->close();
    $schemaTool = $container->get(Schema::class);
    $schemaTool->validate()->update();
})();
