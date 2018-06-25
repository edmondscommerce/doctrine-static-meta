<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;

class HasFullNameEmbeddableTraitFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = self::VAR_PATH.'/'.self::TEST_TYPE.'/HasAddressEmbeddableTraitFunctionalTest';

    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\Place';

    private $entityFqn;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(self::TEST_ENTITY, HasFullNameEmbeddableTrait::class);
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues()
    {
        $entity = $this->createEntity($this->entityFqn);
        $entity->getFullNameEmbeddable()
               ->setTitle('Mr')
               ->setFirstname('Aklhasd')
               ->setMiddleNames([
                                    'Blah',
                                    'Foo',
                                    'Cheese',
                                ])
               ->setLastName('TestyTest')
               ->setSuffix('Jr');

        $this->getEntitySaver()->save($entity);

        $loaded = $this->getEntityManager()
                       ->getRepository($this->entityFqn)
                       ->findAll()[0];
        $this->assertSame($entity, $loaded);
    }
}
