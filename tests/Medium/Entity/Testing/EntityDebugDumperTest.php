<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * Class EntityDebugDumperTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Testing
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper
 * @medium
 */
class EntityDebugDumperTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/EntityDebugDumperTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;

    private const VALUE_DECIMAL = '20.10000000000000';
    protected static $buildOnce = true;
    /**
     * @var EntityDebugDumper
     */
    private static $dumper;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$dumper = new EntityDebugDumper();
    }

    /**
     * @test
     */
    public function itRemovesTrailingZerosOnDecimals(): string
    {
        $dump = self::$dumper->dump($this->getEntity());
        self::assertNotContains(self::VALUE_DECIMAL, $dump);

        return $dump;
    }

    private function getEntity(): EntityInterface
    {
        $emailAddressDto = $this->getEntityDtoFactory()
                                ->createEmptyDtoFromEntityFqn(
                                    $this->getCopiedFqn(
                                        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL
                                    )
                                )->setEmailAddress('person@mail.com');

        $personDto = $this->getEntityDtoFactory()
                          ->createEmptyDtoFromEntityFqn($this->getCopiedFqn(self::TEST_ENTITY_FQN))
                          ->setDecimal(self::VALUE_DECIMAL);
        $personDto->getAttributesEmails()->add($emailAddressDto);
        $this->getDataFillerFactory()
             ->getInstanceFromEntityFqn($this->getCopiedFqn(self::TEST_ENTITY_FQN))
             ->updateDtoWithFakeData($personDto);

        $entity = $this->createEntity(
            $this->getCopiedFqn(self::TEST_ENTITY_FQN),
            $personDto
        );

        return $entity;
    }

    /**
     * @test
     * @depends itRemovesTrailingZerosOnDecimals
     *
     * @param string $dump
     */
    public function itConvertsCollectionsToAListOfIDs(string $dump): void
    {
        self::assertStringContainsString(
            '[getAttributesEmails] => Array
        (
            [0] => EntityDebugDumperTest_ItRemovesTrailingZerosOnDecimals_\Entities\Attributes\Email:',
            $dump
        );
    }
}
