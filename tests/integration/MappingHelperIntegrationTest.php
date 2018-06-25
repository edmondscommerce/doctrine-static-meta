<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpProperty;

/**
 * Class MappingHelperIntegrationTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class MappingHelperIntegrationTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/MappingHelperIntegrationTest/';

    protected const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                           .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
                                           .'\\MappingEntity';


    public function testInvalidBoolThrowsException()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'Bool';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(\InvalidArgumentException::class);
        MappingHelper::setSimpleBooleanFields(['test'], $builder, 3);
    }

    public function testInvalidStringThrowsException()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'String';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(\InvalidArgumentException::class);
        MappingHelper::setSimpleStringFields(['test'], $builder, 3);
    }

    public function testInvalidDateTimeThrowsException()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'DateTime';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(\InvalidArgumentException::class);
        MappingHelper::setSimpleDatetimeFields(['test'], $builder, 3);
    }

    public function testInvalidFloatThrowsException()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'Float';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(\InvalidArgumentException::class);
        MappingHelper::setSimpleFloatFields(['test'], $builder, 'cheese');
    }

    public function testInvalidDecimalThrowsException()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'Decimal';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(\InvalidArgumentException::class);
        MappingHelper::setSimpleDecimalFields(['test'], $builder, 'cheese');
    }

    public function testInvalidTextThrowsException()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'Text';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(\InvalidArgumentException::class);
        MappingHelper::setSimpleTextFields(['test'], $builder, true);
    }

    public function testInvalidIntegerThrowsException()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'Integer';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);
        $builder = new ClassMetadataBuilder(new ClassMetadataInfo($entityFqn));

        $this->expectException(\InvalidArgumentException::class);
        MappingHelper::setSimpleIntegerFields(['test'], $builder, 'cheese');
    }
}
