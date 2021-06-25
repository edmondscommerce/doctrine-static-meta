<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Entity\Embeddable\Objects\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\MockEntityFactory;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * Class MoneyEmbeddableTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class MoneyEmbeddableTest extends TestCase
{
    /**
     * @var MoneyEmbeddableInterface
     */
    private MoneyEmbeddableInterface $embeddable;

    public function setup():void
    {
        $entity           = MockEntityFactory::createMockEntity();
        $this->embeddable = MoneyEmbeddable::create(MoneyEmbeddable::DEFAULTS);
        $this->embeddable->setOwningEntity($entity);
        //using reflection as would happen with Doctrine hydrating an object
        $reflection = new ReflectionObject($this->embeddable);
        $propAmount = $reflection->getProperty(MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT);
        $propAmount->setAccessible(true);
        $propAmount->setValue($this->embeddable, 100);
        $propCurrencyCode = $reflection->getProperty(MoneyEmbeddableInterface::EMBEDDED_PROP_CURRENCY_CODE);
        $propCurrencyCode->setAccessible(true);
        $propCurrencyCode->setValue($this->embeddable, 'GBP');
    }

    /**
     * @test
     * @small
     *      */
    public function itCanGetTheMoneyObject(): void
    {
        $actual = $this->embeddable->getMoney();
        self::assertNotFalse($actual);
    }
}
