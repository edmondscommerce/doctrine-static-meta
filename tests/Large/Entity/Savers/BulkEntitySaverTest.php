<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

class BulkEntitySaverTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/BulkEntitySaverTest';

    /**
     * @var BulkEntitySaver
     */
    private $saver;

    public function setup(): void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR, self::TEST_PROJECT_ROOT_NAMESPACE);
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->saver = new BulkEntitySaver($this->getEntityManager());
    }

    /**
     * @test
     * @large
     */
    public function itCanBulkSaveLargeDataEntities()
    {
        $this->saver->setChunkSize(100);
        $entityFqn = $this->getCopiedFqn(TestCodeGenerator::TEST_ENTITY_LARGE_DATA);
        $generator = $this->getTestEntityGeneratorFactory()
                          ->createForEntityFqn($entityFqn)
                          ->getGenerator($this->getEntityManager(), $entityFqn);
        for ($i = 0, $iMax = $this->getDataSize(); $i < $iMax; $i++) {
            $this->saver->addEntityToSave($this->getNextEntity($generator));
        }
        $this->saver->endBulkProcess();
        $numEntities = $this->getRepositoryFactory()->getRepository($entityFqn)->count();
        self::assertSame($this->getDataSize(), $numEntities);
    }

    private function getDataSize()
    {
        if ($this->isQuickTests()) {
            return 200;
        }
        if (isset($_SERVER['BulkEntitySaverTest_DataSize'])) {
            return (int)$_SERVER['BulkEntitySaverTest_DataSize'];
        }

        return 1000;
    }

    private function getNextEntity(\Generator $generator): EntityInterface
    {
        $generator->next();

        return $generator->current();
    }
}
