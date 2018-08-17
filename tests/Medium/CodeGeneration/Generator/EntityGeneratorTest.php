<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class EntityGeneratorIntegrationTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Generator
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator
 */
class EntityGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/EntityGeneratorTest/';

    /**
     * @test
     * @medium
     * @covers ::generateEntity
     */
    public function generateEntity(): void
    {
        $fqn = static::TEST_PROJECT_ROOT_NAMESPACE
               . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
               . '\\Yet\\Another\\TestEntity';
        $this->getEntityGenerator()->generateEntity($fqn);
        $createdFile = static::WORK_DIR
                       . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME
                       . '/Yet/Another/TestEntity.php';
        $this->assertNoMissedReplacements($createdFile);

        $createdFile = static::WORK_DIR
                       . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       . '/' . AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                       . '/Yet/Another/TestEntityRepository.php';
        $this->assertNoMissedReplacements($createdFile);
        $this->qaGeneratedCode();
    }

    /**
     * @test
     * @medium
     * @covers ::generateEntity
     * @testdox Ensure we create the correct custom repository and also that Doctrine is properly configured to use it
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function generateRepository(): void
    {
        $entityFqn = static::TEST_PROJECT_ROOT_NAMESPACE
                     . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                     . '\\Some\\Other\\TestEntity';

        $repositoryFqn = '\\' . static::TEST_PROJECT_ROOT_NAMESPACE
                         . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                         . '\\Some\\Other\\TestEntityRepository';

        $this->getEntityGenerator()->generateEntity($entityFqn);
        $this->setupCopiedWorkDir();

        $entityManager = $this->getEntityManager();
        $repository    = $entityManager->getRepository($this->getCopiedFqn($entityFqn));
        self::assertInstanceOf($this->getCopiedFqn($repositoryFqn), $repository);
    }

    /**
     * @test
     * @medium
     * @testdox If the project namespace root has the word Entities in there, make sure it does not cause issues
     * @covers ::generateEntity
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function generateWithEntitiesInProjectNamespace(): void
    {
        $namespaceRoot = 'My\\Test\\ProjectWithEntities';
        $generator     = $this->getEntityGenerator()
                              ->setProjectRootNamespace($namespaceRoot);
        $entityFqnDeep = $namespaceRoot
                         . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                         . '\\Some\\Other\\TestEntity';
        $generator->generateEntity($entityFqnDeep);

        $entityFqnRoot = $namespaceRoot
                         . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                         . '\\RootLevelEntity';
        $generator->generateEntity($entityFqnRoot);

        self::assertTrue($this->qaGeneratedCode($namespaceRoot));
    }

    /**
     * @test
     * @medium
     * @covers ::generateEntity
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function generateEntityWithDeepNesting(): void
    {
        $entityNamespace          = static::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                    . AbstractGenerator::ENTITIES_FOLDER_NAME
                                    . '\\Human\\Head\\Eye';
        $entityFullyQualifiedName = $entityNamespace . '\\Lash';
        $this->getEntityGenerator()
             ->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
             ->generateEntity($entityFullyQualifiedName);

        $createdFile = static::WORK_DIR
                       . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME
                       . '/Human/Head/Eye/Lash.php';
        $this->assertNoMissedReplacements($createdFile);
        self::assertContains("namespace $entityNamespace;", file_get_contents($createdFile));

        $createdFile = static::WORK_DIR
                       . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       . '/' . AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                       . '/Human/Head/Eye/LashRepository.php';
        $this->assertNoMissedReplacements($createdFile);
        $entityFullyQualifiedName = $this->container->get(NamespaceHelper::class)->tidy(
            static::TEST_PROJECT_ROOT_NAMESPACE . '\\'
            . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
            . '\\Human\\Head\\Eye'
        );
        self::assertContains("namespace $entityFullyQualifiedName;", file_get_contents($createdFile));

        $this->qaGeneratedCode();
    }
}
