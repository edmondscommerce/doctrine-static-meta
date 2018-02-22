<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class SetFieldCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/SetRelationCommandTest/';

    const FIELDS_TO_TYPES = [
        'stringField'   => MappingHelper::TYPE_STRING,
        'floatField'    => MappingHelper::TYPE_FLOAT,
        'intField'      => MappingHelper::TYPE_INTEGER,
        'textField'     => MappingHelper::TYPE_TEXT,
        'datetimeField' => MappingHelper::TYPE_DATETIME,
    ];

    public function generateFields()
    {
        $fieldGenerator = $this
            ->container
            ->get(FieldGenerator::class)
            ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
            ->setPathToProjectRoot(static::WORK_DIR);
        $return         = [];
        foreach (self::FIELDS_TO_TYPES as $field => $type) {
            $return[] =
                $fieldGenerator->generateField($field, $type);
        }

        return $return;
    }

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
        $this->qaGeneratedCode();
    }
}
