<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;

class GenerateEmbeddableFromArchetypeCommandTest extends AbstractCommandIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'
                            .self::TEST_TYPE.'/GenerateEmbeddableFromArchetypeCommandTest/';

    public function testSetEmbeddable()
    {
        $command = $this->container->get(GenerateEmbeddableFromArchetypeCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEmbeddableFromArchetypeCommand::OPT_PROJECT_ROOT_PATH_SHORT         => self::WORK_DIR,
                '-'.GenerateEmbeddableFromArchetypeCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT    =>
                    self::TEST_PROJECT_ROOT_NAMESPACE,
                '-'.GenerateEmbeddableFromArchetypeCommand::OPT_NEW_EMBEDDABLE_CLASS_NAME_SHORT => 'PriceEmbeddable',
                '-'.GenerateEmbeddableFromArchetypeCommand::OPT_ARCHETYPE_OBJECT_FQN_SHORT      =>
                    MoneyEmbeddable::class,
            ]
        );

        $this->assertTrue($this->qaGeneratedCode());
    }
}
