<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EmailAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\ValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory
 */
class EntityFactoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityFactoryTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\EntityFactoryTestEntity';
    protected static $buildOnce = true;
    private $entityFqn;
    /**
     * @var EntityFactoryInterface
     */
    private $factory;

    public function setup()
    {
        parent::setUp();
        if (false === static::$built) {
            $this->buildOnce();
        }
        $this->setupCopiedWorkDir();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->factory   = new EntityFactory(
            $this->container->get(ValidatorFactory::class),
            $this->getNamespaceHelper(),
            $this->container->get(EntityDependencyInjector::class)
        );
        $this->factory->setEntityManager($this->getEntityManager());
    }

    private function buildOnce()
    {
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_FQN);
        $this->getFieldSetter()->setEntityHasField(
            self::TEST_ENTITY_FQN,
            IsbnFieldTrait::class
        );
        $this->getFieldSetter()->setEntityHasField(
            self::TEST_ENTITY_FQN,
            EmailAddressFieldTrait::class
        );

        static::$built = true;
    }

    /**
     * @test
     * @large
     *      */
    public function itCanCreateAnEmptyEntity(): void
    {
        $entity = $this->factory->create($this->entityFqn);
        self::assertInstanceOf($this->entityFqn, $entity);
    }

    /**
     * @test
     * @large
     *      */
    public function itThrowsAnExceptionIfThereIsAnInvalidProperty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create($this->entityFqn, ['invalidProperty' => true]);
    }

    /**
     * @test
     * @large
     *      */
    public function itCanCreateAnEntityWithValues(): void
    {
        $values = [
            IsbnFieldInterface::PROP_ISBN                  => '978-3-16-148410-0',
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS => 'test@test.com',
        ];
        $entity = $this->factory->create($this->entityFqn, $values);

        self::assertSame($entity->getIsbn(), $values[IsbnFieldInterface::PROP_ISBN]);

        self::assertSame($entity->getEmailAddress(), $values[EmailAddressFieldInterface::PROP_EMAIL_ADDRESS]);
    }

    /**
     * @test
     * @large
     *      */
    public function itCanCreateAnEntitySpecificFactoryAndCanCreateThatEntity(): void
    {
        $entityFactory    = $this->factory->createFactoryForEntity($this->entityFqn);
        $entityFactoryFqn = $this->getNamespaceHelper()->getFactoryFqnFromEntityFqn($this->entityFqn);
        self::assertInstanceOf($entityFactoryFqn, $entityFactory);
        self::assertInstanceOf($this->entityFqn, $entityFactory->create());
    }
}
