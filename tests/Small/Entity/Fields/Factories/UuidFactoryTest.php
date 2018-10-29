<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Entity\Fields\Factories;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory
 * @small
 */
class UuidFactoryTest extends TestCase
{
    /**
     * @var UuidFactory
     */
    private static $factory;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setUpBeforeClass()
    {
        $factory = Uuid::getFactory();
        if ($factory instanceof \Ramsey\Uuid\UuidFactory) {
            self::$factory = new UuidFactory($factory);

            return;
        }
        throw new \LogicException('This should never happen');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function itCanGenerateOrderedTimeUuids(): void
    {
        $actual = self::$factory->getOrderedTimeUuid();
        self::assertSame(1, $actual->getVersion());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function itCanGenerateStandardUuids(): void
    {
        $actual = self::$factory->getUuid();
        self::assertSame(4, $actual->getVersion());
    }
}
