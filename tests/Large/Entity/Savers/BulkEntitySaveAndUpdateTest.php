<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Savers;

use Doctrine\DBAL\Tools\Dumper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

class BulkEntitySaveAndUpdateTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/BulkEntitySaveAndUpdateTest';

    private const INTEGER_ID_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_INTEGER_KEY;

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
        }
        $this->saver   = new BulkEntitySaver($this->getEntityManager());
        $this->updater = new BulkEntityUpdater($this->getEntityManager(), new MysqliConnectionFactory());
    }


    /**
     * @test
     * @large
     */
    public function itCanBulkSaveLargeDataEntities(): int
    {
        $this->createDatabase();
        $this->saver->setChunkSize(100);
        $entityFqn = self::INTEGER_ID_ENTITY;

        $generator = $this->getTestEntityGeneratorFactory()
                          ->createForEntityFqn($entityFqn)
                          ->getGenerator($this->getEntityManager(), $entityFqn);
        for ($i = 0, $iMax = $this->getDataSize(); $i < $iMax; $i++) {
            $this->saver->addEntityToSave($this->getNextEntity($generator));
        }
        $this->saver->endBulkProcess();
        $numEntities = $this->getRepositoryFactory()->getRepository($entityFqn)->count();
        self::assertSame($this->getDataSize(), $numEntities);

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

    private function getNextEntity(\Generator $generator): EntityInterface
    {
        $generator->next();

        return $generator->current();
    }

    /**
     * @test
     * @large
     */
    public function itCanBulkSaveArraysOfLargeDataEntities()
    {
        $this->saver->setChunkSize(100);
        $entityFqn = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;
        $generator = $this->getTestEntityGeneratorFactory()
                          ->createForEntityFqn($entityFqn)
                          ->getGenerator($this->getEntityManager(), $entityFqn);
        $entities  = [];
        for ($i = 0, $iMax = $this->getDataSize(); $i < $iMax; $i++) {
            $entities[] = $this->getNextEntity($generator);
        }
        $this->saver->addEntitiesToSave($entities);
        $this->saver->endBulkProcess();
        $numEntities = $this->getRepositoryFactory()->getRepository($entityFqn)->count();
        self::assertSame($this->getDataSize(), $numEntities);
    }

    /**
     * @test
     * @large
     * @depends itCanBulkSaveLargeDataEntities
     *
     * @param int $previouslySavedCount
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanBulkUpdateAnArrayOfLargeDataEntities(int $previouslySavedCount)
    {
        $entityFqn = self::INTEGER_ID_ENTITY;
        $this->updater->setChunkSize(100);
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

        $repository = $this->getRepositoryFactory()->getRepository($entityFqn);
        $entities   = $repository->findAll();
        $integer    = 100;
        $text       = 'blah blah blah';
        foreach ($entities as $entity) {
            $entity->setInteger($integer);
            $entity->setText($text);
        }
        $this->updater->addEntitiesToSave($entities);
        $entities = null;
        $this->updater->endBulkProcess();
        $numEntities = $repository->count();
        self::assertSame($previouslySavedCount, $numEntities);
        $reloaded = $repository->findAll();
        foreach ($reloaded as $entity) {
            self::assertSame($integer, $entity->getInteger(), Dumper::dump($entity));
            self::assertSame($text, $entity->getText(), Dumper::dump($entity));
        }
    }
}
