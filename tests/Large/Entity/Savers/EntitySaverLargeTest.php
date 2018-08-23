<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @large
 */
class EntitySaverLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntitySaverLargeTest';

    private const TEST_ENTITIES = [
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\EntitySaverLargeTestEntityOne',
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Deeply\\Nested\\EntitySaverLargeTestEntityTwo',
    ];

    private const TEST_FIELDS = [
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Fields\\Traits\\NameFieldTrait',
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Fields\\Traits\\FooFieldTrait',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $fieldGenerator = $this->getFieldGenerator();
        foreach (self::TEST_FIELDS as $fieldFqn) {
            $fieldGenerator->generateField($fieldFqn, MappingHelper::TYPE_STRING);
        }
        $entityGenerator = $this->getEntityGenerator();
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityGenerator->generateEntity($entityFqn);
            foreach (self::TEST_FIELDS as $fieldFqn) {
                $this->getFieldSetter()->setEntityHasField($entityFqn, $fieldFqn);
            }
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
    }

    public function testItCanSaveAndRemoveASingleEntity(): void
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITIES));
        $entity    = $this->createEntity($entityFqn);
        $entity->setName('blah');
        $entity->setfoo('bar');
        $saver = $this->getEntitySaver();
        $saver->save($entity);
        $loaded = $this->findAllEntity($entityFqn)[0];
        self::assertSame($entity->getName(), $loaded->getName());
        self::assertSame($entity->getFoo(), $loaded->getFoo());
        $saver->remove($loaded);
        self::assertSame([], $this->findAllEntity($entityFqn));
    }

    protected function findAllEntity(string $entityFqn): array
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->getRepository($entityFqn)->findAll();
    }

    public function testItCanSaveAndRemoveMultipleEntities(): void
    {
        $entities = [];
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            foreach (range(0, 9) as $num) {
                $entities[$entityFqn . $num] = $this->createEntity($entityFqn);
                $entities[$entityFqn . $num]->setName('blah');
                $entities[$entityFqn . $num]->setfoo('bar');
            }
        }
        $saver = $this->getEntitySaver();
        $saver->saveAll($entities);
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $loaded    = $this->findAllEntity($entityFqn);
            self::assertCount(10, $loaded);
            foreach (range(0, 9) as $num) {
                self::assertSame($entities[$entityFqn . $num]->getName(), $loaded[$num]->getName());
                self::assertSame($entities[$entityFqn . $num]->getFoo(), $loaded[$num]->getFoo());
            }
        }

        $saver->removeAll($entities);
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            self::assertSame([], $this->findAllEntity($entityFqn));
        }
    }
}
