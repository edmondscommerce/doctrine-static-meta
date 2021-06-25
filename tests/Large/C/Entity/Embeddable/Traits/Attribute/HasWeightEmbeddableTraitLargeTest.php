<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Embeddable\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Attribute\WeightEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Attribute\WeightEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ReflectionException;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Attribute\HasWeightEmbeddableTrait
 */
class HasWeightEmbeddableTraitLargeTest extends AbstractLargeTest
{
    public const  WORK_DIR    = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/HasWeightEmbeddableTraitLargeTest';
    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;
    /**
     * @var string
     */
    private string $entityFqn;

    public function setup():void
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function itCanBeSavedAndReloadedWithTheCorrectValues(): void
    {
        $entity = $this->createTestEntity();
        $entity->update(
            new class ($this->getCopiedFqn(self::TEST_ENTITY), $entity->getId()) extends AbstractEntityUpdateDto
            {
                public function getWeightEmbeddable(): WeightEmbeddableInterface
                {
                    return new WeightEmbeddable(
                        WeightEmbeddableInterface::UNIT_GRAM,
                        100
                    );
                }
            }
        );
        $this->getEntitySaver()->save($entity);
        $loaded                 =
            $this->getRepositoryFactory()->getRepository($this->getCopiedFqn(self::TEST_ENTITY))->findAll()[0];
        $loadedWeightEmbeddable = $loaded->getWeightEmbeddable();
        self::assertSame(WeightEmbeddableInterface::UNIT_GRAM, $loadedWeightEmbeddable->getUnit());
        self::assertSame(100.0, $loadedWeightEmbeddable->getValue());
    }

    /**
     * @return EntityInterface
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    private function createTestEntity(): EntityInterface
    {
        return $this->createEntity($this->getCopiedFqn(self::TEST_ENTITY));
    }
}
