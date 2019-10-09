<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use InvalidArgumentException;

/**
 * Class MappingHelperIntegrationTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @covers \EdmondsCommerce\DoctrineStaticMeta\MappingHelper
 */
class MappingHelperTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/MappingHelperIntegrationTest/';

    protected const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                           . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                                           . '\\MappingEntity';

    /**
     * @test
     * @medium
     *      * @throws DoctrineStaticMetaException
     */
    public function invalidBoolThrowsException(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'Bool';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(InvalidArgumentException::class);
        MappingHelper::setSimpleBooleanFields(['test'], $builder, 3);
    }

    /**
     * @test
     * @medium
     *      * @throws DoctrineStaticMetaException
     */
    public function invalidStringThrowsException(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'String';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(InvalidArgumentException::class);
        MappingHelper::setSimpleStringFields(['test'], $builder, 3);
    }

    /**
     * @test
     * @medium
     *      * @throws DoctrineStaticMetaException
     */
    public function invalidDateTimeThrowsException(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'DateTime';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(InvalidArgumentException::class);
        MappingHelper::setSimpleDatetimeFields(['test'], $builder, 3);
    }

    /**
     * @test
     * @medium
     *      * @throws DoctrineStaticMetaException
     */
    public function invalidFloatThrowsException(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'Float';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(InvalidArgumentException::class);
        MappingHelper::setSimpleFloatFields(['test'], $builder, 'cheese');
    }

    /**
     * @test
     * @medium
     *      * @throws DoctrineStaticMetaException
     */
    public function invalidDecimalThrowsException(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'Decimal';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(InvalidArgumentException::class);
        MappingHelper::setSimpleDecimalFields(['test'], $builder, 'cheese');
    }

    /**
     * @test
     * @medium
     *      * @throws DoctrineStaticMetaException
     */
    public function invalidTextThrowsException(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'Text';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(InvalidArgumentException::class);
        MappingHelper::setSimpleTextFields(['test'], $builder, true);
    }

    /**
     * @test
     * @medium
     *      * @throws DoctrineStaticMetaException
     */
    public function invalidIntegerThrowsException(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'Integer';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(InvalidArgumentException::class);
        MappingHelper::setSimpleIntegerFields(['test'], $builder, 'cheese');
    }
}
