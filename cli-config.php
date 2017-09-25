<?php declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;

require __DIR__.'/vendor/autoload.php';

$entityManager = (new \EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory())->getEm(false);

$commands = [
    new GenerateRelationsCommand(),
];

return ConsoleRunner::createHelperSet($entityManager);

