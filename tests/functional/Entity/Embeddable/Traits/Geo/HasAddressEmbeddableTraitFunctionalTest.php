<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo\HasAddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;

class HasAddressEmbeddableTraitFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = self::VAR_PATH.'/'.self::TEST_TYPE.'/HasAddressEmbeddableTraitFunctionalTest';

    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\Place';

    private $entityFqn;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(self::TEST_ENTITY, HasAddressEmbeddableTrait::class);
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues()
    {
        $entity = new $this->entityFqn;
        $entity->getAddressEmbeddable()
               ->setCity('the city')
               ->setCountryCode('ABC')
               ->setHouseName('foo')
               ->setHouseNumber('345')
               ->setPostalArea('cheese land')
               ->setPostalCode('ABC 123')
               ->setStreet('streety street');

        $this->getEntitySaver()->save($entity);

        $loaded = $this->getEntityManager()
                       ->getRepository($this->entityFqn)
                       ->findAll()[0];
        $this->assertSame($entity, $loaded);
    }
}
