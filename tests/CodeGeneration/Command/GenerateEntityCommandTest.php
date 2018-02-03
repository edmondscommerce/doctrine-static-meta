<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;

class GenerateEntityCommandTest extends AbstractCommandTest
{
    const WORK_DIR = VAR_PATH.'/GenerateEntityCommandTest/';

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testGenerateEntity()
    {
        $command = $this->container->get(GenerateEntityCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-'.GenerateEntityCommand::OPT_FQN_SHORT                    => self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                                                               .self::TEST_PROJECT_ENTITIES_FOLDER.'\\This\\Is\\A\\TestEntity',
            ]
        );
        $createdFiles = [
            $this->entitiesPath.'/This/Is/A/TestEntity.php',
            $this->entitiesPath.'/../../tests/Entities/This/Is/A/TestEntityTest.php',
        ];
        foreach ($createdFiles as $createdFile) {
            $this->assertTemplateCorrect($createdFile);
        }
    }
}
