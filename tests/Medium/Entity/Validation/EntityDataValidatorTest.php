<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidator
 * @medium
 */
class EntityDataValidatorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/EntityValidatorTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                    TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS;

    public const VALID_IP_ADDRESSES = [
        '192.136.234.145',
    ];

    public const INVALID_IP_ADDRESSES = [
        'cheese',
        '192.136',
    ];
    protected static $buildOnce = true;
    private          $testEntity;
    private          $testDto;
    /**
     * @var EntityDataValidator
     */
    private $validator;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->testDto    = $this->createTestDto();
        $this->testEntity = $this->createTestEntity($this->testDto);
        $this->validator  = $this->container->get(EntityDataValidator::class);
    }

    private function createTestDto(): DataTransferObjectInterface
    {
        $dto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn(self::TEST_ENTITY_FQN);
        $dto->setShortIndexedRequiredString('foo');

        return $dto;
    }

    private function createTestEntity(DataTransferObjectInterface $dto)
    {
        $testEntityFqn = self::TEST_ENTITY_FQN;

        return $this->getEntityFactory()->create($testEntityFqn, $dto);
    }

    /**
     * @test
     */
    public function setValidOnDto(): void
    {
        foreach (self::VALID_IP_ADDRESSES as $ipAddress) {
            $this->testDto->setIpAddress($ipAddress);
            self::assertSame($ipAddress, $this->testDto->getIpAddress());
        }
        self::assertTrue($this->validator->setDto($this->testDto)->isValid());
    }

    /**
     * @test
     */
    public function getValidationExceptionOnInvalidDtos(): void
    {
        foreach (self::INVALID_IP_ADDRESSES as $ipAddress) {
            $caughtException = null;
            try {
                $this->testDto->setIpAddress($ipAddress);
                $this->validator->setDto($this->testDto)->validate();
            } catch (ValidationException $exception) {
                $caughtException = $exception;
            }
            self::assertInstanceOf(ValidationException::class, $caughtException);
        }
    }

    /**
     * @test
     */
    public function doNotGetValidationExceptionOnValidDtos(): void
    {
        $this->validator->setDto($this->testDto);
        foreach (self::VALID_IP_ADDRESSES as $ipAddress) {
            $caughtException = null;
            try {
                $this->testDto->setIpAddress($ipAddress);
                $this->validator->validate();
            } catch (ValidationException $exception) {
                $caughtException = $exception;
            }
            self::assertNull($caughtException);
        }
    }

    /**
     * @test
     */
    public function itReturnsFalseOnIsValidForInvalidDtos(): void
    {
        $this->validator->setDto($this->testDto);
        foreach (self::INVALID_IP_ADDRESSES as $ipAddress) {
            $this->testDto->setIpAddress($ipAddress);
            self::assertFalse($this->validator->isValid());
        }
    }

    /**
     * @test
     */
    public function itReturnsTrueOnIsValidForValidDtos(): void
    {
        $this->validator->setDto($this->testDto);
        foreach (self::VALID_IP_ADDRESSES as $ipAddress) {
            $this->testDto->setIpAddress($ipAddress);
            self::assertTrue($this->validator->isValid());
        }
    }

    /**
     * @test
     */
    public function itReturnsFalseOnIsValidForInvalidEntities(): void
    {
        $this->validator->setEntity($this->testEntity);
        $reflection = new ReflectionClass($this->testEntity);
        $property   = $reflection->getProperty('ipAddress');
        $property->setAccessible(true);
        foreach (self::INVALID_IP_ADDRESSES as $ipAddress) {
            $property->setValue($this->testEntity, $ipAddress);
            self::assertFalse($this->validator->isValid());
        }
    }

    /**
     * @test
     */
    public function itReturnsTrueOnIsValidForValidEntities(): void
    {
        $this->validator->setEntity($this->testEntity);
        $reflection = new ReflectionClass($this->testEntity);
        $property   = $reflection->getProperty('ipAddress');
        $property->setAccessible(true);
        foreach (self::VALID_IP_ADDRESSES as $ipAddress) {
            $property->setValue($this->testEntity, $ipAddress);
            self::assertTrue($this->validator->isValid(), $this->validator->getErrorsAsString());
        }
    }
}
