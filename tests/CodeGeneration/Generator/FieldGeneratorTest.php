<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

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

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function testGenerateTextField()
    {
        $this->fieldGenerator->generateField(
            'brandName',
            MappingHelper::TYPE_STRING
        );
        $expected = '';
        $actual   = '';
        $this->assertEquals($expected, $actual);
        $this->qaGeneratedCode();
    }
}
