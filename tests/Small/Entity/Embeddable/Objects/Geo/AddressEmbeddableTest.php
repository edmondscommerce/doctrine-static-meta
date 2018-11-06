<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Entity\Embeddable\Objects\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\MockEntityFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class AddressEmbeddableTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AddressEmbeddableTest extends TestCase
{
    /**
     * @test
     * @small
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable
     */
    public function itCanSetAndGetAllTheThings(): void
    {
        $expected = [
            AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NUMBER => '1',
            AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NAME   => 'home',
            AddressEmbeddableInterface::EMBEDDED_PROP_STREET       => 'streety street',
            AddressEmbeddableInterface::EMBEDDED_PROP_CITY         => 'Bradford',
            AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_AREA  => 'Shipley',
            AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_CODE  => 'BD17 7DB',
            AddressEmbeddableInterface::EMBEDDED_PROP_COUNTRY_CODE => 'GBR',
        ];
        $actual   = [];

        $address = AddressEmbeddable::create($expected);
        $address->setOwningEntity(MockEntityFactory::createMockEntity());
        foreach ($expected as $property => $value) {
            $getter = "get$property";
            $actual[$property] = $address->$getter();
        }
        self::assertSame($expected, $actual);
    }
}
