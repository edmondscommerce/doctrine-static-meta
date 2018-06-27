<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

class EntityValidatorIntegrationTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/EntityValidatorTest';

    public const TEST_ENTITY_SERVER = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                      .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Server';

    public const TEST_ENTITY_SERVER_COPIED = self::TEST_ENTITY_SERVER.'Copied';

    public const VALID_IP_ADDRESSES = [
        '192.136.234.145',
    ];

    public const INVALID_IP_ADDRESSES = [
        'cheese',
        '192.136',
    ];

    /**
     * @var IpAddressFieldInterface
     */
    private $testEntity;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_SERVER);
        $this->getFieldSetter()->setEntityHasField(
            self::TEST_ENTITY_SERVER,
            IpAddressFieldTrait::class
        );
        file_put_contents(
            self::WORK_DIR.'/src/Entities/ServerCopied.php',
            str_replace(
                [
                    'class Server',
                ],
                [
                    'class ServerCopied',
                ],
                file_get_contents(self::WORK_DIR.'/src/Entities/Server.php')
            )
        );
        $entityFqn        = self::TEST_ENTITY_SERVER_COPIED;
        $this->testEntity = $this->createEntity($entityFqn);
    }

    public function testIsValid()
    {
        foreach (self::VALID_IP_ADDRESSES as $ipAddress) {
            $this->testEntity->setIpAddress($ipAddress);
            $this->assertSame($ipAddress, $this->testEntity->getIpAddress());
        }
    }

    public function testInvalid()
    {
        foreach (self::INVALID_IP_ADDRESSES as $ipAddress) {
            $exception = null;
            try {
                $this->testEntity->setIpAddress($ipAddress);
            } catch (ValidationException $exception) {
            }
            $this->assertInstanceOf(ValidationException::class, $exception);
        }
    }
}
