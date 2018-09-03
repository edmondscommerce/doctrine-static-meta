<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Entity\Embeddable\Objects\Financial;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ImplementNotifyChangeTrackingPolicyInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\MockEntityFactory;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class MoneyEmbeddableTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class MoneyEmbeddableTest extends TestCase
{
    /**
     * @var MoneyEmbeddable
     */
    private $embeddable;

    public function setup()
    {
        $entity           = MockEntityFactory::createMockEntity();
        $this->embeddable = new MoneyEmbeddable();
        $this->embeddable->setOwningEntity($entity);
        //using reflection as would happen with Doctrine hydrating an object
        $reflection = new \ReflectionObject($this->embeddable);
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
     * @covers ::getMoney()
     */
    public function itCanGetTheMoneyObject(): void
    {
        $actual = $this->embeddable->getMoney();
        self::assertNotFalse($actual);
    }

    /**
     * @test
     * @small
     * @covers ::setMoney()
     */
    public function itCanSetANewMoneyObject(): void
    {
        $newMoney = new Money(200, new Currency('GBP'));
        $this->embeddable->setMoney($newMoney);
        $expected = $newMoney;
        $actual   = $this->embeddable->getMoney();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::addMoney()
     */
    public function itCanAddToTheMoney(): void
    {
        $toAdd = new Money(100, new Currency('GBP'));
        $this->embeddable->addMoney($toAdd);
        $expected = '200';
        $actual   = $this->embeddable->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::subtractMoney()
     */
    public function itCanSubtractFromTheMoney(): void
    {
        $toSubtract = new Money(60, new Currency('GBP'));
        $this->embeddable->subtractMoney($toSubtract);
        $expected = '40';
        $actual   = $this->embeddable->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }
}
