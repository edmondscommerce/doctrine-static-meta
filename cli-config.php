<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CliConfigCommandFactory;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ErrorException;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

require __DIR__ . '/vendor/autoload.php';

set_error_handler(
    function ($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);

function getFactory(): CliConfigCommandFactory
{
    SimpleEnv::setEnv(__DIR__ . '/.env');
    $container = new Container();
    $container->buildSymfonyContainer($_SERVER);

    return $container->get(CliConfigCommandFactory::class);
}

try {

    $cliConfigCommandFactory = getFactory();
    $commands                = $cliConfigCommandFactory->getCommands();

    return $cliConfigCommandFactory->createHelperSet();

} catch (\Exception $e) {
    throw new DoctrineStaticMetaException(
        'Exception setting up Doctrine CLI: ' . $e->getMessage(), $e->getCode(),
        $e
    );
}