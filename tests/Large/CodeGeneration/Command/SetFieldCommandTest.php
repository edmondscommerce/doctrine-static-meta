<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use Doctrine\Common\Inflector\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class SetFieldCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class SetFieldCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/SetFieldCommandTest/';

    private const FIELDS_TO_TYPES = [
        MappingHelper::TYPE_STRING,
        MappingHelper::TYPE_FLOAT,
        MappingHelper::TYPE_INTEGER,
        MappingHelper::TYPE_TEXT,
        MappingHelper::TYPE_DATETIME,
    ];

    /**
     * @test
     * @large
     *      * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function setField(): void
    {
        list($entityFqn) = $this->generateEntities();

        $command = $this->container->get(SetFieldCommand::class);
        $tester  = $this->getCommandTester($command);
        foreach ($this->generateFields() as $fieldFqn) {
            $tester->execute(
                [
                    '-' . SetFieldCommand::OPT_FIELD_SHORT  => $fieldFqn,
                    '-' . SetFieldCommand::OPT_ENTITY_SHORT => $entityFqn,
                ]
            );
        }
        self::assertNotFalse(
            \strpos(
                file_get_contents(static::WORK_DIR . '/src/Entities/' . $this->getName() . '/FirstEntity.php'),
                'use DatetimeFieldTrait'
            )
        );
        self::assertTrue($this->qaGeneratedCode());
    }

    /**
     * @return array
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    private function generateFields(): array
    {
        $fieldGenerator = $this->getFieldGenerator();
        $return         = [];
        $namespace      = static::TEST_PROJECT_ROOT_NAMESPACE . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

        foreach (self::FIELDS_TO_TYPES as $type) {
            $classy   = Inflector::classify($type);
            $fieldFqn = "$namespace\\$classy\\$classy";
            $return[] = $fieldGenerator->generateField($fieldFqn, $type);
        }

        return $return;
    }
}
