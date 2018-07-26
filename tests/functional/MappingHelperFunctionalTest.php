<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpProperty;

/**
 * Class MappingHelperFunctionalTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class MappingHelperFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE . '/MappingHelperFunctionalTest/';

    protected const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                           . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                                           . '\\MappingEntity';

    protected const TEST_FIELD_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                          . '\\Entity\\Fields\\Traits\\';

    protected const TEST_FIELD_DEFAULT_VALUES = [
        MappingHelper::TYPE_STRING  => '',
        MappingHelper::TYPE_FLOAT   => 0.0,
        MappingHelper::TYPE_DECIMAL => 0.0,
        MappingHelper::TYPE_INTEGER => 0,
        MappingHelper::TYPE_TEXT    => '',
        MappingHelper::TYPE_BOOLEAN => false,
    ];

    public function testGenerateOneOfEachFieldTypeUsingSetSimpleFields(): void
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE . 'Two';
        $this->getEntityGenerator()
             ->generateEntity($entityFqn);

        $fieldsToTypes = [];
        foreach (MappingHelper::COMMON_TYPES as $commonType) {
            $fieldsToTypes[$commonType . 'Field'] = $commonType;
        }
        $entityClassFile = self::WORK_DIR . '/src/Entities/MappingEntityTwo.php';
        $entityClass     = PhpClass::fromFile($entityClassFile);
        $entityClass->addUseStatement(MappingHelper::class);
        $fieldsToTypesCode = var_export($fieldsToTypes, true);
        $entityClass->setMethod(
            (new PhpMethod(UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META . 'SimpleFields'))
                ->setParameters([(new PhpParameter('builder'))->setType('\\' . ClassMetadataBuilder::class)])
                ->setStatic(true)
                ->setVisibility('private')
                ->setBody(<<<PHP
        MappingHelper::setSimpleFields(
            $fieldsToTypesCode
        , \$builder);
PHP
                )
        );
        foreach (array_keys($fieldsToTypes) as $field) {
            $entityClass->setProperty((new PhpProperty($field))->setVisibility('private'));
        }
        $this->container->get(CodeHelper::class)->generate($entityClass, $entityClassFile);
        $this->qaGeneratedCode();
        $this->setupCopiedWorkDirAndCreateDatabase();
        $meta = $this->getEntityManager()->getClassMetadata($this->getCopiedFqn($entityFqn));
        self::assertCount(count(MappingHelper::COMMON_TYPES) + 1, $meta->getFieldNames());
    }

    protected function getDefaultValue($type)
    {
        if (!isset(self::TEST_FIELD_DEFAULT_VALUES[$type])) {
            return null;
        }

        return self::TEST_FIELD_DEFAULT_VALUES[$type];
    }
}
