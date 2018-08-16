<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EmailAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;

class EntityFactoryTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE . '/EntityFactoryTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\TestEntity';

    private $entityFqn;

    /**
     * @var EntityFactory
     */
    private $factory;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_FQN);
        $this->getFieldSetter()->setEntityHasField(
            self::TEST_ENTITY_FQN,
            IsbnFieldTrait::class
        );
        $this->getFieldSetter()->setEntityHasField(
            self::TEST_ENTITY_FQN,
            EmailAddressFieldTrait::class
        );
        $this->setupCopiedWorkDir();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->factory   = new EntityFactory($this->container->get(EntityValidatorFactory::class));
        $this->factory->setEntityManager($this->getEntityManager());
    }

    /**
     * @test
     *
     */
    public function itCanCreateAnEmptyEntity(): void
    {
        $entity = $this->factory->create($this->entityFqn);
        self::assertInstanceOf($this->entityFqn, $entity);
    }

    /**
     * @test
     *
     */
    public function itThrowsAnExceptionIfThereIsAnInvalidProperty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create($this->entityFqn, ['invalidProperty' => true]);
    }

    /**
     * @test
     *
     */
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
}
