<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\D\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\UuidFunctionPolyfill;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Exception;
use Generator;
use Ramsey\Uuid\Uuid;
use ReflectionException;
use RuntimeException;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractBulkProcess
 * @large
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BulkEntitySaveAndUpdateTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/BulkEntitySaveAndUpdateTest';

    public const TEST_PROJECT_ROOT_NAMESPACE = 'BulkEntitySaveAndUpdate';

    public const TEST_ENTITIES_ROOT_NAMESPACE = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' .
                                                AbstractGenerator::ENTITIES_FOLDER_NAME;

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                    TestCodeGenerator::TEST_ENTITY_SIMPLE;

    private const UPDATE_INT  = 100;
    private const UPDATE_TEXT = 'this text has been updated blah blah';

    protected static bool $buildOnce = true;
    /**
     * @var BulkEntitySaver
     */
    private BulkEntitySaver $saver;
    /**
     * @var BulkEntityUpdater
     */
    private BulkEntityUpdater $updater;

    public function setup(): void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR, self::TEST_PROJECT_ROOT_NAMESPACE);
            self::$built = true;
        }
        $polyfill      = new UuidFunctionPolyfill($this->getEntityManager());
        $this->saver   = new BulkEntitySaver($this->getEntityManager());
        $this->updater =
            new BulkEntityUpdater($this->getEntityManager(), $polyfill, new MysqliConnectionFactory());
    }

    /**
     * @test
     * @return array|EntityInterface[]
     */
    public function itCanBulkSaveArraysOfLargeDataEntities(): array
    {
        $numToSave   = (int)ceil($this->getDataSize() / 2);
        $entities    = $this->createDbWithEntities($numToSave);
        $numEntities = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN)->count();
        self::assertSame($numToSave, $numEntities);

        return $entities;
    }

    /**
     * @return int
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getDataSize(): int
    {
        if ($this->isQuickTests()) {
            return 200;
        }
        if (isset($_SERVER['BulkEntitySaveAndUpdateTest_DataSize'])) {
            return (int)$_SERVER['BulkEntitySaveAndUpdateTest_DataSize'];
        }

        return 1000;
    }

    private function createDbWithEntities(int $numToSave): array
    {
        $this->createDatabase();
        $this->saver->setChunkSize(100);
        $generator = $this->getGenerator($numToSave);
        $entities  = [];
        foreach ($generator as $entity) {
            $entities[] = $entity;
        }
        $this->getEntityManager()->clear();
        $this->saver->addEntitiesToSave($entities);
        $this->saver->endBulkProcess();

        return $entities;
    }

    private function getGenerator(int $numToGenerate): Generator
    {
        return $this->getTestEntityGeneratorFactory()
                    ->createForEntityFqn(self::TEST_ENTITY_FQN)
                    ->getGenerator($numToGenerate);
    }

    /**
     * @test
     * @depends itCanBulkSaveArraysOfLargeDataEntities
     *
     * @param array $previouslySavedEntities
     *
     * @return int
     */
    public function itCanBulkSaveLargeDataEntities(array $previouslySavedEntities): int
    {
        $previouslySavedCount = count($previouslySavedEntities);
        $numEntities          = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN)->count();
        self::assertSame($previouslySavedCount, $numEntities);
        $this->saver->setChunkSize(100);

        $numToSave = (int)ceil($this->getDataSize() / 2);
        $generator = $this->getGenerator($numToSave);
        foreach ($generator as $entity) {
            $this->saver->addEntityToSave($entity);
        }
        $this->saver->endBulkProcess();
        $numEntities = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN)->count();
        self::assertGreaterThanOrEqual($this->getDataSize(), $numEntities);

        return $numEntities;
    }

    /**
     * @test
     * @depends itCanBulkSaveLargeDataEntities
     *
     * @param int $previouslySavedCount
     *
     * @return array
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function itCanBulkUpdateAnArrayOfLargeDataEntities(int $previouslySavedCount): array
    {
        $repository  = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN);
        $numEntities = $repository->count();
        $entities    = $repository->findAll();
        $this->updater->setChunkSize(100);
        $this->setExtractorOnUpdater(self::TEST_ENTITY_FQN);
        $this->updateEntitiesAndAddToBulkProcess($entities);
        $this->updater->endBulkProcess();
        self::assertSame($previouslySavedCount, $numEntities);
        $reloaded = $repository->findAll();
        $dumper   = new EntityDebugDumper();
        foreach ($reloaded as $entity) {
            self::assertSame(self::UPDATE_INT, $entity->getInteger(), $dumper->dump($entity));
            self::assertSame(self::UPDATE_TEXT, $entity->getText(), $dumper->dump($entity));
        }

        return $reloaded;
    }

    private function setExtractorOnUpdater(string $entityFqn, string $tableName = null): void
    {
        if (null === $tableName) {
            $tableName = $entityFqn::getDoctrineStaticMeta()->getMetaData()->getTableName();
        }
        $this->updater->setExtractor(
            new class ($entityFqn, $tableName) implements BulkEntityUpdater\BulkEntityUpdateHelper {
                /**
                 * @var string
                 */
                private string $entityFqn;
                /**
                 * @var string
                 */
                private string $tableName;

                public function __construct(string $entityFqn, string $tableName)
                {
                    $this->entityFqn = $entityFqn;
                    $this->tableName = $tableName;
                }

                public function getTableName(): string
                {
                    return $this->tableName;
                }

                public function getEntityFqn(): string
                {
                    return $this->entityFqn;
                }

                /**
                 * Extract entity into an array: [columnName=>value]
                 *
                 * First item in array has to be the primary key
                 *
                 * @param EntityInterface $entity
                 *
                 * @return array
                 */
                public function extract(EntityInterface $entity): array
                {
                    return [
                        'id'      => $entity->getId(),
                        'integer' => $entity->getInteger(),
                        'text'    => $entity->getText(),
                    ];
                }
            }
        );
    }

    /**
     * @param array|EntityInterface[] $entities
     *
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    private function updateEntitiesAndAddToBulkProcess(array &$entities): void
    {
        $dto = $this->getUpdateDto();
        $dto->setText(self::UPDATE_TEXT);
        $dto->setInteger(self::UPDATE_INT);
        $this->updater->prepareEntitiesForBulkUpdate($entities);
        foreach ($entities as $entity) {
            $dto->setId($entity->getId());
            $entity->update($dto);
        }
        $this->updater->addEntitiesToSave($entities);
        $entities = null;
    }

    /**
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getUpdateDto(): AbstractEntityUpdateDto|DataTransferObjectInterface
    {
        $entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);

        return new class ($entityFqn, Uuid::uuid4()) extends AbstractEntityUpdateDto {
            /**
             * @var string
             */
            private string $text = '';

            /**
             * @var int
             */
            private int $integer;

            /**
             * @return string
             */
            public function getText(): string
            {
                return $this->text;
            }

            /**
             * @return int
             */
            public function getInteger(): int
            {
                return $this->integer;
            }

            /**
             * @param string $text
             */
            public function setText(string $text): void
            {
                $this->text = $text;
            }

            /**
             * @param int $integer
             */
            public function setInteger(int $integer): void
            {
                $this->integer = $integer;
            }
        };
    }

    /**
     * @test
     * @depends itCanBulkUpdateAnArrayOfLargeDataEntities
     *
     * @param array $entities
     *
     * @return array|null
     */
    public function itCanAcceptARatioOfNonUpdatedRows(array $entities): ?array
    {
        $this->setExtractorOnUpdater(self::TEST_ENTITY_FQN);
        $this->updater->startBulkProcess();
        $this->updater->setRequireAffectedRatio(0);
        $this->updater->addEntitiesToSave($entities);
        $this->updater->endBulkProcess();
        $expected = 0;
        $actual   = $this->updater->getTotalAffectedRows();
        self::assertSame($expected, $actual);

        return $entities;
    }

    /**
     * @test
     * @depends itCanAcceptARatioOfNonUpdatedRows
     *
     * @param array $entities
     *
     * @throws Exception
     */
    public function itWillExceptIfNotEnoughRowsUpdated(array $entities): void
    {
        $this->updater->prepareEntitiesForBulkUpdate($entities);
        $skipped = 0;
        $dto     = $this->getUpdateDto();
        $dto->setInteger(200);
        foreach ($entities as $entity) {
            if ($skipped > 3) {
                $dto->setId($entity->getId());
                $entity->update($dto);
                $skipped = 0;
            }
            $skipped++;
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Affected rows count of ');

        try {
            $this->setExtractorOnUpdater(self::TEST_ENTITY_FQN);
            $this->updater->startBulkProcess();
            $this->updater->setRequireAffectedRatio(0.5);
            $this->updater->addEntitiesToSave($entities);
            $this->updater->endBulkProcess();
        } catch (Exception $e) {
            $this->updater->endBulkProcess();
            throw $e;
        }
    }

    /**
     * @test
     */
    public function theUpdaterWillExceptOnInvalidSql(): void
    {
        $entities = $this->createDbWithEntities(10);
        $this->updater->setChunkSize(10);
        $this->setExtractorOnUpdater(self::TEST_ENTITY_FQN, 'invalid_table_name');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Query #4 got MySQL Error #1146');
        $this->updateEntitiesAndAddToBulkProcess($entities);
        $this->updater->endBulkProcess();
    }
}
