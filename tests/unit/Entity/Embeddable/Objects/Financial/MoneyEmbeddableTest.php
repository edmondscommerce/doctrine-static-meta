<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\MoneyEmbeddableInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class MoneyEmbeddableTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable
 */
class MoneyEmbeddableTest extends TestCase
{
    /**
     * @var MoneyEmbeddable
     */
    private $embeddable;

    public function setup()
    {
        $this->embeddable = new MoneyEmbeddable();
        //using reflection as would happen with Doctrine hydrating an object
        $reflection = new \ReflectionObject($this->embeddable);
        $reflection->getProperty(MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT)
                   ->setAccessible(true)
                   ->setValue($this->embeddable, 100);
        $reflection->getProperty(MoneyEmbeddableInterface::EMBEDDED_PROP_CURRENCY_CODE)
                   ->setAccessible(true)
                   ->setValue($this->embeddable, 'GBP');
    }

    /**
     * @test
     * @small
     * @covers ::getMoney()
     */
    public function itCanGetTheMoneyObject()
    {
        $actual = $this->embeddable->getMoney();
        $this->assertNotFalse($actual);
    }

    /**
     * @test
     * @small
     * @covers ::setMoney()
     */
    public function itCanSetANewMoneyObject()
    {
        $newMoney = new Money(200, new Currency('GBP'));
        $this->embeddable->setMoney($newMoney);
        $expected = $newMoney;
        $actual   = $this->embeddable->getMoney();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::addMoney()
     */
    public function itCanAddToTheMoney()
    {
        $toAdd = new Money(100, new Currency('GBP'));
        $this->embeddable->addMoney($toAdd);
        $expected = 200;
        $actual   = $this->embeddable->getMoney()->getAmount();
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::subtractMoney()
     */
    public function itCanSubtractFromTheMoney()
    {
        $toSubtract = new Money(60, new Currency('GBP'));
        $this->embeddable->subtractMoney($toSubtract);
        $expected = 40;
        $actual   = $this->embeddable->getMoney()->getAmount();
        $this->assertSame($expected, $actual);
    }
}
