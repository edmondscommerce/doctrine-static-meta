<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory
 */
class DtoFactoryTest extends AbstractTest
{
    public const  WORK_DIR    = self::VAR_PATH . self::TEST_TYPE_MEDIUM . '/DtoFactoryTest';
    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER;
    protected static $buildOnce = true;
    protected static $built     = false;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     */
    public function itCanCreateDtoFromEntityFqn(): void
    {
        $actual = $this->getFactory()->createEmptyDtoFromEntityFqn($this->getTestEntityFqn());
        $this->getDataFillerFactory()
             ->getInstanceFromEntityFqn($this->getTestEntityFqn())
             ->updateDtoWithFakeData($actual);
        $expected = $this->getNamespaceHelper()->getEntityDtoFqnFromEntityFqn($this->getTestEntityFqn());
        self::assertInstanceOf($expected, $actual);
        $this->assertDtoIsValid($actual);
    }

    public function getFactory(): DtoFactory
    {
        return $this->container->get(DtoFactory::class);
    }


    private function getTestEntityFqn(): string
    {
        return $this->getCopiedFqn(self::TEST_ENTITY);
    }

    private function assertDtoIsValid(DataTransferObjectInterface $dto): void
    {
        self::assertInstanceOf(UuidInterface::class, $dto->getId());
        $this->getEntityValidator()->setDto($dto)->validate();
    }

    private function getEntityValidator(): EntityDataValidator
    {
        return $this->container->get(EntityDataValidatorFactory::class)->buildEntityDataValidator();
    }

    /**
     * @test
     */
    public function itCanCreateDtoFromEntityInstance(): void
    {
        $dto = $this->getFactory()->createEmptyDtoFromEntityFqn($this->getTestEntityFqn());
        $this->getDataFillerFactory()
             ->getInstanceFromEntityFqn($this->getTestEntityFqn())
             ->updateDtoWithFakeData($dto);
        $this->assertDtoIsValid($dto);
        $entity   = $this->createEntity($this->getTestEntityFqn(), $dto);
        $actual   = $this->getFactory()->createDtoFromEntity($entity);
        $expected = $this->getNamespaceHelper()->getEntityDtoFqnFromEntityFqn($this->getTestEntityFqn());
        self::assertInstanceOf($expected, $actual);
        $this->assertDtoIsValid($actual);
    }
}
