<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use PHPUnit\Framework\TestCase;

class FullNameEmbeddableTest extends TestCase
{
    /**
     * @test
     * @small
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable::<public>
     */
    public function itCanGetAndSetAllTheThings()
    {
        $expected   = [
            FullNameEmbeddableInterface::EMBEDDED_PROP_TITLE       => 'Sir',
            FullNameEmbeddableInterface::EMBEDDED_PROP_FIRSTNAME   => 'Roger',
            FullNameEmbeddableInterface::EMBEDDED_PROP_MIDDLENAMES => [
                'Michael',
                'Stephen',
                "O'Neil",
            ],
            FullNameEmbeddableInterface::EMBEDDED_PROP_LASTNAME    => 'Marmaduke',
            FullNameEmbeddableInterface::EMBEDDED_PROP_SUFFIX      => 'The Third',
        ];
        $embeddable = new FullNameEmbeddable();
        $actual     = [];
        foreach ($expected as $property => $value) {
            $setter = "set$property";
            $getter = "get$property";
            $embeddable->$setter($value);
            $actual[$property] = $embeddable->$getter();
        }
        $this->assertSame($expected, $actual);
        $this->assertSame('Sir Roger Michael Stephen O\'Neil Marmaduke The Third', $embeddable->getFormatted());
    }
}
