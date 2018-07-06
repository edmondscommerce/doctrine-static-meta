<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use PHPUnit\Framework\TestCase;

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
        $address  = new AddressEmbeddable();
        foreach ($expected as $property => $value) {
            $setter = "set$property";
            $getter = "get$property";
            $address->$setter($value);
            $actual[$property] = $address->$getter();
        }
        self::assertSame($expected, $actual);
    }
}
