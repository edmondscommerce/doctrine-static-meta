<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\F\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use ReflectionException;

/**
 * Class EntityGeneratorIntegrationTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Generator
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator
 * @large
 */
class EntityGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityGeneratorTest/';

    /**
     * @test
     */
    public function generateEntity(): void
    {
        $fqn = static::TEST_PROJECT_ROOT_NAMESPACE
               . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
               . '\\Yet\\Another\\TestEntity';
        $this->getEntityGenerator()->generateEntity($fqn);
        foreach (
            [
                     static::WORK_DIR
                     . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                     . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME
                     . '/Yet/Another/TestEntity.php'
                     ,
                     static::WORK_DIR
                     . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                     . '/' . AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                     . '/Yet/Another/TestEntityRepository.php',
                     static::WORK_DIR
                     . '/tests/Assets/Entity/Fixtures/Yet/Another/TestEntityFixture.php',
                 ] as $createdFile
        ) {
            $this->assertNoMissedReplacements($createdFile);
        }
        $this->qaGeneratedCode();
    }

    /**
     * @test
     * @testdox If the project namespace root has the word Entities in there, make sure it does not cause issues
     *
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function generateWithEntitiesInProjectNamespace(): void
    {
        $this->setupCopiedWorkDir();
        $namespaceRoot = $this->copiedRootNamespace;
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
     * @throws DoctrineStaticMetaException
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
        self::assertStringContainsString("namespace $entityNamespace;", file_get_contents($createdFile));

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
        self::assertStringContainsString("namespace $entityFullyQualifiedName;", file_get_contents($createdFile));

        $this->qaGeneratedCode();
    }
}
