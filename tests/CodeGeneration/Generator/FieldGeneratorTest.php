<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class FieldGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/FieldGeneratorTest/';

    private const TEST_ENTITY_CAR = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                    .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Car';

    private const CAR_FIELDS_TO_TYPES = [
        ['brand', MappingHelper::TYPE_STRING],
        ['engineCC', MappingHelper::TYPE_INTEGER],
        ['manufactured', MappingHelper::TYPE_DATETIME],
        ['mpg', MappingHelper::TYPE_FLOAT],
        ['description', MappingHelper::TYPE_TEXT],
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
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function buildAndCheck(string $name, string $type)
    {
        $fieldTraitFqn = $this->fieldGenerator->generateField($name, $type);

        $this->qaGeneratedCode();
        $basePath      = self::WORK_DIR.'src/Entity/Fields/';
        $interfacePath = $basePath.'Interfaces/'.Inflector::classify($name).'FieldInterface.php';
        $this->assertNoMissedReplacements($interfacePath);

        $traitPath = $basePath.'Traits/'.Inflector::classify($name).'FieldTrait.php';
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
            $fieldFqn = $this->buildAndCheck(...$args);
            $this->fieldGenerator->setEntityHasField(self::TEST_ENTITY_CAR, $fieldFqn);
        }
        $this->qaGeneratedCode();
    }
}
