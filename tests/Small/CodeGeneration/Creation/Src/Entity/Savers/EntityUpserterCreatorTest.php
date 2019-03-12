<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUpserterCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUpserterCreator
 * @small
 */
class EntityUpserterCreatorTest extends TestCase
{
    private const BASE_NAMESPACE = 'EdmondsCommerce\DoctrineStaticMeta';

    private const UPSERTER = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\NewUpsertDtoDataModifierInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\TestEntityDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\TestEntityDtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\TestEntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\TestEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\TestEntityRepository;

class TestEntityUpserter
{
    /**
     * @var TestEntityDtoFactory
     */
    private $dtoFactory;
    /**
     * @var TestEntityFactory
     */
    private $entityFactory;
    /**
     * @var TestEntityRepository
     */
    private $repository;
    /**
     * @var EntitySaver
     */
    private $saver;
    /**
     * @var TestEntityUnitOfWorkHelper
     */
    private $unitOfWorkHelper;

    public function __construct(
        TestEntityRepository $repository,
        TestEntityDtoFactory $dtoFactory,
        TestEntityFactory $entityFactory,
        EntitySaver $saver,
        TestEntityUnitOfWorkHelper $unitOfWorkHelper
    ) {
        $this->repository       = $repository;
        $this->dtoFactory       = $dtoFactory;
        $this->entityFactory    = $entityFactory;
        $this->saver            = $saver;
        $this->unitOfWorkHelper = $unitOfWorkHelper;
    }

    public function getUpsertDtoByProperties(array $propertiesToValues): TestEntityDto
    {
        $modifier = $this->getModifierClass($propertiesToValues);

        return $this->getUpsertDtoByCriteria($propertiesToValues, $modifier);
    }

    private function getModifierClass(array $propertiesToValues): NewUpsertDtoDataModifierInterface
    {
        return new class($propertiesToValues) implements NewUpsertDtoDataModifierInterface
        {
            private $propertiesToValues;

            public function __construct(array $propertiesToValues)
            {
                $this->propertiesToValues = $propertiesToValues;
            }

            public function addDataToNewlyCreatedDto(DataTransferObjectInterface $dto): void
            {
                foreach ($this->propertiesToValues as $property => $value) {
                    $setter = 'set' . ucfirst($property);
                    $dto->$setter($value);
                }
            }
        };
    }

    /**
     * This method is used to get a DTO using search criteria, when you are not certain if the entity exists or not.
     * The criteria is passed through to the repository findOneBy method, if an entity is found then a DTO will be
     * created from it and returned.
     *
     * If an entity is not found then a new empty DTO will be created and returned instead.
     *
     * @param array                             $criteria
     * @param NewUpsertDtoDataModifierInterface $modifier
     *
     * @return TestEntityDto
     * @see \Doctrine\ORM\EntityRepository::findOneBy for how to use the crietia
     */
    public function getUpsertDtoByCriteria(
        array $criteria,
        NewUpsertDtoDataModifierInterface $modifier
    ): TestEntityDto {
        $entity = $this->repository->findOneBy($criteria);
        if ($entity === null) {
            $dto = $this->dtoFactory->create();
            $modifier->addDataToNewlyCreatedDto($dto);

            return $dto;
        }

        return $this->dtoFactory->createDtoFromTestEntity($entity);
    }

    public function getUpsertDtoByProperty(string $propertyName, $value): TestEntityDto
    {
        $modifier = $this->getModifierClass([$propertyName => $value]);

        return $this->getUpsertDtoByCriteria([$propertyName => $value], $modifier);
    }

    /**
     * This is used to persist the DTO to the database. If the DTO is for a new entity then it will be created, if it
     * is for an existing Entity then it will be updated.
     *
     * Be aware that this method should __only__ be used with DTOs that have been created using the
     * self::getUpsertDtoByCriteria method, as if they come from elsewhere we will not not if the entity needs to be
     * created or updated
     *
     * @param TestEntityDto $dto
     *
     * @return TestEntityInterface
     * @throws \Doctrine\DBAL\DBALException
     */
    public function persistUpsertDto(TestEntityDto $dto): TestEntityInterface
    {
        $entity = $this->convertUpsertDtoToEntity($dto);
        $this->saver->save($entity);

        return $entity;
    }

    /**
     * This method will convert the DTO into an entity, but will not save it. This is useful if you want to bulk create
     * or update entities
     *
     * @param TestEntityDto $dto
     *
     * @return TestEntityInterface
     */
    public function convertUpsertDtoToEntity(TestEntityDto $dto): TestEntityInterface
    {
        if ($this->unitOfWorkHelper->hasRecordOfDto($dto) === false) {
            $entity = $this->entityFactory->create($dto);

            return $entity;
        }
        $entity = $this->unitOfWorkHelper->getEntityFromUnitOfWorkUsingDto($dto);
        $entity->update($dto);

        return $entity;
    }
}
PHP;

    public const NESTED_UPSERTER = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\Deeply\Ne\S\ted;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Deeply\Ne\S\ted\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\Deeply\Ne\S\ted\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\Deeply\Ne\S\ted\NewUpsertDtoDataModifierInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\Deeply\Ne\S\ted\TestEntityDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\Deeply\Ne\S\ted\TestEntityDtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\Deeply\Ne\S\ted\TestEntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Deeply\Ne\S\ted\TestEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\Deeply\Ne\S\ted\TestEntityRepository;

class TestEntityUpserter
{
    /**
     * @var TestEntityDtoFactory
     */
    private $dtoFactory;
    /**
     * @var TestEntityFactory
     */
    private $entityFactory;
    /**
     * @var TestEntityRepository
     */
    private $repository;
    /**
     * @var EntitySaver
     */
    private $saver;
    /**
     * @var TestEntityUnitOfWorkHelper
     */
    private $unitOfWorkHelper;

    public function __construct(
        TestEntityRepository $repository,
        TestEntityDtoFactory $dtoFactory,
        TestEntityFactory $entityFactory,
        EntitySaver $saver,
        TestEntityUnitOfWorkHelper $unitOfWorkHelper
    ) {
        $this->repository       = $repository;
        $this->dtoFactory       = $dtoFactory;
        $this->entityFactory    = $entityFactory;
        $this->saver            = $saver;
        $this->unitOfWorkHelper = $unitOfWorkHelper;
    }

    public function getUpsertDtoByProperties(array $propertiesToValues): TestEntityDto
    {
        $modifier = $this->getModifierClass($propertiesToValues);

        return $this->getUpsertDtoByCriteria($propertiesToValues, $modifier);
    }

    private function getModifierClass(array $propertiesToValues): NewUpsertDtoDataModifierInterface
    {
        return new class($propertiesToValues) implements NewUpsertDtoDataModifierInterface
        {
            private $propertiesToValues;

            public function __construct(array $propertiesToValues)
            {
                $this->propertiesToValues = $propertiesToValues;
            }

            public function addDataToNewlyCreatedDto(DataTransferObjectInterface $dto): void
            {
                foreach ($this->propertiesToValues as $property => $value) {
                    $setter = 'set' . ucfirst($property);
                    $dto->$setter($value);
                }
            }
        };
    }

    /**
     * This method is used to get a DTO using search criteria, when you are not certain if the entity exists or not.
     * The criteria is passed through to the repository findOneBy method, if an entity is found then a DTO will be
     * created from it and returned.
     *
     * If an entity is not found then a new empty DTO will be created and returned instead.
     *
     * @param array                             $criteria
     * @param NewUpsertDtoDataModifierInterface $modifier
     *
     * @return TestEntityDto
     * @see \Doctrine\ORM\EntityRepository::findOneBy for how to use the crietia
     */
    public function getUpsertDtoByCriteria(
        array $criteria,
        NewUpsertDtoDataModifierInterface $modifier
    ): TestEntityDto {
        $entity = $this->repository->findOneBy($criteria);
        if ($entity === null) {
            $dto = $this->dtoFactory->create();
            $modifier->addDataToNewlyCreatedDto($dto);

            return $dto;
        }

        return $this->dtoFactory->createDtoFromTestEntity($entity);
    }

    public function getUpsertDtoByProperty(string $propertyName, $value): TestEntityDto
    {
        $modifier = $this->getModifierClass([$propertyName => $value]);

        return $this->getUpsertDtoByCriteria([$propertyName => $value], $modifier);
    }

    /**
     * This is used to persist the DTO to the database. If the DTO is for a new entity then it will be created, if it
     * is for an existing Entity then it will be updated.
     *
     * Be aware that this method should __only__ be used with DTOs that have been created using the
     * self::getUpsertDtoByCriteria method, as if they come from elsewhere we will not not if the entity needs to be
     * created or updated
     *
     * @param TestEntityDto $dto
     *
     * @return TestEntityInterface
     * @throws \Doctrine\DBAL\DBALException
     */
    public function persistUpsertDto(TestEntityDto $dto): TestEntityInterface
    {
        $entity = $this->convertUpsertDtoToEntity($dto);
        $this->saver->save($entity);

        return $entity;
    }

    /**
     * This method will convert the DTO into an entity, but will not save it. This is useful if you want to bulk create
     * or update entities
     *
     * @param TestEntityDto $dto
     *
     * @return TestEntityInterface
     */
    public function convertUpsertDtoToEntity(TestEntityDto $dto): TestEntityInterface
    {
        if ($this->unitOfWorkHelper->hasRecordOfDto($dto) === false) {
            $entity = $this->entityFactory->create($dto);

            return $entity;
        }
        $entity = $this->unitOfWorkHelper->getEntityFromUnitOfWorkUsingDto($dto);
        $entity->update($dto);

        return $entity;
    }
}
PHP;


    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityUpserter(): void
    {
        $nestedNamespace = '\\Deeply\\Ne\\S\\ted';
        $newObjectFqn    = self::BASE_NAMESPACE . "\\Entity\\Savers$nestedNamespace\\TestEntityUpserter";
        $file            = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected        = self::NESTED_UPSERTER;
        $actual          = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityUpserterCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntityUpserterCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
    }

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityUpserterFromEntityFqn(): void
    {
        $entityName      = 'TestEntity';
        $nestedNamespace = '\\Deeply\\Ne\\S\\ted';
        $entityFqn       = self::BASE_NAMESPACE . "\\Entities$nestedNamespace\\$entityName";
        $file            = $this->getCreator()
                                ->setNewObjectFqnFromEntityFqn($entityFqn)
                                ->createTargetFileObject()
                                ->getTargetFile();
        $expected        = self::NESTED_UPSERTER;
        $actual          = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewEntityUpserter(): void
    {
        $newObjectFqn = self::BASE_NAMESPACE . "\\Entity\\Savers\\TestEntityUpserter";
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::UPSERTER;
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewEntityUpserterFromEntityFqn(): void
    {
        $entityName = 'TestEntity';
        $entityFqn  = self::BASE_NAMESPACE . "\\Entities\\$entityName";
        $file       = $this->getCreator()
                           ->setNewObjectFqnFromEntityFqn($entityFqn)
                           ->createTargetFileObject()
                           ->getTargetFile();
        $expected   = self::UPSERTER;
        $actual     = $file->getContents();
        self::assertSame($expected, $actual);
    }
}
