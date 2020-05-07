<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\Modifiers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixtureEntitiesModifierInterface;

/**
 * Use this modifier along when creating your Fixture instance to generate fixtures with all associations set
 */
class AddAssociationEntitiesModifier implements FixtureEntitiesModifierInterface
{
    /**
     * @var TestEntityGeneratorFactory
     */
    private TestEntityGeneratorFactory $testEntityGeneratorFactory;

    /**
     * @var TestEntityGenerator
     */
    private TestEntityGenerator $testEntityGenerator;

    public function __construct(TestEntityGeneratorFactory $testEntityGeneratorFactory)
    {
        $this->testEntityGeneratorFactory = $testEntityGeneratorFactory;
    }

    public function modifyEntities(array &$entities): void
    {
        foreach ($entities as $entity) {
            $this->addAssociationEntities($entity);
        }
    }

    private function addAssociationEntities(EntityInterface $entity): void
    {
        $this->getTestEntityGeneratorForEntity($entity)->addAssociationEntities($entity);
    }

    private function getTestEntityGeneratorForEntity(EntityInterface $entity): TestEntityGenerator
    {
        if ($this->testEntityGenerator instanceof TestEntityGenerator) {
            return $this->testEntityGenerator;
        }
        $this->testEntityGenerator = $this->testEntityGeneratorFactory->createForEntityFqn(
            $entity::getEntityFqn()
        );

        return $this->testEntityGenerator;
    }
}
