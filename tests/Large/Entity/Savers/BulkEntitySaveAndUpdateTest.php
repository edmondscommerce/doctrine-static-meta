<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\UuidFunctionPolyfill;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractBulkProcess
 * @large
 */
class BulkEntitySaveAndUpdateTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/BulkEntitySaveAndUpdateTest';

    public const TEST_PROJECT_ROOT_NAMESPACE = 'BulkEntitySaveAndUpdate';

    public const TEST_ENTITIES_ROOT_NAMESPACE = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' .
                                                AbstractGenerator::ENTITIES_FOLDER_NAME;

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;

    private const UPDATE_INT  = 100;
    private const UPDATE_TEXT = 'this text has been updated blah blah';

    protected static $buildOnce = true;
    /**
     * @var BulkEntitySaver
     */
    private $saver;
    /**
     * @var BulkEntityUpdater
     */
    private $updater;

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
        $this->updater = new BulkEntityUpdater($this->getEntityManager(), $polyfill, new MysqliConnectionFactory());
    }

    /**
     * @test
     */
    public function itCanBulkSaveArraysOfLargeDataEntities()
    {
        $numToSave = (int)ceil($this->getDataSize() / 2);
        $this->createDbWithEntities($numToSave);
        $numEntities = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN)->count();
        self::assertSame($numToSave, $numEntities);

        return $numEntities;
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
        $generator = $this->getGenerator();
        $entities  = [];
        foreach ($generator as $entity) {
            $entities[] = $entity;
            if ($numToSave === count($entities)) {
                break;
            }
        }
        $this->saver->addEntitiesToSave($entities);
        $this->saver->endBulkProcess();

        return $entities;
    }

    private function getGenerator(): \Generator
    {
        return $this->getTestEntityGeneratorFactory()
                    ->createForEntityFqn(self::TEST_ENTITY_FQN)
                    ->getGenerator();
    }

    /**
     * @test
     * @depends itCanBulkSaveArraysOfLargeDataEntities
     *
     * @param int $previouslySavedCount
     *
     * @return int
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function itCanBulkSaveLargeDataEntities(int $previouslySavedCount): int
    {
        $numEntities = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN)->count();
        self::assertSame($previouslySavedCount, $numEntities);
        $this->saver->setChunkSize(100);

        $generator = $this->getTestEntityGeneratorFactory()
                          ->createForEntityFqn(self::TEST_ENTITY_FQN)
                          ->getGenerator();
        $numToSave = (int)ceil($this->getDataSize() / 2);
        $num       = 0;
        foreach ($generator as $entity) {
            $this->saver->addEntityToSave($entity);
            if ($numToSave === ++$num) {
                break;
            }
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

    private function setExtractorOnUpdater(string $entityFqn, string $tableName = 'person'): void
    {
        $this->updater->setExtractor(
            new class($entityFqn, $tableName) implements BulkEntityUpdater\BulkEntityUpdateHelper
            {
                /**
                 * @var string
                 */
                private $entityFqn;
                /**
                 * @var string
                 */
                private $tableName;

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

    private function updateEntitiesAndAddToBulkProcess(array &$entities): void
    {
        $dto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn(self::TEST_ENTITY_FQN);
        $dto->setText(self::UPDATE_TEXT)->setInteger(self::UPDATE_INT);
        $this->updater->prepareEntitiesForBulkUpdate($entities);
        foreach ($entities as $entity) {
            $dto->setId($entity->getId());
            $entity->update($dto);
        }
        $this->updater->addEntitiesToSave($entities);
        $entities = null;
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
     * @throws \Exception
     */
    public function itWillExceptIfNotEnoughRowsUpdated(array $entities): void
    {
        $this->updater->prepareEntitiesForBulkUpdate($entities);
        $skipped = 0;
        $dto     = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn(self::TEST_ENTITY_FQN);
        $dto->setInteger(200);
        foreach ($entities as $entity) {
            if ($skipped > 3) {
                $dto->setId($entity->getId());
                $entity->update($dto);
                $skipped = 0;
            }
            $skipped++;
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Affected rows count of ');

        try {
            $this->setExtractorOnUpdater(self::TEST_ENTITY_FQN);
            $this->updater->startBulkProcess();
            $this->updater->setRequireAffectedRatio(0.5);
            $this->updater->addEntitiesToSave($entities);
            $this->updater->endBulkProcess();
        } catch (\Exception $e) {
            $this->updater->endBulkProcess();
            throw $e;
        }
    }

    /**
     * @test
     */
    public function theUpdaterWillExceptOnInvalidSql()
    {
        $entities = $this->createDbWithEntities(10);
        $this->updater->setChunkSize(10);
        $this->setExtractorOnUpdater(self::TEST_ENTITY_FQN, 'invalid_table_name');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Query #4 got MySQL Error #1146');
        $this->updateEntitiesAndAddToBulkProcess($entities);
        $this->updater->endBulkProcess();
    }
}
