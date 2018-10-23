<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Embeddable\Traits\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo\HasAddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;

class HasAddressEmbeddableTraitLargeTest extends AbstractLargeTest
{
    public const  WORK_DIR    = self::VAR_PATH .
                                '/' .
                                self::TEST_TYPE_LARGE .
                                '/HasAddressEmbeddableTraitLargeTest';
    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Place';
    protected static $buildOnce = true;
    private $entityFqn;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
            $this->getEntityEmbeddableSetter()
                 ->setEntityHasEmbeddable(self::TEST_ENTITY, HasAddressEmbeddableTrait::class);
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->recreateDtos();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues(): void
    {
        /**
         * @var HasAddressEmbeddableInterface $entity
         */
        $entity = $this->createEntity($this->entityFqn);
        $entity->getAddressEmbeddable()
               ->setCity('the city')
               ->setCountryCode('ABC')
               ->setHouseName('foo')
               ->setHouseNumber('345')
               ->setPostalArea('cheese land')
               ->setPostalCode('ABC 123')
               ->setStreet('streety street');

        $this->getEntitySaver()->save($entity);

        $loaded = $this->getRepositoryFactory()
                       ->getRepository($this->entityFqn)
                       ->findAll()[0];
        self::assertSame($entity, $loaded);
    }
}
