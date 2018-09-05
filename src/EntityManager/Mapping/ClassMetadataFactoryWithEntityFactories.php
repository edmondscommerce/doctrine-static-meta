<?php

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ReflectionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

class ClassMetadataFactoryWithEntityFactories extends ClassMetadataFactory implements EntityFactoryAware
{
    /** @var EntityFactoryInterface[] */
    public static $entityFactories = [];
    /** @var GenericFactoryInterface|null */
    public static $genericFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        parent::setEntityManager($entityManager);
        $this->entityManager = $entityManager;
    }

    public function addEntityFactory(string $name, EntityFactoryInterface $entityFactory): void
    {
        self::$entityFactories[$name] = $entityFactory;
    }

    public function addGenericFactory(GenericFactoryInterface $genericFactory): void
    {
        self::$genericFactory = $genericFactory;
    }

    public function wakeupReflection(ClassMetadata $class, ReflectionService $reflectionService): void
    {
        parent::wakeupReflection($class, $reflectionService);
        if ($class instanceof ClassMetadataWithEntityFactories) {
            $class->setFactories(self::$entityFactories, self::$genericFactory);
        }
    }

    protected function newClassMetadataInstance($className): ClassMetadata
    {
        return new ClassMetadataWithEntityFactories(
            $className,
            $this->entityManager->getConfiguration()->getNamingStrategy(),
            $this->getEntityFactories()
        );
    }

    /**
     * @return EntityFactoryInterface[]
     */
    public function getEntityFactories(): array
    {
        return self::$entityFactories;
    }
}
