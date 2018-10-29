<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class GenerateRelationsCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GenerateRelationsCommandTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/GenerateRelationsCommandTest/';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . '\\GenerateRelationsCommandTestEntity';

    public function setup()
    {
        parent::setUp();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     *      * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function generateRelationsNoFiltering(): void
    {

        $command = $this->getCommand();
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );
        $entityFqn      = self::TEST_ENTITY;
        $entityName     = (new  \ts\Reflection\ReflectionClass($entityFqn))->getShortName();
        $entityPlural   = ucfirst($entityFqn::getDoctrineStaticMeta()->getPlural());
        $createdFiles[] =
            glob(self::WORK_DIR . '/src/Entity/Relations/' . $entityName . '/Traits/Has' . $entityName . '/*.php');
        $createdFiles[] =
            glob(self::WORK_DIR . '/src/Entity/Relations/' . $entityName . '/Traits/Has' . $entityPlural . '/*.php');
        $createdFiles[] = glob(self::WORK_DIR . '/src/Entity/Relations/' . $entityName . '/Traits/*.php');
        $createdFiles   = array_merge(...$createdFiles);
        self::assertNotEmpty($createdFiles, 'Failed finding any created files in ' . __METHOD__);
        foreach ($createdFiles as $createdFile) {
            $this->assertNoMissedReplacements($createdFile);
        }
    }

    private function getCommand(): GenerateRelationsCommand
    {
        return $this->container->get(GenerateRelationsCommand::class);
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
}
