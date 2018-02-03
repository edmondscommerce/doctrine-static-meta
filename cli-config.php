<?php declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\DoctrineExtend;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

require __DIR__.'/vendor/autoload.php';
SimpleEnv::setEnv(__DIR__.'/.env');
$container = new Container();
$container->buildSymfonyContainer($_SERVER);

$entityManager = $container->get(\Doctrine\ORM\EntityManager::class);

// This adds the DSM commands into the standard doctrine bin
$commands = [
    $container->get(GenerateRelationsCommand::class),
    $container->get(GenerateEntityCommand::class),
    $container->get(SetRelationCommand::class),
];

return ConsoleRunner::createHelperSet($entityManager);


