<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Di\CompilerPass;

use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\EntityFactoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * In this compiler pass, we loop through all services tagged with the entity dependency service tag
 *
 * The
 */
class EntityDependencyPass implements CompilerPassInterface
{
    public const ENTITY_DEPENDENCY_SERVICE_TAG = 'dsm.entity_dependency';

    public const ENTITY_FACTORY_ADD_ENTITY_DEPENDENCY_METHOD = 'addEntityDependency';

    /**
     * @var Definition
     */
    private $entityFactoryDefinition;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $this->entityFactoryDefinition = $container->findDefinition(EntityFactoryInterface::class);
        $dependencies                  = $container->findTaggedServiceIds(self::ENTITY_DEPENDENCY_SERVICE_TAG);
        foreach ($dependencies as $dependencyFqn => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists('alias', $attributes)) {
                    throw new \RuntimeException('Invalid tag config for ' .
                                                $dependencyFqn .
                                                ', must have an alias configured which is an Entity FQN');
                }
                $entityFqn = $attributes['alias'];
                if (!\ts\stringContains($entityFqn, '\\Entities\\')) {
                    throw new \RuntimeException('Invalid alias for ' .
                                                $dependencyFqn .
                                                ', must have an alias configured which is an Entity FQN');
                }
                $this->addEntityDependencyToEntityFactory($entityFqn, $dependencyFqn);
            }
        }
    }

    private function addEntityDependencyToEntityFactory(string $entityFqn, string $dependencyFqn): void
    {
        $this->entityFactoryDefinition
            ->addMethodCall(
                self::ENTITY_FACTORY_ADD_ENTITY_DEPENDENCY_METHOD,
                [$entityFqn, new Reference($dependencyFqn)]
            );
    }
}
