<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Psr\Container\ContainerInterface;
use ReflectionException;
use RuntimeException;
use ts\Reflection\ReflectionMethod;

class EntityDependencyInjector
{
    public const INJECT_DEPENDENCY_METHOD_PREFIX = 'inject';

    private const TYPE_KEY_STATIC   = 'static';
    private const TYPE_KEY_INSTANCE = 'instance';
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;


    /**
     * This array is keyed by Entity FQN and the values are the inject*** method names that are used for injecting
     * dependencies
     *
     * @var array|ReflectionMethod
     */
    private $entityInjectMethods = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * This method loops over the inject methods for an Entity and then injects the relevant dependencies
     *
     * We match the method argument type with the dependency to be injected.
     *
     * @param EntityInterface $entity
     *
     * @throws ReflectionException
     */
    public function injectEntityDependencies(EntityInterface $entity): void
    {
        $this->buildEntityInjectMethodsForEntity($entity);
        $entityFqn = $this->leadingSlash($entity::getDoctrineStaticMeta()->getReflectionClass()->getName());
        $this->injectStatic($entity, $this->entityInjectMethods[$entityFqn][self::TYPE_KEY_STATIC]);
        $this->inject($entity, $this->entityInjectMethods[$entityFqn][self::TYPE_KEY_INSTANCE]);
    }

    /**
     * Build the array of entity methods to dependencies ready to be used for injection
     *
     * @param EntityInterface $entity
     *
     * @throws ReflectionException
     */
    private function buildEntityInjectMethodsForEntity(EntityInterface $entity): void
    {
        $reflection = $entity::getDoctrineStaticMeta()->getReflectionClass();
        $entityFqn  = $this->leadingSlash($reflection->getName());
        if (array_key_exists($entityFqn, $this->entityInjectMethods)) {
            return;
        }
        $this->entityInjectMethods[$entityFqn] = [
            self::TYPE_KEY_INSTANCE => [],
            self::TYPE_KEY_STATIC   => [],
        ];

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (!\ts\stringStartsWith($method->getName(), self::INJECT_DEPENDENCY_METHOD_PREFIX)) {
                continue;
            }
            $typeKey = $method->isStatic() ? self::TYPE_KEY_STATIC : self::TYPE_KEY_INSTANCE;

            $this->entityInjectMethods[$entityFqn][$typeKey][$method->getName()] =
                $this->getDependencyForInjectMethod($method);
        }
    }

    private function leadingSlash(string $fqn): string
    {
        return '\\' . ltrim($fqn, '\\');
    }

    private function getDependencyForInjectMethod(ReflectionMethod $method): string
    {
        $params = $method->getParameters();
        if (1 !== count($params)) {
            throw new RuntimeException(
                'Invalid method signature for ' .
                $method->getName() .
                ', should only take one argument which is the dependency to be injected'
            );
        }
        $type = current($params)->getType();
        if (null === $type) {
            throw new RuntimeException(
                'Invalid method signature for ' .
                $method->getName() .
                ', the object being set must be type hinted'
            );
        }

        return $type->getName();
    }

    private function injectStatic(EntityInterface $entity, array $methods): void
    {
        foreach ($methods as $methodName => $dependencyFqn) {
            $entity::$methodName($this->container->get($dependencyFqn));
        }
    }

    private function inject(EntityInterface $entity, array $methods): void
    {
        foreach ($methods as $methodName => $dependencyFqn) {
            $entity->$methodName($this->container->get($dependencyFqn));
        }
    }
}
