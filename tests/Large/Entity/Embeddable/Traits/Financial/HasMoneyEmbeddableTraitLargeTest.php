<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Embeddable\Traits\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\HasMoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Money\Currency;
use Money\Money;

class HasMoneyEmbeddableTraitLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/HasMoneyEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\BankAccount';

    private $entityFqn;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(self::TEST_ENTITY, HasMoneyEmbeddableTrait::class);
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait
     * @return HasMoneyEmbeddableInterface
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues(): HasMoneyEmbeddableInterface
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

        $expected = '100';
        $loaded   = $this->saveAndReload($entity);
        $actual   = $loaded->getMoneyEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);

        return $loaded;
    }

    protected function copyAndSetEntityFqn(): void
    {
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    protected function saveAndReload(EntityInterface $entity)
    {
        $this->getEntitySaver()->save($entity);
        /**
         * @var AbstractEntityRepository $repo
         */
        $repo   = $this->getEntityManager()->getRepository($this->entityFqn);
        $loaded = $repo->findAll()[0];

        return $loaded;
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait
     *
     * @param HasMoneyEmbeddableInterface $entity
     */
    public function theEntityEmbeddableCanBeUpdatedAndSavedAndLoaded(HasMoneyEmbeddableInterface $entity): void
    {
        $entity->getMoneyEmbeddable()
               ->setMoney(new Money(
                              200,
                              new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE)
                          ));
        $reloaded = $this->saveAndReload($entity);
        $expected = '200';
        $actual   = $reloaded->getMoneyEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait
     * @return void
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

        /**
         * @var AbstractEntityRepository $repo
         */
        $repo     = $this->getEntityManager()->getRepository($this->entityFqn);
        $loaded   = $repo->findAll()[0];
        $expected = '100';
        $actual   = $loaded->getMoneyEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
        $expected = '200';
        $actual   = $loaded->getPriceEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }
}
