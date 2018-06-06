<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class SetFieldCommandTest extends AbstractCommandIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/SetFieldCommandTest/';

    private const FIELDS_TO_TYPES = [
        MappingHelper::TYPE_STRING,
        MappingHelper::TYPE_FLOAT,
        MappingHelper::TYPE_INTEGER,
        MappingHelper::TYPE_TEXT,
        MappingHelper::TYPE_DATETIME,
    ];

    /**
     * @return array
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function generateFields()
    {
        $fieldGenerator = $this->container
            ->get(FieldGenerator::class)
            ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
            ->setPathToProjectRoot(static::WORK_DIR);
        $return = [];
        $namespace = static::TEST_PROJECT_ROOT_NAMESPACE . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

        foreach (self::FIELDS_TO_TYPES as $type) {
            $classy = Inflector::classify($type);
            $fieldFqn = "$namespace\\$classy\\$classy";
            $return[] = $fieldGenerator->generateField($fieldFqn, $type);
        }

        return $return;
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function testSetField()
    {
        list($entityFqn) = $this->generateEntities();

        $command = $this->container->get(SetFieldCommand::class);
        $tester  = $this->getCommandTester($command);
        foreach ($this->generateFields() as $fieldFqn) {
            $tester->execute(
                [
                    '-'.SetFieldCommand::OPT_FIELD_SHORT  => $fieldFqn,
                    '-'.SetFieldCommand::OPT_ENTITY_SHORT => $entityFqn,
                ]
            );
        }
        $this->assertNotFalse(
            \strpos(
                file_get_contents(static::WORK_DIR.'/src/Entities/testSetField/FirstEntity.php'),
                'use DatetimeFieldTrait'
            )
        );
        $this->assertTrue($this->qaGeneratedCode());
    }
}
