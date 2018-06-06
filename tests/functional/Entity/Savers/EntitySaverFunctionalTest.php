<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class EntitySaverFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/EntitySaverFunctionalTest';

    private const TEST_ENTITIES = [
        self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\TestEntityOne',
        self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\Deeply\\Nested\\TestEntityTwo',
    ];

    private const TEST_FIELDS = [
        self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entity\\Fields\\Traits\\NameFieldTrait',
        self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entity\\Fields\\Traits\\FooFieldTrait',
    ];

    public function setup()
    {
        parent::setup();
        $fieldGenerator = $this->getFieldGenerator();
        foreach (self::TEST_FIELDS as $fieldFqn) {
            $fieldGenerator->generateField($fieldFqn, MappingHelper::TYPE_STRING);
        }
        $entityGenerator = $this->getEntityGenerator();
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityGenerator->generateEntity($entityFqn);
            foreach (self::TEST_FIELDS as $fieldFqn) {
                $fieldGenerator->setEntityHasField($entityFqn, $fieldFqn);
            }
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
    }



    protected function findAllEntity(string $entityFqn)
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->getRepository($entityFqn)->findAll();
    }


    public function testItCanSaveAndRemoveASingleEntity()
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITIES));
        $entity    = new $entityFqn();
        $entity->setName('blah');
        $entity->setfoo('bar');
        $saver = $this->getEntitySaver();
        $saver->save($entity);
        $loaded = $this->findAllEntity($entityFqn)[0];
        $this->assertSame($entity->getName(), $loaded->getName());
        $this->assertSame($entity->getFoo(), $loaded->getFoo());
        $saver->remove($loaded);
        $this->assertSame([], $this->findAllEntity($entityFqn));
    }

    public function testItCanSaveAndRemoveMultipleEntities()
    {
        $entities = [];
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            foreach (range(0, 9) as $num) {
                $entities[$entityFqn.$num] = new $entityFqn();
                $entities[$entityFqn.$num]->setName('blah');
                $entities[$entityFqn.$num]->setfoo('bar');
            }
        }
        $saver = $this->getEntitySaver();
        $saver->saveAll($entities);
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $loaded    = $this->findAllEntity($entityFqn);
            $this->assertCount(10, $loaded);
            foreach (range(0, 9) as $num) {
                $this->assertSame($entities[$entityFqn.$num]->getName(), $loaded[$num]->getName());
                $this->assertSame($entities[$entityFqn.$num]->getFoo(), $loaded[$num]->getFoo());
            }
        }

        $saver->removeAll($entities);
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $this->assertSame([], $this->findAllEntity($entityFqn));
        }
    }
}
