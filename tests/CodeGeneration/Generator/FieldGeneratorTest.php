<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class FieldGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/FieldGeneratorTest/';

    private const TEST_ENTITY_CAR = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                    .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Car';

    private const TEST_FIELD_NAMESPACE = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                         .AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

    private const CAR_FIELDS_TO_TYPES = [
        [self::TEST_FIELD_NAMESPACE.'\\Brand', MappingHelper::TYPE_STRING],
        [self::TEST_FIELD_NAMESPACE.'\\EngineCC', MappingHelper::TYPE_INTEGER],
        [self::TEST_FIELD_NAMESPACE.'\\Manufactured', MappingHelper::TYPE_DATETIME],
        [self::TEST_FIELD_NAMESPACE.'\\Mpg', MappingHelper::TYPE_FLOAT],
        [self::TEST_FIELD_NAMESPACE.'\\Description', MappingHelper::TYPE_TEXT],
        [self::TEST_FIELD_NAMESPACE.'\\IsCar', MappingHelper::TYPE_BOOLEAN],
    ];

    private const UNIQUE_FIELDS_TO_TYPES = [
        [self::TEST_FIELD_NAMESPACE.'\\UniqueString', MappingHelper::TYPE_STRING],
        [self::TEST_FIELD_NAMESPACE.'\\UniqueInt', MappingHelper::TYPE_INTEGER],
    ];

    /**
     * @var FieldGenerator
     */
    private $fieldGenerator;

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_CAR);
        $this->fieldGenerator = $this->getFieldGenerator();
    }

    public function testFieldMustContainEntityNamespace()
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

    public function testFieldTypeMustBeValid()
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

    public function testPHPTypeMustBeValid()
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

    public function testDefaultTypeMustBeValid()
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
     * Default values passed in by CLI could come through quite dirty and need to be normalised     *
     */
    public function testDefaultValueIsNormalised()
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
                        self::TEST_FIELD_NAMESPACE.'\\normalisedDefault'.$type.$key,
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
        $this->assertSame([], $errors, print_r($errors, true));
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

        $this->qaGeneratedCode();
        $basePath        = self::WORK_DIR.'src/Entity/Fields/';
        $namespaceHelper = new NamespaceHelper();
        $basename        = $namespaceHelper->basename($name);

        $interfacePath = $basePath.'Interfaces/'.Inflector::classify($basename).'FieldInterface.php';
        $this->assertNoMissedReplacements($interfacePath);

        $traitPath = $basePath.'Traits/'.Inflector::classify($basename).'FieldTrait.php';
        $this->assertNoMissedReplacements($traitPath);

        $interfaceContents = file_get_contents($interfacePath);
        $traitContents     = file_get_contents($traitPath);

        if (!\in_array($type, [MappingHelper::TYPE_TEXT, MappingHelper::TYPE_STRING], true)) {
            $this->assertNotContains(': string', $interfaceContents);
            $this->assertNotContains('(string', $interfaceContents);
            $this->assertNotContains(': string', $traitContents);
            $this->assertNotContains('(string', $traitContents);
            $phpType = MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$type];
            if (null === $default) {
                $phpType = "?$phpType";
            }
            $this->assertContains(': '.$phpType, $interfaceContents);
            $this->assertContains('('.$phpType, $interfaceContents);
            $this->assertContains(': '.$phpType, $traitContents);
            $this->assertContains('('.$phpType, $traitContents);
        }

        if ($type === MappingHelper::TYPE_BOOLEAN) {
            $this->assertNotContains('public function get', $interfaceContents);
            $this->assertNotContains('public function isIs', $interfaceContents, '', true);
            $this->assertNotContains('public function get', $traitContents);
            $this->assertNotContains('public function isIs', $traitContents, '', true);
        }

        return $fieldTraitFqn;
    }

    public function testBuildFieldsAndSetToEntity()
    {
        foreach (self::CAR_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1], null);
            $this->fieldGenerator->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }

    public function testBuildNullableFieldsAndSetToEntity()
    {
        foreach (self::CAR_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1], null);
            $this->fieldGenerator->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }

    public function testBuildUniqueFieldsAndSetToEntity()
    {
        foreach (self::UNIQUE_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1], null, true);
            $this->fieldGenerator->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }
}
