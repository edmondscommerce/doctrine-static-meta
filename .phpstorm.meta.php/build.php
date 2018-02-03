#!/usr/bin/env php
<?php declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';
$php  = <<<'PHP'
<?php declare(strict_types=1);

namespace PHPSTORM_META {

    /**
     * This gives us dynamic type hinting if we use the container as a service locator
     *
     * @see https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
     *
     * This is built using the build.php script
     */
    override(
        \EdmondsCommerce\DoctrineStaticMeta\Container::get(0),
        map([
##MAP##
            ]
        )
    );
}
PHP;
$maps = [];
foreach (\EdmondsCommerce\DoctrineStaticMeta\Container::SERVICES as $service) {
    $maps[] = "                '\\$service'=>\\$service::class,";
}
$php = str_replace('##MAP##', implode("\n", $maps), $php);
file_put_contents(__DIR__.'/container.meta.php', $php);

