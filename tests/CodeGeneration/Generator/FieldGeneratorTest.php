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

    public function testGenerateTextField()
    {
        $name = 'someLongDescription';
        $type = MappingHelper::TYPE_TEXT;
        $this->buildAndCheck($name, $type);
    }

    public function testGenerateStringField()
    {
        $name = 'brandName';
        $type = MappingHelper::TYPE_STRING;
        $this->buildAndCheck($name, $type);
    }

    public function testGenerateDateField()
    {
        $name = 'someDate';
        $type = MappingHelper::TYPE_DATETIME;
        $this->buildAndCheck($name, $type);
    }

    public function testGenerateIntField()
    {
        $name = 'countOfThings';
        $type = MappingHelper::TYPE_INTEGER;
        $this->buildAndCheck($name, $type);
    }

    public function testGenerateFloatField()
    {
        $name = 'currencyWithPrecision';
        $type = MappingHelper::TYPE_FLOAT;
        $this->buildAndCheck($name, $type);
    }

    /**
     * Build and then test a field
     *
     * @param string $name
     * @param string $type
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function buildAndCheck(string $name, string $type)
    {
        $this->fieldGenerator->generateField($name, $type);

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
    }
}
