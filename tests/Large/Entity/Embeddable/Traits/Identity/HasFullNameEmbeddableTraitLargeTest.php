<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Embeddable\Traits\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity\HasFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;

class HasFullNameEmbeddableTraitLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/HasAddressEmbeddableTraitLargeTest';

    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Place';

    private $entityFqn;

    public function setUp(): void    {
        parent::setUp();
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
    public function theEntityCanBeSavedAndLoadedWithCorrectValues(): void
    {
        /**
         * @var HasFullNameEmbeddableInterface $entity
         */
        $entity = $this->createEntity($this->entityFqn);
        $entity->getFullNameEmbeddable()
               ->setTitle('Mr')
               ->setFirstName('Aklhasd')
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
        self::assertSame($entity, $loaded);
    }
}
