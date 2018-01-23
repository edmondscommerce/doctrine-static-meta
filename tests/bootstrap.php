<?php declare(strict_types=1);
$phpunitEntitiesPath = __DIR__ . '/../var/phpunit/Entities';
if (!is_dir($phpunitEntitiesPath)) {
    mkdir($phpunitEntitiesPath, 0777, true);
}
$_SERVER[\EdmondsCommerce\DoctrineStaticMeta\ConfigInterface::paramEntitiesPath] = $phpunitEntitiesPath;

