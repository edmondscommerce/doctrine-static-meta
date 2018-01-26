<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
$server                               = $_SERVER;
$server[ConfigInterface::paramDbName] .= '_test';
$config                               = new Config($server);
$database                             = new Database($config);
$database->drop(true);
$database->create(true);
