<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\B\Builder;

use EdmondsCommerce\DoctrineStaticMeta\Builder\Builder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Builder\Builder
 */
class BuilderTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/BuilderTest/';

    protected const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                           . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                                           . '\\BuilderTestEntity';

    protected const TEST_ENTITY_ONE = self::TEST_ENTITY_FQN_BASE . '\\EntityOne';
    protected const TEST_ENTITY_TWO = self::TEST_ENTITY_FQN_BASE . '\\EntityTwo';
    protected const TEST_ENTITIES   = [
        self::TEST_ENTITY_ONE,
        self::TEST_ENTITY_TWO,
    ];

    protected const TEST_FIELD_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                          . '\\Entity\\Fields\\Traits';

    protected const TEST_FIELD_ENITY_ONE_ENUM = self::TEST_FIELD_FQN_BASE . '\\EntityOne\\EnumFieldTrait';

    protected const TEST_FIELDS_ENTITY_ONE = [
        self::TEST_FIELD_ENITY_ONE_ENUM => EnumFieldTrait::class,
    ];

    protected static $buildOnce = true;
    /**
     * @var Builder
     */
    private $builder;

    public function setUp()
    {
        parent::setUp();
        if (true !== self::$built) {
            foreach (self::TEST_ENTITIES as $entityFqn) {
                $this->getEntityGenerator()->generateEntity($entityFqn);
            }
            foreach (self::TEST_FIELDS_ENTITY_ONE as $fieldFqn => $fieldType) {
                $this->getFieldGenerator()->generateField($fieldFqn, $fieldType);
                $this->getFieldSetter()->setEntityHasField(self::TEST_ENTITY_ONE, $fieldFqn);
            }
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->builder = $this->container->get(Builder::class);
    }

    /**
     * @test
     * @large
     */
    public function itCanUpdateEnumValueOptions(): void
    {
        $options = [
            'this',
            'that',
        ];
        $this->builder->setEnumOptionsOnInterface(
            '\BuilderTest_ItCanUpdateEnumValueOptions_\Entity\Fields\Interfaces\EntityOne\EnumFieldInterface',
            $options
        );
        $code = file_get_contents(
            $this->copiedWorkDir . '/src/Entity/Fields/Interfaces/EntityOne/EnumFieldInterface.php'
        );
        self::assertNotContains('FOO', $code);
        self::assertNotContains('BAR', $code);
        self::assertNotRegExp('%^\s+const%', $code);
    }
}
