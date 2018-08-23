<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableFromArchetypeCommand;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class GenerateEmbeddableFromArchetypeCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableFromArchetypeCommand
 */
class GenerateEmbeddableFromArchetypeCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/GenerateEmbeddableFromArchetypeCommandTest/';

    /**
     * @test
     * @large
     * @covers ::execute
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function generateEmbeddableFromArchetype(): void
    {
        $command = $this->container->get(GenerateEmbeddableFromArchetypeCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEmbeddableFromArchetypeCommand::OPT_PROJECT_ROOT_PATH_SHORT         => self::WORK_DIR,
                '-' . GenerateEmbeddableFromArchetypeCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT    =>
                    self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . GenerateEmbeddableFromArchetypeCommand::OPT_NEW_EMBEDDABLE_CLASS_NAME_SHORT => 'PriceEmbeddable',
                '-' . GenerateEmbeddableFromArchetypeCommand::OPT_ARCHETYPE_OBJECT_FQN_SHORT      =>
                    MoneyEmbeddable::class,
            ]
        );

        self::assertTrue($this->qaGeneratedCode());
    }
}
