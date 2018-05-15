<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;

class EntityGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/EntityGeneratorTest/';

    /**
     */
    public function testGenerateEntity()
    {
        $fqn = static::TEST_PROJECT_ROOT_NAMESPACE
               .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
               .'\\Yet\\Another\\TestEntity';
        $this->getEntityGenerator()->generateEntity($fqn);
        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITIES_FOLDER_NAME
                       .'/Yet/Another/TestEntity.php';
        $this->assertNoMissedReplacements($createdFile);

        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                       .'/Yet/Another/TestEntityRepository.php';
        $this->assertNoMissedReplacements($createdFile);
        $this->qaGeneratedCode();
    }

    public function testGenerateRepository(): void
    {
        $entityFqn = static::TEST_PROJECT_ROOT_NAMESPACE
                     .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
                     .'\\Some\\Other\\TestEntity';

        $repositoryFqn = '\\'.static::TEST_PROJECT_ROOT_NAMESPACE
                         .AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                         .'\\Some\\Other\\TestEntityRepository';

        $this->getEntityGenerator()->generateEntity($entityFqn);
        $this->setupCopiedWorkDir();

        $entityManager = $this->getEntityManager();
        $repository    = $entityManager->getRepository($this->getCopiedFqn($entityFqn));
        $this->assertInstanceOf($this->getCopiedFqn($repositoryFqn), $repository);
    }


    public function testGenerateEntityWithDeepNesting()
    {
        $entityNamespace          = static::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                    .AbstractGenerator::ENTITIES_FOLDER_NAME
                                    .'\\Human\\Head\\Eye';
        $entityFullyQualifiedName = $entityNamespace.'\\Lash';
        $this->getEntityGenerator()
             ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
             ->generateEntity($entityFullyQualifiedName);

        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITIES_FOLDER_NAME
                       .'/Human/Head/Eye/Lash.php';
        $this->assertNoMissedReplacements($createdFile);
        $this->assertContains("namespace $entityNamespace;", file_get_contents($createdFile));

        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                       .'/Human/Head/Eye/LashRepository.php';
        $this->assertNoMissedReplacements($createdFile);
        $entityFullyQualifiedName = $this->container->get(NamespaceHelper::class)->tidy(
            static::TEST_PROJECT_ROOT_NAMESPACE.'\\'
            .AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
            .'\\Human\\Head\\Eye'
        );
        $this->assertContains("namespace $entityFullyQualifiedName;", file_get_contents($createdFile));

        $this->qaGeneratedCode();
    }
}
