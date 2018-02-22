<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class GenerateFieldCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/GenerateFieldCommandTest/';

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws DoctrineStaticMetaException
     */
    public function testGenerateEntity()
    {
        $command = $this->container->get(GenerateFieldCommand::class);
        $tester  = $this->getCommandTester($command);
        $fieldsPath = self::WORK_DIR.'src/Entity/Fields';

        $createdFiles = [];

        foreach (MappingHelper::COMMON_TYPES as $type) {
            $tester->execute(
                [
                    '-'.GenerateFieldCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                    '-'.GenerateFieldCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                    '-'.GenerateFieldCommand::OPT_NAME_SHORT                    => $type,
                    '-'.GenerateFieldCommand::OPT_TYPE_SHORT                    => $type
                ]
            );

            $createdFiles[] = "$fieldsPath/Interfaces/";
            $createdFiles[] = "$fieldsPath/Traits/";
        }


//        $createdFiles = [
//            $this->entitiesPath.'/This/Is/A/TestEntity.php',
//            $this->entitiesPath.'/../../tests/Entities/This/Is/A/TestEntityTest.php',
//        ];

        foreach ($createdFiles as $createdFile) {
            $this->assertNoMissedReplacements($createdFile);
        }

        $this->qaGeneratedCode();
    }
}
