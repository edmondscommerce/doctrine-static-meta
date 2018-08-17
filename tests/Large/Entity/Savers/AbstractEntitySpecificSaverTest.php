<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Savers;

use Doctrine\Common\Cache\ArrayCache;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

/**
 * Class AbstractEntitySpecificSaverTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Savers
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @large
 */
class AbstractEntitySpecificSaverTest extends AbstractLargeTest
{

    public const WORK_DIR = AbstractTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE .
                            '/AbstractEntitySpecificSaverTest';

    private const TEST_ENTITTES = [
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\TestOne',
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Deeply\\Nested\\TestTwo',
    ];

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
        $fieldFqn = $this->getFieldGenerator()
                         ->generateField(
                             self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                             . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE . '\\Name',
                             MappingHelper::TYPE_STRING
                         );
        foreach (self::TEST_ENTITTES as $entityFqn) {
            $this->getEntityGenerator()->generateEntity($entityFqn, true);
            $this->getFieldSetter()->setEntityHasField($entityFqn, $fieldFqn);
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
                new  \ts\Reflection\ReflectionClass($entityFqn),
                $this->saverFactory,
                new EntityValidatorFactory(new DoctrineCache(new ArrayCache()))
            ))->generateEntities($this->getEntityManager(), $entityFqn, 10);
        }
    }

    public function testRemoveAll(): void
    {
        foreach (self::TEST_ENTITTES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $saver     = $this->getEntitySpecificSaver($entityFqn);
            /**
             * @var AbstractEntityRepository $repo
             */
            $repo   = $this->getEntityManager()->getRepository($entityFqn);
            $loaded = $repo->findAll();
            self::assertSame($this->generatedEntities[$entityFqn], $loaded);
            $saver->removeAll($loaded);
            $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            self::assertSame([], $reLoaded);
        }
    }

    protected function getEntitySpecificSaver(string $entityFqn): EntitySaverInterface
    {
        $saver = $this->saverFactory->getSaverForEntityFqn($entityFqn);
        if (!$saver instanceof EntitySaverInterface) {
            $this->fail(
                '$saver for $entityFqn ' . $entityFqn . ' is not an instance of EntitySaverInterface'
            );
        }

        return $saver;
    }

    public function testRemove(): void
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITTES));
        $saver     = $this->getEntitySpecificSaver($entityFqn);
        /**
         * @var AbstractEntityRepository $repo
         */
        $repo   = $this->getEntityManager()->getRepository($entityFqn);
        $loaded = $repo->findAll();
        foreach ($loaded as $entity) {
            $saver->remove($entity);
        }
        $reLoaded = $repo->findAll();
        self::assertNotSame($loaded, $reLoaded);
    }

    public function testSaveAll(): void
    {
        foreach (self::TEST_ENTITTES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $saver     = $this->getEntitySpecificSaver($entityFqn);
            /**
             * @var AbstractEntityRepository $repo
             */
            $repo                                = $this->getEntityManager()->getRepository($entityFqn);
            $loaded                              = $repo->findAll();
            $this->generatedEntities[$entityFqn] = $this->cloneEntities($loaded);
            foreach ($loaded as $entity) {
                $entity->setName('name ' . microtime(true));
            }
            $saver->saveAll($loaded);
            $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
            self::assertNotSame($this->generatedEntities[$entityFqn], $reLoaded);
        }
    }

    protected function cloneEntities(array $entities): array
    {
        $clones = [];
        foreach ($entities as $entity) {
            $clones[] = clone $entity;
        }

        return $clones;
    }

    public function testSave(): void
    {
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITTES));
        $saver     = $this->getEntitySpecificSaver($entityFqn);
        /**
         * @var AbstractEntityRepository $repo
         */
        $repo                                = $this->getEntityManager()->getRepository($entityFqn);
        $loaded                              = $repo->findAll();
        $this->generatedEntities[$entityFqn] = $this->cloneEntities($loaded);
        foreach ($loaded as $entity) {
            $entity->setName('name ' . microtime(true));
        }
        foreach ($loaded as $entity) {
            $saver->save($entity);
        }
        $reLoaded = $this->getEntityManager()->getRepository($entityFqn)->findAll();
        self::assertNotSame($this->generatedEntities[$entityFqn], $reLoaded);
    }
}
