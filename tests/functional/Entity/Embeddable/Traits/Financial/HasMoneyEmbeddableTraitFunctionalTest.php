<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\HasMoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
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
    }

    protected function copyAndSetEntityFqn(): void
    {
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues(): void
    {
        $this->copyAndSetEntityFqn();
        /**
         * @var HasMoneyEmbeddableInterface $entity
         */
        $entity = $this->createEntity($this->entityFqn);
        $entity->getMoneyEmbeddable()
               ->setMoney(new Money(
                   100,
                   new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE)
               ));
        $this->getEntitySaver()->save($entity);

        $loaded   = $this->getEntityManager()->getRepository($this->entityFqn)->findAll()[0];
        $expected = '100';
        $actual   = $loaded->getMoneyEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function thereCanBeMultipleOfTheSameArchetypeInAnEntity(): void
    {
        $priceTraitFqn = $this->getArchetypeEmbeddableGenerator()
                              ->createFromArchetype(
                                  MoneyEmbeddable::class,
                                  'PriceEmbeddable'
                              );
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(self::TEST_ENTITY, $priceTraitFqn);
        $this->copyAndSetEntityFqn();
        $entity = $this->createEntity($this->entityFqn);
        $entity->getMoneyEmbeddable()
               ->setMoney(new Money(
                   100,
                   new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE)
               ));
        $entity->getPriceEmbeddable()
               ->setMoney(new Money(
                   200,
                   new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE)
               ));
        $this->getEntitySaver()->save($entity);

        $loaded   = $this->getEntityManager()->getRepository($this->entityFqn)->findAll()[0];
        $expected = '100';
        $actual   = $loaded->getMoneyEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
        $expected = '200';
        $actual   = $loaded->getPriceEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }
}
