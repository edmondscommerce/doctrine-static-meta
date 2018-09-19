<?php declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateConstraintCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableFromArchetypeCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverrideCreateCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverridesUpdateCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\RemoveUnusedRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetEmbeddableCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ErrorException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
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

try {

    SimpleEnv::setEnv(__DIR__ . '/.env');
    $container = new Container();
    $container->buildSymfonyContainer($_SERVER);

    $schemaBuilder = $container->get(Schema::class);
    $schemaBuilder->validate()->update();

// This adds the DSM commands into the standard doctrine bin
    $commands = [
        $container->get(GenerateRelationsCommand::class),
        $container->get(GenerateEntityCommand::class),
        $container->get(SetRelationCommand::class),
        $container->get(GenerateFieldCommand::class),
        $container->get(SetFieldCommand::class),
        $container->get(SetEmbeddableCommand::class),
        $container->get(GenerateEmbeddableFromArchetypeCommand::class),
        $container->get(RemoveUnusedRelationsCommand::class),
        $container->get(OverrideCreateCommand::class),
        $container->get(OverridesUpdateCommand::class),
        $container->get(CreateConstraintCommand::class),
    ];

    $entityManager = $container->get(EntityManagerInterface::class);
} catch (DoctrineStaticMetaException | ErrorException $e) {
    throw new DoctrineStaticMetaException('Exception setting up Doctrine CLI: ' . $e->getMessage(), $e->getCode(), $e);
}

return ConsoleRunner::createHelperSet($entityManager);

