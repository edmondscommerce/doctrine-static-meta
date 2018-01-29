<?php declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\DoctrineExtend;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

require __DIR__ . '/vendor/autoload.php';

$entityManager = DevEntityManagerFactory::setupAndGetEm();

// This adds the DSM commands into the standard doctrine bin
$commands = DoctrineExtend::getCommands();

return ConsoleRunner::createHelperSet($entityManager);
