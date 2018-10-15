<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Money\Currency;
use Money\Money;

class HasMoneyEmbeddableTraitTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/HasMoneyEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;
    protected static $buildOnce = true;
    protected static $built     = false;
    private          $entity;

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
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable::setMoney()
     */
    public function theEntityWithTheTraitCanSetTheMoneyObject(): void
    {
        $money = new Money(100, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->setMoney($money);

        $this->theEntityWithTheTraitCanGetTheMoneyObject('100');
    }

    /**
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait::getMoneyEmbeddable()
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable::getMoney()
     *
     * @param string $expectedAmount
     */
    public function theEntityWithTheTraitCanGetTheMoneyObject(
        string $expectedAmount = MoneyEmbeddableInterface::DEFAULT_AMOUNT
    ): void {
        $money    = $this->entity->getMoneyEmbeddable()->getMoney();
        $expected = [
            'amount'       => $expectedAmount,
            'currencyCode' => MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE,
        ];
        $actual   = [
            'amount'       => $money->getAmount(),
            'currencyCode' => $money->getCurrency()->getCode(),
        ];
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait::setMoneyEmbeddable()
     */
    public function theEntityWithTheTraitCanSetTheMoneyEmbeddable(): void
    {
        $money           = new Money(200, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $moneyEmbeddable = new MoneyEmbeddable();
        $moneyEmbeddable->setMoney($money);
        $this->entity->update(new class($moneyEmbeddable) implements DataTransferObjectInterface
        {
            /**
             * @var MoneyEmbeddable
             */
            private $moneyEmbeddable;

            public function __construct(MoneyEmbeddable $moneyEmbeddable)
            {

                $this->moneyEmbeddable = $moneyEmbeddable;
            }

            public function getMoneyEmbeddable(): MoneyEmbeddable
            {
                return $this->moneyEmbeddable;
            }
        });
        $this->theEntityWithTheTraitCanGetTheMoneyObject('200');
    }

    /**
     * @test
     * @medium
     * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable::addMoney()
     */
    public function theEntityWithTheTraitCanAddAMoneyObjectToTheCurrentMoneyObject(): void
    {
        $money = new Money(300, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->addMoney($money);
        $this->theEntityWithTheTraitCanGetTheMoneyObject('300');
        $money = new Money(100, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->addMoney($money);
        $this->theEntityWithTheTraitCanGetTheMoneyObject('400');
    }

    /**
     * @test
     * @medium
     * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable::subtractMoney()
     */
    public function theEntityWithTheTraitCanSubtractAMoneyObjectToTheCurrentMoneyObject(): void
    {
        $money = new Money(1, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->subtractMoney($money);
        $this->theEntityWithTheTraitCanGetTheMoneyObject('-1');
        $money = new Money(2, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->subtractMoney($money);
        $this->theEntityWithTheTraitCanGetTheMoneyObject('-3');
    }
}
