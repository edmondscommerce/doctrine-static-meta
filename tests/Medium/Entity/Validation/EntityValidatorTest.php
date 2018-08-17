<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidator
 */
class EntityValidatorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/EntityValidatorTest';

    public const TEST_ENTITY_SERVER = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                      . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Server';

    public const TEST_ENTITY_SERVER_COPIED = self::TEST_ENTITY_SERVER . 'Copied';

    public const VALID_IP_ADDRESSES = [
        '192.136.234.145',
    ];

    public const INVALID_IP_ADDRESSES = [
        'cheese',
        '192.136',
    ];
    protected static $buildOnce = true;
    private $testEntity;

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function setup()
    {
        parent::setup();
        if (false === self::$built) {
            $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_SERVER);
            $this->getFieldSetter()->setEntityHasField(
                self::TEST_ENTITY_SERVER,
                IpAddressFieldTrait::class
            );
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $entityFqn        = $this->getCopiedFqn(self::TEST_ENTITY_SERVER);
        $this->testEntity = $this->createEntity($entityFqn);
    }

    /**
     * @test
     * @medium
     * @covers ::validateProperty ::isValid
     */
    public function setValid(): void
    {
        foreach (self::VALID_IP_ADDRESSES as $ipAddress) {
            $this->testEntity->setIpAddress($ipAddress);
            self::assertSame($ipAddress, $this->testEntity->getIpAddress());
        }
        self::assertTrue($this->testEntity->isValid());
    }

    /**
     * @test
     * @medium
     * @covers ::validateProperty
     */
    public function setInvalid(): void
    {
        foreach (self::INVALID_IP_ADDRESSES as $ipAddress) {
            $exception = null;
            try {
                $this->testEntity->setIpAddress($ipAddress);
            } catch (ValidationException $exception) {
            }
            self::assertInstanceOf(ValidationException::class, $exception);
        }
    }

    /**
     * @test
     * @medium
     * @covers ::validate ::isValid
     */
    public function validateWhenInvalid()
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $property   = $reflection->getProperty('ipAddress');
        $property->setAccessible(true);
        $property->setValue($this->testEntity, current(self::INVALID_IP_ADDRESSES));
        self::assertFalse($this->testEntity->isValid());
    }
}
