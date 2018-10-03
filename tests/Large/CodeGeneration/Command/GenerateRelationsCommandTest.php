<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class GenerateRelationsCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand
 */
class GenerateRelationsCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/GenerateRelationsCommandTest/';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . '\\GenerateRelationsCommandTestEntity';

    public function buildCode()
    {
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
        $namespaceHelper = $this->container->get(NamespaceHelper::class);
        $command         = $this->container->get(GenerateRelationsCommand::class);
        $entityFqn       = self::TEST_ENTITY;

        $tester = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );

        $entityName     = (new  \ts\Reflection\ReflectionClass(self::TEST_ENTITY))->getShortName();
        $entityPlural   = ucfirst($entityFqn::getDoctrineStaticMeta()->getPlural());
        $entityPath     = $namespaceHelper->getEntitySubPath(
            $entityFqn,
            self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
        );
        $createdFiles[] = glob($this->entityRelationsPath . $entityPath . '/Traits/Has' . $entityName . '/*.php');
        $createdFiles[] = glob($this->entityRelationsPath . $entityPath . '/Traits/Has' . $entityPlural . '/*.php');
        $createdFiles[] = glob($this->entityRelationsPath . $entityPath . '/Traits/*.php');
        $createdFiles   = array_merge(...$createdFiles);
        self::assertNotEmpty($createdFiles, 'Failed finding any created files in ' . __METHOD__);
        foreach ($createdFiles as $createdFile) {
            $this->assertNoMissedReplacements($createdFile);
        }
    }
}
