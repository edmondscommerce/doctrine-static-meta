<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

class EntityValidatorIntegrationTest extends AbstractTest
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

    private $testEntity;

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_SERVER);
        $this->getFieldSetter()->setEntityHasField(
            self::TEST_ENTITY_SERVER,
            IpAddressFieldTrait::class
        );
        $this->setupCopiedWorkDir();
        $entityFqn        = $this->getCopiedFqn(self::TEST_ENTITY_SERVER);
        $this->testEntity = $this->createEntity($entityFqn);
    }

    public function testIsValid(): void
    {
        foreach (self::VALID_IP_ADDRESSES as $ipAddress) {
            $this->testEntity->setIpAddress($ipAddress);
            self::assertSame($ipAddress, $this->testEntity->getIpAddress());
        }
    }

    public function testInvalid(): void
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
}
