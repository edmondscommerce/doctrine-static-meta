<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Entity\Embeddable\Objects\Identity;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ImplementNotifyChangeTrackingPolicyInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;
use PHPUnit\Framework\TestCase;

/**
 * Class FullNameEmbeddableTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class FullNameEmbeddableTest extends TestCase
{
    /**
     * @test
     * @small
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable::<public>
     */
    public function itCanGetAndSetAllTheThings(): void
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
        $embeddable->setOwningEntity(new class() implements ImplementNotifyChangeTrackingPolicyInterface
        {
            private static $metaData;

            public function __construct()
            {
                self::$metaData = new ClassMetadata('anon');
            }

            use ImplementNotifyChangeTrackingPolicy;
        });
        $actual = [];
        foreach ($expected as $property => $value) {
            $setter = "set$property";
            $getter = "get$property";
            $embeddable->$setter($value);
            $actual[$property] = $embeddable->$getter();
        }
        self::assertSame($expected, $actual);
        self::assertSame('Sir Roger Michael Stephen O\'Neil Marmaduke The Third', $embeddable->getFormatted());
    }
}
