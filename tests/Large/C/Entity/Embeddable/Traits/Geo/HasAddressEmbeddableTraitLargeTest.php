<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Embeddable\Traits\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo\HasAddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait
 */
class HasAddressEmbeddableTraitLargeTest extends AbstractLargeTest
{
    public const  WORK_DIR    = self::VAR_PATH .
                                '/' .
                                self::TEST_TYPE_LARGE .
                                '/HasAddressEmbeddableTraitLargeTest';
    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Place';
    protected static $buildOnce = true;
    private          $entityFqn;

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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValuesWithAnAssocArray(): void
    {
        /**
         * @var HasAddressEmbeddableInterface $entity
         */
        $entity = $this->createEntity($this->entityFqn);
        $entity->update(
            new class($this->entityFqn, $entity->getId()) extends AbstractEntityUpdateDto
            {
                public function getAddressEmbeddable()
                {
                    return AddressEmbeddable::create(
                        [
                            AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NUMBER => '1',
                            AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NAME   => '',
                            AddressEmbeddableInterface::EMBEDDED_PROP_STREET       => 'streetname',
                            AddressEmbeddableInterface::EMBEDDED_PROP_CITY         => 'cityname',
                            AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_CODE  => 'ABC 123',
                            AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_AREA  => 'county',
                            AddressEmbeddableInterface::EMBEDDED_PROP_COUNTRY_CODE => 'UK',
                        ]

                    );
                }

            }
        );

        $this->getEntitySaver()->save($entity);

        $loaded = $this->getRepositoryFactory()
                       ->getRepository($this->entityFqn)
                       ->findAll()[0];
        self::assertSame($entity, $loaded);
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValuesWithAnArray(): void
    {
        /**
         * @var HasAddressEmbeddableInterface $entity
         */
        $entity = $this->createEntity($this->entityFqn);
        $entity->update(
            new class($this->entityFqn, $entity->getId()) extends AbstractEntityUpdateDto
            {
                public function getAddressEmbeddable()
                {
                    return AddressEmbeddable::create(
                        [
                            '1',
                            '',
                            'streetname',
                            'cityname',
                            'ABC 123',
                            'county',
                            'UK',
                        ]

                    );
                }

            }
        );

        $this->getEntitySaver()->save($entity);

        $loaded = $this->getRepositoryFactory()
                       ->getRepository($this->entityFqn)
                       ->findAll()[0];
        self::assertSame($entity, $loaded);
    }
}
