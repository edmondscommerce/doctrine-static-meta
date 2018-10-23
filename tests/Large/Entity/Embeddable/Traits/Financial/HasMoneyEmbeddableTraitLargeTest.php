<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Embeddable\Traits\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Money\Currency;
use Money\Money;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait
 */
class HasMoneyEmbeddableTraitLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/HasMoneyEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;
    protected static $buildOnce = true;
    protected static $built     = false;
    private          $entityFqn;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
    }

    /**
     * @test
     */
    public function theEntityCanBeSavedAndLoadedWithCorrectValues(): void
    {
        $this->copyAndSetEntityFqn();
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

        $loaded->getMoneyEmbeddable()
               ->setMoney(new Money(
                              200,
                              new Currency(MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE)
                          ));
        $reloaded = $this->saveAndReload($loaded);
        $expected = '200';
        $actual   = $reloaded->getMoneyEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }

    private function copyAndSetEntityFqn(): void
    {
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    private function saveAndReload(EntityInterface $entity)
    {
        $this->getEntitySaver()->save($entity);
        $repo = $this->getRepositoryFactory()->getRepository(
            $entity::getDoctrineStaticMeta()->getReflectionClass()->getName()
        );

        return $repo->findAll()[0];
    }

    /**
     * @test
     */
    public function thereCanBeMultipleOfTheSameArchetypeInAnEntity(): void
    {
        $priceTraitFqn = $this->getArchetypeEmbeddableGenerator()
                              ->setPathToProjectRoot(self::WORK_DIR)
                              ->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE)
                              ->createFromArchetype(
                                  MoneyEmbeddable::class,
                                  'PriceEmbeddable'
                              );
        $this->getEntityEmbeddableSetter()
             ->setPathToProjectRoot(self::WORK_DIR)
             ->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE)
             ->setEntityHasEmbeddable(self::TEST_ENTITY, $priceTraitFqn);
        $this->copyAndSetEntityFqn();
        $this->recreateDtos();
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
        $repo     = $this->getRepositoryFactory()->getRepository($this->entityFqn);
        $loaded   = $repo->findAll()[0];
        $expected = '100';
        $actual   = $loaded->getMoneyEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
        $expected = '200';
        $actual   = $loaded->getPriceEmbeddable()->getMoney()->getAmount();
        self::assertSame($expected, $actual);
    }
}
