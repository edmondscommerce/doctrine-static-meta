<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @medium
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait
 */
class HasAddressEmbeddableTraitTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_MEDIUM . '/AddressEmbeddableTraitIntegrationTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;
    protected static $buildOnce = true;
    protected static $built     = false;
    private $entity;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
        $entityFqn    = $this->getCopiedFqn(self::TEST_ENTITY);
        $this->entity = $this->createEntity($entityFqn);
    }

    /**
     * @test
     */
    public function theAddressEmbeddableCanBeSettedAndGetted(): void
    {
        $this->entity->update(
            new class ($this->getCopiedFqn(self::TEST_ENTITY), $this->entity->getId()) extends AbstractEntityUpdateDto
            {
                public function getAddressEmbeddable(): AddressEmbeddableInterface
                {

                    $values = [
                        '100',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];

                    return AddressEmbeddable::create($values);
                }
            }
        );
        $expected = '100';
        $actual   = $this->entity->getAddressEmbeddable()->getHouseNumber();
        self::assertSame($expected, $actual);
    }
}
