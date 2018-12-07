<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;
use Psr\Container\ContainerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CliConfigCommandFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * For use in your project's cli-config.php file
     *
     * @see /cli-config.php
     *
     * @return array
     */
    public function getCommands(): array
    {
        return [
            /**
             * DSM Commmands
             */
            $this->container->get(GenerateRelationsCommand::class),
            $this->container->get(GenerateEntityCommand::class),
            $this->container->get(SetRelationCommand::class),
            $this->container->get(GenerateFieldCommand::class),
            $this->container->get(SetFieldCommand::class),
            $this->container->get(SetEmbeddableCommand::class),
            $this->container->get(GenerateEmbeddableFromArchetypeCommand::class),
            $this->container->get(GenerateEmbeddableSkeletonCommand::class),
            $this->container->get(RemoveUnusedRelationsCommand::class),
            $this->container->get(OverrideCreateCommand::class),
            $this->container->get(OverridesUpdateCommand::class),
            $this->container->get(CreateConstraintCommand::class),
            $this->container->get(FinaliseBuildCommand::class),
            $this->container->get(CreateConstraintCommand::class),
            /**
             * Migrations Commands
             */
            $this->container->get(ExecuteCommand::class),
            $this->container->get(GenerateCommand::class),
            $this->container->get(LatestCommand::class),
            $this->container->get(MigrateCommand::class),
            $this->container->get(DiffCommand::class),
            $this->container->get(UpToDateCommand::class),
            $this->container->get(StatusCommand::class),
            $this->container->get(VersionCommand::class),
        ];
    }
}
