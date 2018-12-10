<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\D\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\GetGeneratedCodeContainerTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

class EntityUnitOfWorkHelperLargeTest extends AbstractLargeTest
{
    use GetGeneratedCodeContainerTrait;

    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityUnitOfWorkHelperLargeTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL;

    protected static $buildOnce = true;
    /** @var string */
    private $entityFqn;
    /** @var NamespaceHelper */
    private $namespaceHelper;

    public function setUp()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn       = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->namespaceHelper = self::$containerStaticRef->get(NamespaceHelper::class);
    }

    /**
     * @test
     */
    public function itCanReturnAnEntityFromTheUnitOfWork(): void
    {
        $entity = $this->createEntity($this->entityFqn);
        $dto    = $this->getEntityDtoFactory()->createDtoFromEntity($entity);
        $class  = $this->getClass();
        $this->getEntitySaver()->save($entity);
        $savedEntity = $this->getRepository()->get($entity->getId());
        $class->addEntityRecord($savedEntity);
        $fetchedEntity = $class->getEntityFromUnitOfWorkUsingDto($dto);
        self::assertSame($savedEntity, $fetchedEntity);
    }

    private function getClass()
    {
        $class = $this->namespaceHelper->getEntityUnitOfWorkHelperFqnFromEntityFqn($this->entityFqn);

        return $this->getGeneratedClass($class);
    }

    private function getRepository()
    {
        $class = $this->namespaceHelper->getRepositoryqnFromEntityFqn($this->entityFqn);

        return $this->getGeneratedClass($class);
    }

    /**
     * @test
     */
    public function itIsAbleToLearnAboutAnEntity(): void
    {
        $entity = $this->createEntity($this->entityFqn);
        $dto    = $this->getEntityDtoFactory()->createDtoFromEntity($entity);
        $class  = $this->getClass();
        self::assertFalse($class->hasRecordOfDto($dto));
        $this->getEntitySaver()->save($entity);
        $this->getRepository()->get($entity->getId());
        self::assertTrue($class->hasRecordOfDto($dto));
    }

    /**
     * @test
     */
    public function itWillReturnFalseIfItDoesNotKnowAboutAnEntity(): void
    {
        $dto   = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($this->entityFqn);
        $class = $this->getClass();
        self::assertFalse($class->hasRecordOfDto($dto));
    }

    /**
     * @test
     */
    public function itWillThrowAnExceptionIfItTriedToFetchAnEntityItDoesNotKnowAbout(): void
    {
        $dto   = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($this->entityFqn);
        $class = $this->getClass();
        $this->expectException(\RuntimeException::class);
        $class->getEntityFromUnitOfWorkUsingDto($dto);
    }
}
