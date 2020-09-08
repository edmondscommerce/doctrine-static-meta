<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\D\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\RepositoryFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver
 */
class EntitySaverLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntitySaverLargeTest';

    private const TEST_ENTITIES = [
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL,
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_SIMPLE,
    ];

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
    }

    public function testItCanSaveAndRemoveASingleEntity(): void
    {
        $entityFqn = $this->getCopiedFqn(self::TEST_ENTITIES[0]);
        $entity    = $this->createEntity($entityFqn);
        $entity->update(
            $this->getEntityDtoFactory()
                 ->createDtoFromEntity($entity)
                 ->setString('blah')
                 ->setFloat(2.2)
        );
        $saver = $this->getEntitySaver();
        $saver->save($entity);
        $loaded = $this->findAllEntity($entityFqn)[0];
        self::assertSame($entity->getString(), $loaded->getString());
        self::assertSame($entity->getFloat(), $loaded->getFloat());
        $saver->remove($loaded);
        self::assertSame([], $this->findAllEntity($entityFqn));
    }

    protected function findAllEntity(string $entityFqn): array
    {
        return $this->container->get(RepositoryFactory::class)->getRepository($entityFqn)->findAll();
    }

    public function testItCanSaveAndRemoveMultipleEntities(): void
    {
        $entities      = [];
        $numToGenerate = 10;
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $generator =
                $this->getTestEntityGeneratorFactory()->createForEntityFqn($entityFqn)->getGenerator($numToGenerate);
            foreach ($generator as $key => $entity) {
                $entity->update(
                    $this->getEntityDtoFactory()
                         ->createDtoFromEntity($entity)
                         ->setString('blah')
                         ->setFloat(2.2)
                );
                $entities[$entityFqn . $key] = $entity;
            }
        }
        $saver = $this->getEntitySaver();
        $saver->saveAll($entities);
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $loaded    = $this->findAllEntity($entityFqn);
            self::assertCount($numToGenerate, $loaded);
            foreach (range(0, 9) as $num) {
                self::assertSame($entities[$entityFqn . $num]->getString(), $loaded[$num]->getString());
                self::assertSame($entities[$entityFqn . $num]->getFloat(), $loaded[$num]->getFloat());
            }
        }

        $saver->removeAll($entities);
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            self::assertSame([], $this->findAllEntity($entityFqn));
        }
    }
}
