<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

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

    /**
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function setup()
    {
        parent::setup();
        if (true !== $this->built) {
            $fieldFqn = $this->getFieldGenerator()
                             ->generateField(
                                 self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                 .AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE.'\\Name',
                                 MappingHelper::TYPE_STRING
                             );
            foreach (self::TEST_ENTITTES as $entityFqn) {
                $this->getEntityGenerator()->generateEntity($entityFqn, true);
                $this->getFieldSetter()->setEntityHasField($entityFqn, $fieldFqn);
            }
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->saverFactory = new EntitySaverFactory(
            $this->getEntityManager(),
            new EntitySaver($this->getEntityManager()),
            new NamespaceHelper()
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
        if (!$saver instanceof AbstractEntitySpecificSaver) {
            $this->fail(
                '$saver for $entityFqn '.$entityFqn.' is not an instance of AbstractEntitySpecificSaver'
            );
        }

        return $saver;
    }

    public function testRemoveAll(): void
    {
        foreach (self::TEST_ENTITTES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $saver     = $this->getEntitySpecificSaver($entityFqn);
            $loaded    = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            self::assertSame($this->generatedEntities[$entityFqn], $loaded);
            $saver->removeAll($loaded);
            $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            self::assertSame([], $reLoaded);
        }
    }

    public function testRemove(): void
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITTES));
        $saver     = $this->getEntitySpecificSaver($entityFqn);
        $loaded    = $this->getEntityManager()->getRepository($entityFqn)->findAll();
        foreach ($loaded as $entity) {
            $saver->remove($entity);
        }
        $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
        self::assertNotSame($loaded, $reLoaded);
    }

    protected function cloneEntities(array $entities): array
    {
        $clones = [];
        foreach ($entities as $entity) {
            $clones[] = clone $entity;
        }

        return $clones;
    }

    public function testSaveAll(): void
    {
        foreach (self::TEST_ENTITTES as $entityFqn) {
            $entityFqn                           = $this->getCopiedFqn($entityFqn);
            $saver                               = $this->getEntitySpecificSaver($entityFqn);
            $loaded                              = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            $this->generatedEntities[$entityFqn] = $this->cloneEntities($loaded);
            foreach ($loaded as $entity) {
                $entity->setName('name '.microtime(true));
            }
            $saver->saveAll($loaded);
            $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            self::assertNotSame($this->generatedEntities[$entityFqn], $reLoaded);
        }
    }

    public function testSave(): void
    {
        $entityFqn                           = $this->getCopiedFqn(current(self::TEST_ENTITTES));
        $saver                               = $this->getEntitySpecificSaver($entityFqn);
        $loaded                              = $this->getEntityManager()->getRepository($entityFqn)->findAll();
        $this->generatedEntities[$entityFqn] = $this->cloneEntities($loaded);
        foreach ($loaded as $entity) {
            $entity->setName('name '.microtime(true));
        }
        foreach ($loaded as $entity) {
            $saver->save($entity);
        }
        $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
        self::assertNotSame($this->generatedEntities[$entityFqn], $reLoaded);
    }
}
