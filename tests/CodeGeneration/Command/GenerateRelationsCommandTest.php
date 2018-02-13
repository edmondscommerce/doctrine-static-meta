<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;

class GenerateRelationsCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/GenerateEntityCommandTest/';

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function testGenerateRelationsNoFiltering()
    {
        $entityFqns      = $this->generateEntities();
        $namespaceHelper = $this->container->get(NamespaceHelper::class);
        $command         = $this->container->get(GenerateRelationsCommand::class);
        $tester          = $this->getCommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );
        $createdFiles = [];
        foreach ($entityFqns as $entityFqn) {
            $entityName     = (new \ReflectionClass($entityFqn))->getShortName();
            $entityPlural   = ucfirst($entityFqn::getPlural());
            $entityPath     = $namespaceHelper->getEntitySubPath(
                $entityFqn,
                self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.self::TEST_PROJECT_ENTITIES_FOLDER
            );
            $createdFiles[] = glob($this->entityRelationsPath.$entityPath.'/Traits/Has'.$entityName.'/*.php');
            $createdFiles[] = glob($this->entityRelationsPath.$entityPath.'/Traits/Has'.$entityPlural.'/*.php');
            $createdFiles[] = glob($this->entityRelationsPath.$entityPath.'/Traits/*.php');
        }
        $createdFiles = \array_merge(...$createdFiles);
        $this->assertNotEmpty($createdFiles);
        foreach ($createdFiles as $createdFile) {
            $this->assertTemplateCorrect($createdFile);
        }
    }
}
