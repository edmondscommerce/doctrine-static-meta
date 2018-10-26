<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
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
        $this->saver   = new BulkEntitySaver($this->getEntityManager());
        $this->updater = new BulkEntityUpdater($this->getEntityManager(), new MysqliConnectionFactory());
    }


    /**
     * @test
     */
    public function itCanBulkSaveArraysOfLargeDataEntities()
    {
        $this->createDatabase();
        $this->saver->setChunkSize(100);
        $generator = $this->getTestEntityGeneratorFactory()
                          ->createForEntityFqn(self::TEST_ENTITY_FQN)
                          ->getGenerator();
        $entities  = [];
        $numToSave = (int)ceil($this->getDataSize() / 2);
        foreach ($generator as $entity) {
            $entities[] = $entity;
            if ($numToSave === count($entities)) {
                break;
            }
        }
        $this->saver->addEntitiesToSave($entities);
        $this->saver->endBulkProcess();
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
        for ($i = 0; $i < $numToSave; $i++) {
            $this->saver->addEntityToSave($this->getNextEntity($generator));
        }
        $this->saver->endBulkProcess();
        $numEntities = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN)->count();
        self::assertGreaterThanOrEqual($this->getDataSize(), $numEntities);

        return $numEntities;
    }

    private function getNextEntity(\Generator $generator): EntityInterface
    {
        $generator->next();

        return $generator->current();
    }

    /**
     * @test
     * @depends itCanBulkSaveLargeDataEntities
     *
     * @param int $previouslySavedCount
     *
     * @return array
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function itCanBulkUpdateAnArrayOfLargeDataEntities(int $previouslySavedCount): array
    {
        $this->updater->setChunkSize(100);
        $this->setExtractorOnUpdater(self::TEST_ENTITY_FQN);

        $repository = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY_FQN);
        $entities   = $repository->findAll();
        $integer    = 100;
        $text       = 'blah blah blah';
        $dto        = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn(self::TEST_ENTITY_FQN);
        $dto->setText($text)->setInteger($integer);
        $this->updater->prepareEntitiesForBulkUpdate($entities);
        foreach ($entities as $entity) {
            $entity->update($dto);
        }
        $this->updater->addEntitiesToSave($entities);
        $entities = null;
        $this->updater->endBulkProcess();
        $numEntities = $repository->count();
        self::assertSame($previouslySavedCount, $numEntities);
        $reloaded = $repository->findAll();
        $dumper   = new EntityDebugDumper();
        foreach ($reloaded as $entity) {
            self::assertSame($integer, $entity->getInteger(), $dumper->dump($entity));
            self::assertSame($text, $entity->getText(), $dumper->dump($entity));
        }

        return $reloaded;
    }

    private function setExtractorOnUpdater(string $entityFqn): void
    {
        $this->updater->setExtractor(
            new class($entityFqn) implements BulkEntityUpdater\BulkEntityUpdateHelper
            {
                /**
                 * @var string
                 */
                private $entityFqn;

                public function __construct(string $entityFqn)
                {
                    $this->entityFqn = $entityFqn;
                }

                public function getTableName(): string
                {
                    return 'integer_id_key_entity';
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
}
