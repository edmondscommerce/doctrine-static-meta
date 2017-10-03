<?php declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__.'/vendor/autoload.php';

$entityManager = (new \EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory())->getEm(false);

// This adds the DSM commands into the standard doctrine bin
$commands = \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\DoctrineExtend::getCommands();

return ConsoleRunner::createHelperSet($entityManager);

