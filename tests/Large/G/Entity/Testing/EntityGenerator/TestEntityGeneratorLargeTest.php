<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\G\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFillerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\GetGeneratedCodeContainerTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Test\Code\Generator\Entities\Simple;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestEntityGeneratorLargeTest extends AbstractLargeTest
{

    use GetGeneratedCodeContainerTrait;

    public const WORK_DIR = AbstractTest::VAR_PATH .
                            self::TEST_TYPE_LARGE .
                            '/TestEntityGeneratorLargeTest';

    public const TEST_ENTITY_NAMESPACE_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                              . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME;

    private const TEST_ENTITY = self::TEST_ENTITY_NAMESPACE_BASE . TestCodeGenerator::TEST_ENTITY_PERSON;

    private const TEST_ENTITY_SIMPLE = self::TEST_ENTITY_NAMESPACE_BASE . TestCodeGenerator::TEST_ENTITY_SIMPLE;

    protected static $buildOnce = true;

    public function setup(): void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
    }

    /**
     * @test
     * @return EntityInterface
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function itCanGenerateASingleEntity(): EntityInterface
    {
        $entityFqn           = self::TEST_ENTITY;
        $entityFqn           = $this->getCopiedFqn($entityFqn);
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $entity              = $testEntityGenerator->generateEntity();
        $entityManager       = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
        self::assertTrue(true);

        return $entity;
    }

    protected function getTestEntityGenerator(string $entityFqn): TestEntityGenerator
    {
        /**
         * @var TestEntityGeneratorFactory $factory
         */
        $factory = $this->container->get(TestEntityGeneratorFactory::class);
        $factory->setFakerDataProviderClasses(
            \constant(
                $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . '\\AbstractEntityTest') .
                '::FAKER_DATA_PROVIDERS'
            )
        );

        return $factory->createForEntityFqn($entityFqn);
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     * @test
     */
    public function itCanGenerateTheAttributesEmailsEntity(): void
    {
        $entityFqn           = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL
        );
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $entity              = $testEntityGenerator->generateEntity();
        $entityManager       = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
        self::assertTrue(true);
    }

    /**
     * @test
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function itGeneratesEntitiesAndAssociatedEntities(): void
    {
        $entities = [];
        $limit    = ($this->isQuickTests() ? 2 : null);
        foreach (TestCodeGenerator::TEST_ENTITIES as $key => $entityFqn) {
            if ($limit !== null && $key === $limit) {
                break;
            }
            $entityFqn           = $this->getCopiedFqn($entityFqn);
            $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
            $entity              = $testEntityGenerator->generateEntity();
            self::assertInstanceOf($entityFqn, $entity);
            $testEntityGenerator->addAssociationEntities($entity);
            $entities[] = $entity;
        }
        $this->getEntitySaver()->saveAll($entities);
        self::assertTrue(true);
    }


    /**
     * @test
     *      * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanGenerateMultipleEntities(): void
    {
        $entityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
        $count     = $this->isQuickTests() ? 2 : 100;
        $actual    = $this->getTestEntityGenerator($entityFqn)->generateEntities($count);
        self::assertCount($count, $actual);
        self::assertInstanceOf($entityFqn, current($actual));
    }

    /**
     * @test
     *      * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanCreateAnEmptyEntityUsingTheFactory(): void
    {
        $entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_SIMPLE);
        $entity    = $this->getTestEntityGenerator($entityFqn)->create();
        self::assertInstanceOf($entityFqn, $entity);
    }

    /**
     * @test
     *      * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanCreateAnEntityWithValuesSet(): void
    {
        $entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_SIMPLE);
        $values    = [
            'string' => 'this has been set',
        ];
        $entity    = $this->getTestEntityGenerator($entityFqn)->create($values);
        self::assertSame($values['string'], $entity->getString());
    }

    /**
     * @test
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanYieldUnsavedEntities(): void
    {
        $entityFqn           = $this->getCopiedFqn(self::TEST_ENTITY);
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $generator           = $testEntityGenerator->getGenerator(3);
        $entity1             = null;
        foreach ($generator as $entity) {
            $entity1 = $entity;
            break;
        }
        $generator->next();
        $entity3 = $generator->current();
        $generator->next();
        $entity2 = $generator->current();

        self::assertInstanceOf($entityFqn, $entity1);
        self::assertInstanceOf($entityFqn, $entity2);
        self::assertInstanceOf($entityFqn, $entity3);
        self::assertNotSame($entity1, $entity2);
        self::assertNotSame($entity1, $entity3);
        self::assertNotSame($entity2, $entity3);
    }

    /**
     * @test
     */
    public function itWillUseACustomDataFiller(): void
    {
        /** @var FakerDataFillerFactory $factory */
        $factory = $this->container->get(FakerDataFillerFactory::class);
        $entityFqn = trim($this->getCopiedFqn(self::TEST_ENTITY_SIMPLE), '\\');
        $fakerDataFiller = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE . '\\Assets\\Entity\\FakerDataFillers\\SimpleFakerDataFiller'
        );
        $factory->setCustomFakerDataFillersFqns([$entityFqn => $fakerDataFiller]);
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $generator           = $testEntityGenerator->getGenerator(3);
        $entity1             = null;
        foreach ($generator as $entity) {
            /** @var Simple $entity */
            $entityString = $entity->getString();
            self::assertSame('Set from a custom Faker Data Filler', $entityString);
        }
    }
}
