<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand
 * @large
 */
class SetFieldCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/SetFieldCommandTest/';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER;

    private const TEST_ENTITY_PATH = '/src/Entities/Order.php';

    /**
     * @test
     */
    public function setField(): void
    {
        $command = $this->container->get(SetFieldCommand::class);
        $tester  = $this->getCommandTester($command);
        foreach ($this->getArchetypeFields() as $fieldFqn) {
            $tester->execute(
                [
                    '-' . SetFieldCommand::OPT_FIELD_SHORT                  => $fieldFqn,
                    '-' . SetFieldCommand::OPT_ENTITY_SHORT                 => $this->getCopiedFqn(self::TEST_ENTITY),
                    '-' . SetFieldCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                    '-' . SetFieldCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->copiedRootNamespace,
                ]
            );
            $this->assertFileContains($this->copiedWorkDir . '/' . self::TEST_ENTITY_PATH, $fieldFqn);
        }
    }

    private function getArchetypeFields(): array
    {
        /** @var NamespaceHelper $helper */
        $helper = $this->container->get(NamespaceHelper::class);

        return $helper->getAllArchetypeFieldFqns();
    }
}
