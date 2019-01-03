<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\DBAL\Tools\Console as DBALConsole;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CliConfigCommandFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Configuration
     */
    private $migrationsConfig;

    public function __construct(ContainerInterface $container, Config $config, EntityManagerInterface $entityManager)
    {
        $this->container     = $container;
        $this->config        = $config;
        $this->entityManager = $entityManager;
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
        $commands           = [
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
        ];
        $migrationsCommands = [
            $this->container->get(ExecuteCommand::class),
            $this->container->get(GenerateCommand::class),
            $this->container->get(LatestCommand::class),
            $this->container->get(MigrateCommand::class),
            $this->container->get(DiffCommand::class),
            $this->container->get(UpToDateCommand::class),
            $this->container->get(StatusCommand::class),
            $this->container->get(VersionCommand::class),
        ];
        foreach ($migrationsCommands as $command) {
            $commands[] = $this->addMigrationsConfig($command);
        }

        return $commands;
    }

    private function addMigrationsConfig(AbstractCommand $command): AbstractCommand
    {
        $command->setMigrationConfiguration($this->getMigrationsConfig());

        return $command;
    }

    private function getMigrationsConfig(): Configuration
    {
        if (null === $this->migrationsConfig) {
            $this->migrationsConfig = new Configuration($this->entityManager->getConnection());
            $this->migrationsConfig->setMigrationsDirectory($this->config->get(Config::PARAM_MIGRATIONS_DIRECTORY));
            $this->migrationsConfig->setMigrationsAreOrganizedByYearAndMonth(true);
            $this->migrationsConfig->setMigrationsNamespace('Migrations');
        }

        return $this->migrationsConfig;
    }

    public function createHelperSet(): HelperSet
    {
        return new HelperSet(
            [
                'db'       => new DBALConsole\Helper\ConnectionHelper($this->entityManager->getConnection()),
                'em'       => new EntityManagerHelper($this->entityManager),
                'question' => new \Symfony\Component\Console\Helper\QuestionHelper(),
            ]
        );
    }
}
