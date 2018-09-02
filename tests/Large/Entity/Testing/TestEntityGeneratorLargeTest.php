<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\FullProjectBuildLargeTest;

/**
 * @large
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator
 */
class TestEntityGeneratorLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            self::TEST_TYPE_LARGE .
                            '/TestEntityGeneratorLargeTest';

    private const TEST_ENTITIES = FullProjectBuildLargeTest::TEST_ENTITIES;

    private const TEST_RELATIONS = FullProjectBuildLargeTest::TEST_RELATIONS;

    private const TEST_FIELD_FQN_BASE = FullProjectBuildLargeTest::TEST_FIELD_NAMESPACE_BASE . '\\Traits';

    protected static $buildOnce = true;

    public function setup(): void
    {
        parent::setup();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
    }

    /**
     * @test
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator
     * @return EntityInterface
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanGenerateASingleEntity(): EntityInterface
    {
        $entityFqn           = current(self::TEST_ENTITIES);
        $entityFqn           = $this->getCopiedFqn($entityFqn);
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $entityManager       = $this->getEntityManager();
        $entity              = $testEntityGenerator->generateEntity($entityManager, $entityFqn);
        $entityManager->persist($entity);
        $entityManager->flush();
        self::assertTrue(true);

        return $entity;
    }

    protected function getTestEntityGenerator(string $entityFqn): TestEntityGenerator
    {
        $testedEntityReflectionClass = new \ts\Reflection\ReflectionClass($entityFqn);

        return new \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator(
            [],
            $testedEntityReflectionClass,
            $this->container->get(EntitySaverFactory::class),
            $this->container->get(EntityValidatorFactory::class),
            AbstractEntityTest::SEED
        );
    }

    /**
     * @test
     * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator
     *
     * @param EntityInterface $originalEntity
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @depends itCanGenerateASingleEntity
     */
    public function itCanGenerateAnOffsetEntity(EntityInterface $originalEntity): void
    {
        $entityFqn           = \get_class($originalEntity);
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $entityManager       = $this->getEntityManager();
        $newEntity           = $testEntityGenerator->generateEntity($entityManager, $entityFqn, 1);
        self::assertNotEquals($this->dump($newEntity), $this->dump($originalEntity));
    }

    /**
     * @test
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function itGeneratesEntitiesAndAssociatedEntities(): void
    {
        $entities      = [];
        $entityManager = $this->getEntityManager();
        $limit         = ($this->isQuickTests() ? 2 : null);
        foreach (self::TEST_ENTITIES as $key => $entityFqn) {
            if ($limit !== null && $key === $limit) {
                break;
            }
            $entityFqn           = $this->getCopiedFqn($entityFqn);
            $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
            $entity              = $testEntityGenerator->generateEntity($entityManager, $entityFqn);
            self::assertInstanceOf($entityFqn, $entity);
            $testEntityGenerator->addAssociationEntities($entityManager, $entity);
            $entities[] = $entity;
        }
        $this->getEntitySaver()->saveAll($entities);
        self::assertTrue(true);
    }


    /**
     * @test
     * @covers ::generateEntities
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanGenerateMultipleEntities(): void
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITIES));
        $count     = $this->isQuickTests() ? 2 : 100;
        $actual    = $this->getTestEntityGenerator($entityFqn)->generateEntities(
            $this->getEntityManager(),
            $entityFqn,
            $count
        );
        self::assertCount($count, $actual);
        self::assertInstanceOf($entityFqn, current($actual));
    }

    /**
     * @test
     * @covers ::create
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanCreateAnEmptyEntityUsingTheFactory(): void
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITIES));
        $entity    = $this->getTestEntityGenerator($entityFqn)->create($this->getEntityManager());
        self::assertInstanceOf($entityFqn, $entity);
    }

    /**
     * @test
     * @covers ::create
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanCreateAnEntityWithValuesSet(): void
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITIES));
        $values    = [
            'string' => 'this has been set',
        ];
        $entity    = $this->getTestEntityGenerator($entityFqn)->create($this->getEntityManager(), $values);
        self::assertSame($values['string'], $entity->getString());
    }
}
