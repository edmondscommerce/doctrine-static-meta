<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use Money\Currency;
use Money\Money;

class HasMoneyEmbeddableTraitFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/HasMoneyEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\BankAccount';

    private $entityFqn;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(self::TEST_ENTITY, HasMoneyEmbeddableTrait::class);
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues()
    {
        $entity = new $this->entityFqn;
        $entity->getMoneyEmbeddable()
               ->setMoney(new Money(
                              100,
                              new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE)
                          )
               );
        $this->getEntitySaver()->save($entity);

        $loaded   = $this->getEntityManager()->getRepository($this->entityFqn)->findAll()[0];
        $expected = '100';
        $actual   = $loaded->getMoneyEmbeddable()->getMoney()->getAmount();
        $this->assertSame($expected, $actual);

    }
}
