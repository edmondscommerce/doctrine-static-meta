<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;

class AbstractEntitySpecificSaverTest extends AbstractFunctionalTest
{

    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/AbstractEntitySpecificSaverTest';

    private const TEST_ENTITTES = [
        self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\TestOne',
        self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\Deeply\\Nested\\TestTwo',
    ];

    private $built = false;
    /**
     * @var EntitySaverFactory
     */
    private $saverFactory;
    /**
     * @var array
     */
    private $generatedEntities;

    public function setup()
    {
        parent::setup();
        if (true !== $this->built) {
            foreach (self::TEST_ENTITTES as $entityFqn) {
                $this->getEntityGenerator()->generateEntity($entityFqn, true);
            }
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->saverFactory = new EntitySaverFactory(
            $this->getEntityManager(),
            new EntitySaver($this->getEntityManager()),
            New NamespaceHelper()
        );
        foreach (self::TEST_ENTITTES as $entityFqn) {
            $entityFqn                           = $this->getCopiedFqn($entityFqn);
            $this->generatedEntities[$entityFqn] = (new TestEntityGenerator(
                100.0,
                [],
                new \ReflectionClass($entityFqn),
                $this->saverFactory
            ))->generateEntities($this->getEntityManager(), $entityFqn, 10);
        }
    }

    protected function getEntitySpecificSaver(string $entityFqn): AbstractEntitySpecificSaver
    {
        $saver = $this->saverFactory->getSaverForEntityFqn($entityFqn);
        if ($saver instanceof AbstractEntitySpecificSaver) {
            return $saver;
        }
        $this->fail(
            '$saver for $entityFqn '.$entityFqn.' is not an instance of AbstractEntitySpecificSaver'
        );
    }

    public function testRemoveAll()
    {
        foreach (self::TEST_ENTITTES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $saver     = $this->getEntitySpecificSaver($entityFqn);
            $loaded    = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            $this->assertSame($this->generatedEntities[$entityFqn], $loaded);
            $saver->removeAll($loaded);
            $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            $this->assertSame([], $reLoaded);
        }
    }

    public function testRemove()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testSaveAll()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testSave()
    {
        $this->markTestIncomplete('TODO');
    }
}
