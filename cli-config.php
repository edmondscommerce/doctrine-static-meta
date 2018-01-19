<?php declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\DoctrineExtend;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

require __DIR__ . '/vendor/autoload.php';

/**
 * Check for the `dbUser` environment variable.
 * If it is not found then we need to set up our env variables
 * Note - this bit can be customised to your requirements
 */
if (!isset($_SERVER['dbUser'])) {
    if (file_exists(__DIR__ . '/.env')) {
        SimpleEnv::setEnv(__DIR__ . '/.env');
    }
}

$config = new \EdmondsCommerce\DoctrineStaticMeta\Config();

if (!is_dir($config->get(ConfigInterface::paramEntitiesPath))) {
    throw new Exception(" ERROR  Entities path does not exist-  you need to either fix the config or create the entites path directory, currently configured as: [" . $config->get(ConfigInterface::paramEntitiesPath) . "] ");
}
$entityManager = DevEntityManagerFactory::getEm($config, false);

// This adds the DSM commands into the standard doctrine bin
$commands = DoctrineExtend::getCommands();

return ConsoleRunner::createHelperSet($entityManager);


