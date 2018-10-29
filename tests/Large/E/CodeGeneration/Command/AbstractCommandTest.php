<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends AbstractTest
{

    protected const COMMAND_TEST_ENTITIES = [
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON,
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS,
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT,
    ];

    protected static $buildOnce = true;

    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
    }


    /**
     * @param AbstractCommand $command
     *
     * @return CommandTester
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getCommandTester(AbstractCommand $command): CommandTester
    {
        $application = new Application();
        //$_SERVER[ConfigInterface::PARAM_ENTITIES_PATH] = static::WORK_DIR.'/src/Entities';
        $helperSet = ConsoleRunner::createHelperSet(
            $this->container->get(EntityManagerInterface::class)
        );
        $application->setHelperSet($helperSet);
        $application->add($command);

        return new CommandTester($command);
    }

    protected function getEntityPath(string $entityFqn): string
    {
        $entityPath = str_replace(
            '\\',
            '/',
            \substr(
                $entityFqn,
                \strpos(
                    $entityFqn,
                    'Entities\\'
                ) + \strlen('Entities\\')
            )
        );

        return '/' . $entityPath;
    }

    /**
     * @return array
     */
    protected function getTestEntityFqns(): array
    {
        return array_map([$this, 'getCopiedFqn'], self::COMMAND_TEST_ENTITIES);
    }
}
