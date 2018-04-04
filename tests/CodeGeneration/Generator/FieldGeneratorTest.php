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
                                            . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

    private const CAR_FIELDS_TO_TYPES = [
        [self::TEST_FIELD_NAMESPACE.'\\Brand',        MappingHelper::TYPE_STRING],
        [self::TEST_FIELD_NAMESPACE.'\\EngineCC',     MappingHelper::TYPE_INTEGER],
        [self::TEST_FIELD_NAMESPACE.'\\Manufactured', MappingHelper::TYPE_DATETIME],
        [self::TEST_FIELD_NAMESPACE.'\\Mpg',          MappingHelper::TYPE_FLOAT],
        [self::TEST_FIELD_NAMESPACE.'\\Description',  MappingHelper::TYPE_TEXT],
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

    /**
     * Build and then test a field
     *
     * @param string $name
     * @param string $type
     *
     * @param bool $isNullable
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function buildAndCheck(string $name, string $type, bool $isNullable)
    {
        $this->fieldGenerator->setIsNullable($isNullable);
        $fieldTraitFqn = $this->fieldGenerator->generateField($name, $type);

        $this->qaGeneratedCode();
        $basePath        = self::WORK_DIR.'src/Entity/Fields/';
        $namespaceHelper = new NamespaceHelper();
        $basename        = $namespaceHelper->basename($name);

        $interfacePath   = $basePath.'Interfaces/'.Inflector::classify($basename).'FieldInterface.php';
        $this->assertNoMissedReplacements($interfacePath);

        $traitPath = $basePath.'Traits/'.Inflector::classify($basename).'FieldTrait.php';
        $this->assertNoMissedReplacements($traitPath);

        if (!\in_array($type, [MappingHelper::TYPE_TEXT, MappingHelper::TYPE_STRING], true)) {
            $this->assertNotContains(': string', file_get_contents($interfacePath));
            $this->assertNotContains('(string', file_get_contents($interfacePath));
            $this->assertNotContains(': string', file_get_contents($traitPath));
            $this->assertNotContains('(string', file_get_contents($traitPath));
        }

        return $fieldTraitFqn;
    }

    public function testBuildFieldsAndSetToEntity()
    {
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_CAR);
        foreach (self::CAR_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1],false);
            $this->fieldGenerator->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }

    public function testBuildNullableFieldsAndSetToEntity()
    {
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_CAR);
        foreach (self::CAR_FIELDS_TO_TYPES as $args) {
            $fieldFqn = $this->buildAndCheck($args[0], $args[1], true);
            $this->fieldGenerator->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }
}
