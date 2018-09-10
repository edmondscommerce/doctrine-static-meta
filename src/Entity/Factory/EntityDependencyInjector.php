<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use ReflectionMethod;

class EntityDependencyInjector
{
    public const INJECT_DEPENDENCY_METHOD_PREFIX = 'inject';

    /**
     * This array is keyed by Entity FQN and the values are dependencies
     *
     * @var array|object[]
     */
    private $entityDependencies = [];

    /**
     * This array is keyed by Entity FQN and the values are the inject*** method names that are used for injecting
     * dependencies
     *
     * @var array|ReflectionMethod
     */
    private $entityInjectMethods = [];

    public function addEntityDependency(string $entityFqn, object $dependency): self
    {
        $this->entityDependencies[$this->leadingSlash($entityFqn)][] = $dependency;

        return $this;
    }

    /**
     * This method loops over the inject methods for an Entity and then injects the relevant dependencies
     *
     * We match the method argument type with the dependency to be injected. The limitation here is that you can only
     * have one inject method for a set type so the inject method should type hint for something as precise as
     * possible
     *
     * @param EntityInterface $entity
     */
    public function injectEntityDependencies(EntityInterface $entity): void
    {
        $methods      = $this->getInjectMethodsForEntity($entity);
        $entityFqn = $this->leadingSlash($entity::getDoctrineStaticMeta()->getReflectionClass()->getName());
        $dependencies = $this->entityDependencies[$entityFqn];
        foreach ($dependencies as $dependency) {
            foreach ($methods as $key => $method) {
                if ($this->injectDependency($dependency, $method, $entity)) {
                    unset($methods[$key]);
                    continue 2;
                }
            }
            throw new \RuntimeException(
                'Failed finding an inject method in ' .
                $entity::getDoctrineStaticMeta()->getShortName() .
                ' for dependency: ' .
                \get_class($dependency)
            );
        }
    }

    /**
     * Build and retrieve the array of inject method names for an Entity
     *
     * Validates that the number of inject methods and the number of dependencies marked for injection matches up
     *
     * @param EntityInterface $entity
     *
     * @return array|ReflectionMethod[]
     */
    private function getInjectMethodsForEntity(EntityInterface $entity): array
    {
        $reflection = $entity::getDoctrineStaticMeta()->getReflectionClass();
        $entityFqn  = $this->leadingSlash($reflection->getName());
        if (array_key_exists($entityFqn, $this->entityInjectMethods)) {
            return $this->entityInjectMethods[$entityFqn];
        }
        $this->entityInjectMethods[$entityFqn] = [];
        $methods                               = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (!\ts\stringStartsWith($method->getName(), self::INJECT_DEPENDENCY_METHOD_PREFIX)) {
                continue;
            }
            $this->entityInjectMethods[$entityFqn][] = $method;
        }
        if ([] === $this->entityInjectMethods[$entityFqn]) {
            return [];
        }
        $numDependeciesForEntity            = count($this->entityDependencies[$entityFqn]);
        $numDependecyInjectMethodsForEntity = count($this->entityInjectMethods[$entityFqn]);
        if ($numDependeciesForEntity !== $numDependecyInjectMethodsForEntity) {
            throw new \RuntimeException('The number of dependencies [' .
                                        $numDependeciesForEntity .
                                        '] and the nubmer of dependency inject methods [' .
                                        $numDependecyInjectMethodsForEntity .
                                        '] does not match.');
        }

        return $this->entityInjectMethods[$entityFqn];
    }

    private function injectDependency(object $dependency, ReflectionMethod $method, EntityInterface $entity): bool
    {
        $params = $method->getParameters();
        if (1 !== count($params)) {
            throw new \RuntimeException(
                'Invalid method signature for ' .
                $method->getName() .
                ', should only take one argument which is the dependency to be injected'
            );
        }
        $type = current($params)->getType();
        if (null === $type) {
            throw new \RuntimeException(
                'Invalid method signature for ' .
                $method->getName() .
                ', the object being set must be type hinted'
            );
        }
        $interfaceOrObjectFqn = $type->getName();
        if ($dependency instanceof $interfaceOrObjectFqn) {
            $methodName = $method->getName();
            if ($method->isStatic()) {
                $entity::$methodName($dependency);

                return true;
            }
            $entity->$methodName($dependency);

            return true;
        }

        return false;
    }

    private function leadingSlash(string $fqn): string
    {
        return '\\' . ltrim($fqn, '\\');
    }
}
