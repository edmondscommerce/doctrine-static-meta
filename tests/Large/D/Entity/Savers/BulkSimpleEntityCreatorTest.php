<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\D\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater\BulkSimpleEntityCreatorHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkSimpleEntityCreator;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkSimpleEntityCreator
 */
class BulkSimpleEntityCreatorTest extends AbstractLargeTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/BulkSimpleEntityCreatorTest';

    public const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_SIMPLE;
    /**
     * @var BulkSimpleEntityCreator
     */
    private $creator;

    /**
     * @var string
     */
    private $testEntityFqn;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->creator = $this->container->get(BulkSimpleEntityCreator::class);
        $this->testEntityFqn=$this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function itCanBulkCreateSimpleEntities(): void
    {
        $tableName = $this->getEntityManager()->getClassMetadata($this->testEntityFqn)->getTableName();
        $this->creator->setHelper(new class($tableName, $this->testEntityFqn) implements BulkSimpleEntityCreatorHelper
        {
            /**
             * @var string
             */
            private $tableName;
            /**
             * @var string
             */
            private $entityFqn;

            public function __construct(string $tableName, string $entityFqn)
            {
                $this->tableName = $tableName;
                $this->entityFqn = $entityFqn;
            }

            public function getTableName(): string
            {
                return $this->tableName;
            }

            public function getEntityFqn(): string
            {
                return $this->entityFqn;
            }
        });
        $num = 10000;
        $this->creator->startBulkProcess();
        $this->creator->addEntitiesToSave($this->getArrayOfEntityDatas($num));
        $this->creator->endBulkProcess();
        $loaded = $this->getRepositoryFactory()->getRepository(self::TEST_ENTITY)->findAll();
        self::assertCount($num, $loaded);
    }

    private function getArrayOfEntityDatas($num): array
    {
        $return = [];
        foreach (range(1, $num) as $key) {
            $return[$key] = $this->getBulkEntityData($key);
        }

        return $return;
    }

    private function getBulkEntityData(int $key): array
    {
        return [
            'string' => md5('key:' . $key),
        ];
    }
}
