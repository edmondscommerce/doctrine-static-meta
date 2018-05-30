<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class EntitySaverFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::WORK_DIR.'/'.self::TEST_TYPE.'/EntitySaverFunctionalTest';

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

    /**
     * @param EntityInterface $entity
     *
     * @return EntitySaverInterface
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    private function getEntitySaver(EntityInterface $entity): EntitySaverInterface
    {
        /**
         * @var EntitySaverFactory $entitySaverFactory
         */
        $entitySaverFactory = $this->container->get(EntitySaverFactory::class);

        return $entitySaverFactory->getSaverForEntity($entity);

    }

    protected function findAllEntity(string $entityFqn)
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->getRepository($entityFqn)->findAll();
    }


    public function testItCanSaveAndRemoveASingleEntity()
    {
        $entityFqn = current(self::TEST_ENTITIES);
        $entity    = new $entityFqn();
        $entity->setName('blah');
        $entity->setfoo('bar');
        $saver = $this->getEntitySaver($entity);
        $saver->save($entity);
        $loaded = $this->findAllEntity($entityFqn)[0];
        $this->assertSame($entity->getName(), $loaded->getName());
        $this->assertSame($entity->getFoo(), $loaded->getFoo());
        $saver->remove($loaded);
        $this->assertSame([], $this->findAllEntity($entityFqn));
    }
}
