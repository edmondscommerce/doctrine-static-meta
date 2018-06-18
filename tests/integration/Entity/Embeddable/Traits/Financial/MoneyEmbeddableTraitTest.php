<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\HasMoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use Money\Currency;
use Money\Money;

class MoneyEmbeddableTraitTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/MoneyEmbeddableTraitTest';

    private const TEST_ENTITY = AbstractIntegrationTest::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\BankAccount';

    /**
     * @var HasMoneyEmbeddableInterface
     */
    private $entity;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(self::TEST_ENTITY, HasMoneyEmbeddableTrait::class);
        $this->setupCopiedWorkDir();
        $entityFqn    = $this->getCopiedFqn(self::TEST_ENTITY);
        $this->entity = new $entityFqn;

    }

    protected function getEntityEmbeddableSetter(): EntityEmbeddableSetter
    {
        return $this->container->get(EntityEmbeddableSetter::class);
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter
     */
    public function generatedCodePassesQa()
    {
        $this->qaGeneratedCode();
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
    ) {
        $money    = $this->entity->getMoneyEmbeddable()->getMoney();
        $expected = [
            'amount'       => $expectedAmount,
            'currencyCode' => MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE,
        ];
        $actual   = [
            'amount'       => $money->getAmount(),
            'currencyCode' => $money->getCurrency()->getCode(),
        ];
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable::setMoney()
     */
    public function theEntityWithTheTraitCanSetTheMoneyObject()
    {
        $money = new Money(100, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->setMoney($money);

        $this->theEntityWithTheTraitCanGetTheMoneyObject('100');
    }

    /**
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait::setMoneyEmbeddable()
     */
    public function theEntityWithTheTraitCanSetTheMoneyEmbeddable()
    {
        $money           = new Money(200, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $moneyEmbeddable = new MoneyEmbeddable();
        $moneyEmbeddable->setMoney($money);
        $this->entity->setMoneyEmbeddable($moneyEmbeddable);
        $this->theEntityWithTheTraitCanGetTheMoneyObject('200');
    }

    /**
     * @test
     * @medium
     * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable::addMoney()
     */
    public function theEntityWithTheTraitCanAddAMoneyObjectToTheCurrentMoneyObject()
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
    public function theEntityWithTheTraitCanSubtractAMoneyObjectToTheCurrentMoneyObject()
    {
        $money = new Money(1, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->subtractMoney($money);
        $this->theEntityWithTheTraitCanGetTheMoneyObject('-1');
        $money = new Money(2, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));
        $this->entity->getMoneyEmbeddable()->subtractMoney($money);
        $this->theEntityWithTheTraitCanGetTheMoneyObject('-3');
    }
}
