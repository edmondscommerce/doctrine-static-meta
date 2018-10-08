<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

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
            $this->container->get(GenerateRelationsCommand::class),
            $this->container->get(GenerateEntityCommand::class),
            $this->container->get(SetRelationCommand::class),
            $this->container->get(GenerateFieldCommand::class),
            $this->container->get(SetFieldCommand::class),
            $this->container->get(SetEmbeddableCommand::class),
            $this->container->get(GenerateEmbeddableFromArchetypeCommand::class),
            $this->container->get(RemoveUnusedRelationsCommand::class),
            $this->container->get(OverrideCreateCommand::class),
            $this->container->get(OverridesUpdateCommand::class),
            $this->container->get(CreateConstraintCommand::class),
            $this->container->get(CreateDataTransferObjectsFromEntitiesCommand::class),
            $this->container->get(CreateConstraintCommand::class),
        ];
    }
}
