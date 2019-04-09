<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Money\Currency;
use Money\Money;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HasMoneyEmbeddableTraitTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/HasMoneyEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;
    protected static $buildOnce = true;
    protected static $built     = false;
    /**
     * @var EntityInterface
     */
    private $entity;

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
     */
    public function theEntityWithTheTraitCanSetTheMoneyObject(): void
    {
        $this->entity->update(
            new class($this->getCopiedFqn(self::TEST_ENTITY), $this->entity->getId()) extends AbstractEntityUpdateDto
            {
                public function getMoneyEmbeddable(): MoneyEmbeddableInterface
                {
                    return new MoneyEmbeddable(
                        new Money(
                            100,
                            new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE)
                        )
                    );
                }
            }
        );

        $this->theEntityWithTheTraitCanGetTheMoneyObject('100');
    }

    /**
     * @test
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
     */
    public function theEntityWithTheTraitCanSetTheMoneyEmbeddable(): void
    {
        $this->entity->update(
            new class($this->getCopiedFqn(self::TEST_ENTITY), $this->entity->getId()) extends AbstractEntityUpdateDto
            {
                public function getMoneyEmbeddable(): MoneyEmbeddableInterface
                {
                    $money = new Money(200, new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE));

                    return new MoneyEmbeddable($money);
                }
            }
        );
        $this->theEntityWithTheTraitCanGetTheMoneyObject('200');
    }
}
