<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsEnabledFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\NullableStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UniqueStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class FieldGeneratorIntegrationTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator
 */
class FieldGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE_LARGE .
                            '/FieldGeneratorIntegrationTest/';

    private const TEST_ENTITY_CAR = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                    . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Car';

    private const TEST_FIELD_NAMESPACE = self::TEST_PROJECT_ROOT_NAMESPACE
                                         . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

    private const CAR_FIELDS_TO_TYPES = [
        [self::TEST_FIELD_NAMESPACE . '\\Brand', MappingHelper::TYPE_STRING],
        [self::TEST_FIELD_NAMESPACE . '\\EngineCC', MappingHelper::TYPE_INTEGER],
        [self::TEST_FIELD_NAMESPACE . '\\Manufactured', MappingHelper::TYPE_DATETIME],
        [self::TEST_FIELD_NAMESPACE . '\\Mpg', MappingHelper::TYPE_FLOAT],
        [self::TEST_FIELD_NAMESPACE . '\\Description', MappingHelper::TYPE_TEXT],
        [self::TEST_FIELD_NAMESPACE . '\\IsCar', MappingHelper::TYPE_BOOLEAN],
    ];

    private const UNIQUE_FIELDS_TO_TYPES = [
        [self::TEST_FIELD_NAMESPACE . '\\UniqueString', MappingHelper::TYPE_STRING],
        [self::TEST_FIELD_NAMESPACE . '\\UniqueInt', MappingHelper::TYPE_INTEGER],
    ];

    /**
     * @var FieldGenerator
     */
    private $fieldGenerator;
    /**
     * @var EntityFieldSetter
     */
    private $entityFieldSetter;
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function setup()
    {
        parent::setUp();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_CAR);
        $this->fieldGenerator    = $this->getFieldGenerator();
        $this->entityFieldSetter = $this->getFieldSetter();
        $this->namespaceHelper   = $this->container->get(NamespaceHelper::class);
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function archetypeFieldCanBeStandardLibraryField(): void
    {
        foreach (FieldGenerator::STANDARD_FIELDS as $standardField) {
            $fieldFqn = \str_replace(
                [
                    'EdmondsCommerce\\DoctrineStaticMeta',
                    $this->namespaceHelper->getClassShortName($standardField),
                ],
                [
                    self::TEST_PROJECT_ROOT_NAMESPACE,
                    'Copied' . $this->namespaceHelper->getClassShortName($standardField),
                ],
                $standardField
            );
            $this->buildAndCheck(
                $fieldFqn,
                $standardField
            );
        }
    }

    /**
     * Build and then test a field
     *
     * @param string $name
     * @param string $type
     *
     * @param mixed  $default
     * @param bool   $isUnique
     *
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function buildAndCheck(
        string $name,
        string $type,
        $default = null,
        bool $isUnique = false
    ): string {
        $fieldTraitFqn = $this->fieldGenerator->generateField(
            $name,
            $type,
            null,
            $default,
            $isUnique
        );
        $isArchetype   = !\in_array($type, MappingHelper::ALL_DBAL_TYPES, true);
        $this->qaGeneratedCode();
        $interfacePath = $this->getPathFromFqn(
            \str_replace(
                '\\Fields\\Traits\\',
                '\\Fields\\Interfaces\\',
                $this->namespaceHelper->cropSuffix($fieldTraitFqn, 'Trait') . 'Interface'
            )
        );
        $checkFor      = [];
        if (true === $isArchetype) {
            $archetypeBasename = $this->namespaceHelper->basename($type);
            $newBaseName       = $this->namespaceHelper->basename($name);
            if (false === strpos($newBaseName, 'FieldTrait')) {
                $newBaseName .= 'FieldTrait';
            }
            if ($archetypeBasename !== $newBaseName) {
                $checkFor = [
                    $this->getCodeHelper()->consty($archetypeBasename),
                    $this->getCodeHelper()->classy($archetypeBasename),
                    $this->getCodeHelper()->propertyIsh($archetypeBasename),
                ];
            }
        }
        $this->assertNoMissedReplacements($interfacePath, $checkFor);

        $traitPath = $this->getPathFromFqn($fieldTraitFqn);
        $this->assertNoMissedReplacements($traitPath, $checkFor);

        $interfaceContents = file_get_contents($interfacePath);
        $traitContents     = file_get_contents($traitPath);

        if (!$isArchetype && !\in_array($type, [MappingHelper::TYPE_TEXT, MappingHelper::TYPE_STRING], true)) {
            self::assertNotContains(': string', $interfaceContents);
            self::assertNotContains('(string', $interfaceContents);
            self::assertNotContains(': string', $traitContents);
            self::assertNotContains('(string', $traitContents);
            $phpType = MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$type];
            if (null === $default) {
                $phpType = "?$phpType";
            }
            self::assertContains(': ' . $phpType, $interfaceContents);
            self::assertContains('(' . $phpType, $interfaceContents);
            self::assertContains(': ' . $phpType, $traitContents);
            self::assertContains('(' . $phpType, $traitContents);
        }

        self::assertNotContains('public function isIs', $interfaceContents, '', true);
        self::assertNotContains('public function isIs', $traitContents, '', true);
        if ($type === MappingHelper::TYPE_BOOLEAN) {
            self::assertNotContains('public function get', $interfaceContents);
            self::assertNotContains('public function get', $traitContents);
        }

        return $fieldTraitFqn;
    }

    protected function getPathFromFqn(string $fqn): string
    {
        $path = self::WORK_DIR . 'src/Entity/Fields';
        $exp  = explode(
            '\\',
            \substr(
                $fqn,
                strpos(
                    $fqn,
                    '\\Entity\\Fields\\'
                ) + \strlen('\\Entity\\Fields\\')
            )
        );
        foreach ($exp as $item) {
            $path .= '/' . $item;
        }
        $path .= '.php';

        return $path;
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function archetypeFieldCanBeNonStandardLibraryField(): void
    {
        $args         = current(self::CAR_FIELDS_TO_TYPES);
        $archetypeFqn = $this->fieldGenerator->generateField($args[0], $args[1]);
        $this->buildAndCheck(self::TEST_FIELD_NAMESPACE . '\\BrandCopied', $archetypeFqn);
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function fieldCanBeDeeplyNamespaced(): void
    {
        $deeplyNamespaced = self::TEST_FIELD_NAMESPACE . '\\Deeply\\Nested\\String';
        $this->buildAndCheck($deeplyNamespaced, MappingHelper::TYPE_STRING);
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function archetypeFieldCanBeDeeplyNested(): void
    {
        $deeplyNamespaced = self::TEST_FIELD_NAMESPACE . '\\Deeply\\Nested\\StringFieldTrait';
        $this->buildAndCheck($deeplyNamespaced, NullableStringFieldTrait::class);
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function theGeneratedFieldCanHaveTheSameNameAsTheArchetype(): void
    {
        $deeplyNamespaced = self::TEST_FIELD_NAMESPACE . '\\Deeply\\Nested\\NullableString';
        $this->buildAndCheck($deeplyNamespaced, NullableStringFieldTrait::class);
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function archetypeBooleansBeginningWithIsAreHandledProperly(): void
    {
        $deeplyNamespaced = self::TEST_FIELD_NAMESPACE . '\\Deeply\\Nested\\IsBoolean';
        $this->buildAndCheck($deeplyNamespaced, DefaultsEnabledFieldTrait::class);
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function fieldMustContainEntityNamespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fieldGenerator->generateField(
            '\\Blah\\Foop',
            MappingHelper::TYPE_STRING,
            null,
            null,
            true
        );
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function fieldTypeMustBeValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fieldGenerator->generateField(
            self::CAR_FIELDS_TO_TYPES[0][0],
            'invalid',
            null,
            null,
            true
        );
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function phpTypeMustBeValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fieldGenerator->generateField(
            self::CAR_FIELDS_TO_TYPES[0][0],
            MappingHelper::PHP_TYPE_FLOAT,
            'invalid',
            null,
            true
        );
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function defaultTypeMustBeValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fieldGenerator->generateField(
            self::CAR_FIELDS_TO_TYPES[0][0],
            MappingHelper::PHP_TYPE_FLOAT,
            'invalid',
            'clearly not a float',
            true
        );
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function defaultValueIsNormalised(): void
    {
        $defaultValuesToTypes = [
            MappingHelper::TYPE_INTEGER => [
                1,
                '1',
                ' 1',
                ' 1 ',
            ],
            MappingHelper::TYPE_FLOAT   => [
                1,
                1.0,
                '1',
                '1.1',
                ' 1.1 ',
                ' 1.1 ',
            ],
            MappingHelper::TYPE_BOOLEAN => [
                'true',
                'false',
                'TRUE',
                'FALSE',
                ' TRue ',
                ' FaLse ',
            ],
        ];
        $errors               = [];
        foreach ($defaultValuesToTypes as $type => $defaultValues) {
            foreach ($defaultValues as $key => $defaultValue) {
                try {
                    $this->buildAndCheck(
                        self::TEST_FIELD_NAMESPACE . '\\normalisedDefault' . $type . $key,
                        $type,
                        $defaultValue
                    );
                } catch (\Throwable $e) {
                    $errors[] = [
                        'type'    => $type,
                        'default' => $defaultValue,
                        'error'   => $e->getMessage(),
                    ];
                }
            }
        }
        self::assertSame([], $errors, print_r($errors, true));
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function buildFieldsAndSetToEntity(): void
    {
        foreach (self::CAR_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1], null);
            $this->entityFieldSetter->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function buildFieldsWithSuffixAndSetToEntity(): void
    {
        foreach (self::CAR_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0] . FieldGenerator::FIELD_TRAIT_SUFFIX, $args[1], null);
            $this->entityFieldSetter->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function buildNullableFieldsAndSetToEntity(): void
    {
        foreach (self::CAR_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1], null);
            $this->entityFieldSetter->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function buildUniqueFieldsAndSetToEntity(): void
    {
        foreach (self::UNIQUE_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1], null, true);
            $this->entityFieldSetter->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function buildingAnArchetypeThenNormalField(): void
    {
        $this->buildAndCheck(self::TEST_FIELD_NAMESPACE . '\\UniqueName', UniqueStringFieldTrait::class);
        $this->buildAndCheck(self::TEST_FIELD_NAMESPACE . '\\SimpleString', MappingHelper::TYPE_STRING);
        $this->buildAndCheck(self::TEST_FIELD_NAMESPACE . '\\UniqueThing', UniqueStringFieldTrait::class);
    }

    /**
     * @test
     * @large
     * @covers ::generateField
     */
    public function notPossibleToAddDuplicateNamedFieldsToSingleEntity(): void
    {
        $someThing  = self::TEST_FIELD_NAMESPACE . '\\Something\\FooFieldTrait';
        $otherThing = self::TEST_FIELD_NAMESPACE . '\\Otherthing\\FooFieldTrait';
        $this->buildAndCheck($someThing, UniqueStringFieldTrait::class);
        $this->buildAndCheck($otherThing, BusinessIdentifierCodeFieldTrait::class);
        $this->entityFieldSetter->setEntityHasField(self::TEST_ENTITY_CAR, $someThing);
        $this->expectException(\InvalidArgumentException::class);
        $this->entityFieldSetter->setEntityHasField(self::TEST_ENTITY_CAR, $otherThing);
    }
}
