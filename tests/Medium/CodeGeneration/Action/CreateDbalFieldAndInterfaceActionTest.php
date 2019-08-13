<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDbalFieldAndInterfaceAction;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

class CreateDbalFieldAndInterfaceActionTest extends AbstractTest
{

    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . AbstractTest::TEST_TYPE_MEDIUM
                            . '/CreateDbalFieldAndInterfaceActionTest';

    private const BASE_FIELD_TRAIT_NS = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Fields\\Traits';
    private const BASE_TRAIT_PATH     = self::WORK_DIR . '/src/Entity/Fields/Traits';
    private const BASE_INTERFACE_PATH = self::WORK_DIR . '/src/Entity/Fields/Interfaces';

    public function provideTypes(): array
    {
        $toMerge   = [];
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_STRING, 'foo', true);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_STRING, 'foo', false);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_BOOLEAN, null, null);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_BOOLEAN, true, null);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_INTEGER, 10, true);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_INTEGER, 100, false);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_INTEGER, null, true);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_FLOAT, 10, null);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_ARRAY, null, null);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_OBJECT, null, null);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_DATETIME, null, null);
        $toMerge[] = $this->getProviderData(MappingHelper::TYPE_TEXT, 'blah', null);

        return array_merge(...$toMerge);
    }

    /**
     * @param string    $mappingHelperCommonType
     * @param mixed     $defaultValue
     * @param bool|null $unique
     *
     * @return array
     */
    private function getProviderData(string $mappingHelperCommonType, $defaultValue, ?bool $unique): array
    {
        $uniqueName  = $unique ? 'Unique' : '';
        $defaultName = (null === $defaultValue) ? (string)$defaultValue : 'null';
        $typeName    = ucfirst(str_replace('\\', '', $mappingHelperCommonType));
        $name        = $uniqueName . ucfirst($defaultName) . $typeName;

        return [
            $name =>
                [
                    self::BASE_FIELD_TRAIT_NS . '\\' . ucfirst($mappingHelperCommonType) . '\\' . $name . 'FieldTrait',
                    $mappingHelperCommonType,
                    $defaultValue,
                    $unique,
                    self::BASE_TRAIT_PATH . '/' . $typeName . '/' . $name . 'FieldTrait.php',
                    self::BASE_INTERFACE_PATH . '/' . $typeName . '/' . $name . 'FieldInterface.php',
                ],
        ];
    }

    /**
     * @test
     * @dataProvider provideTypes
     *
     * @param string    $fqn
     * @param string    $type
     * @param mixed     $defaultValue
     * @param bool|null $unique
     * @param string    $fieldPath
     * @param string    $interfacePath
     */
    public function itCanCreateANewFieldAndInterfaceForEachType(
        string $fqn,
        string $type,
        $defaultValue,
        ?bool $unique,
        string $fieldPath,
        string $interfacePath
    ): void {
        $action = $this->getAction();
        $action->setFieldTraitFqn($fqn);
        $action->setMappingHelperCommonType($type);
        $action->setDefaultValue($defaultValue);
        if (null !== $unique) {
            $action->setIsUnique($unique);
        }
        $action->run();
        self::assertFileExists($fieldPath);
        self::assertFileExists($interfacePath);
    }

    private function getAction(): CreateDbalFieldAndInterfaceAction
    {
        /**
         * @var CreateDbalFieldAndInterfaceAction $action
         */
        $action = $this->container->get(CreateDbalFieldAndInterfaceAction::class)
                                  ->setProjectRootDirectory(self::WORK_DIR)
                                  ->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE);

        return $action;
    }

}
