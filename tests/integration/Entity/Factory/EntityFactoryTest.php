<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;

class EntityFactoryTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/EntityFactoryTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\TestEntity';

    private $entityFqn;

    /**
     * @var EntityFactory
     */
    private $factory;


    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_FQN);
        foreach (FieldGenerator::STANDARD_FIELDS as $fieldFqn) {
            $this->getFieldSetter()->setEntityHasField(self::TEST_ENTITY_FQN, $fieldFqn);
        }
        $this->setupCopiedWorkDir();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->factory   = $this->container->get(EntityFactory::class);
    }

    /**
     * @test
     * @medium
     */
    public function itCanCreateAnEmptyEntity()
    {
        $entity = $this->factory->create($this->entityFqn);
        $this->assertInstanceOf($this->entityFqn, $entity);
    }

    /**
     * @test
     * @medium
     */
    public function itThrowsAnExceptionIfThereIsAnInvalidProperty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create($this->entityFqn, ['invalidProperty' => true]);
    }

    /**
     * @test
     * @medium
     */
    public function itCanCreateAnEntityWithValues()
    {
        $values = [
            IsbnFieldInterface::PROP_ISBN                  => '978-3-16-148410-0',
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS => 'test@test.com',
        ];
        $entity = $this->factory->create($this->entityFqn, $values);

        $this->assertSame($entity->getIsbn(), $values[IsbnFieldInterface::PROP_ISBN]);

        $this->assertSame($entity->getEmailAddress(), $values[EmailAddressFieldInterface::PROP_EMAIL_ADDRESS]);
    }
}
