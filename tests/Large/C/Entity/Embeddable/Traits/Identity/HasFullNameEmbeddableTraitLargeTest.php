<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Embeddable\Traits\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity\HasFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait
 */
class HasFullNameEmbeddableTraitLargeTest extends AbstractLargeTest
{
    public const  WORK_DIR    = self::VAR_PATH .
                                '/' .
                                self::TEST_TYPE_LARGE .
                                '/HasAddressEmbeddableTraitLargeTest';
    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Place';

    private $entityFqn;

    public function setup()
    {
        parent::setUp();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(self::TEST_ENTITY, HasFullNameEmbeddableTrait::class);
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->recreateDtos();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues(): void
    {
        /**
         * @var HasFullNameEmbeddableInterface $entity
         */
        $entity = $this->createEntity($this->entityFqn);
        $entity->update(new class($this->entityFqn, $entity->getId()) extends AbstractEntityUpdateDto
        {
            public function getFullNameEmbeddable(): \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface
            {
                return FullNameEmbeddable::create(
                    [
                        'Mr',
                        'Aklhasd',
                        [
                            'Blah',
                            'Foo',
                            'Cheese',
                        ],
                        'TestyTest',
                        'Jr',
                    ]
                );
            }
        });


        $this->getEntitySaver()->save($entity);

        $loaded = $this->getRepositoryFactory()
                       ->getRepository($this->entityFqn)
                       ->findAll()[0];
        self::assertSame($entity, $loaded);
    }
}
