<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\D\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * Class AbstractEntitySpecificSaverTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Savers
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @large
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractEntitySpecificSaver
 */
class AbstractEntitySpecificSaverTest extends AbstractLargeTest
{

    public const WORK_DIR = AbstractTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE_LARGE .
                            '/AbstractEntitySpecificSaverTest';

    private const TEST_ENTITIES = [
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_LARGE_DATA,
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT,
    ];
    protected static bool $buildOnce = true;
    /**
     * @var EntitySaverFactory
     */
    private EntitySaverFactory $saverFactory;
    /**
     * @var array
     */
    private array $generatedEntities;

    public function setup():void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->saverFactory = new EntitySaverFactory(
            $this->getEntityManager(),
            new EntitySaver($this->getEntityManager()),
            new NamespaceHelper()
        );
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn                           = $this->getCopiedFqn($entityFqn);
            $this->generatedEntities[$entityFqn] =
                $this->container->get(TestEntityGeneratorFactory::class)
                                ->createForEntityFqn($entityFqn)
                                ->generateEntities(10);
            $this->saverFactory->getSaverForEntityFqn($entityFqn)->saveAll($this->generatedEntities[$entityFqn]);
        }
    }

    public function testRemoveAll(): void
    {
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $saver     = $this->getEntitySpecificSaver($entityFqn);
            /**
             * @var AbstractEntityRepository $repo
             */
            $repo   = $this->getRepositoryFactory()->getRepository($entityFqn);
            $loaded = $repo->findAll();
            $saver->removeAll($loaded);
            $reLoaded = $this->getRepositoryFactory()->getRepository($entityFqn)->findAll();
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
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITIES));
        $saver     = $this->getEntitySpecificSaver($entityFqn);
        /**
         * @var AbstractEntityRepository $repo
         */
        $repo   = $this->getRepositoryFactory()->getRepository($entityFqn);
        $loaded = $repo->findAll();
        foreach ($loaded as $entity) {
            $saver->remove($entity);
        }
        $reLoaded = $repo->findAll();
        self::assertNotSame($loaded, $reLoaded);
    }

    public function testSaveAll(): void
    {
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            $saver     = $this->getEntitySpecificSaver($entityFqn);
            /**
             * @var AbstractEntityRepository $repo
             */
            $repo                                = $this->getRepositoryFactory()->getRepository($entityFqn);
            $loaded                              = $repo->findAll();
            $this->generatedEntities[$entityFqn] = $this->cloneEntities($loaded);
            $dto                                 = $this->getEntityDtoFactory()
                                                        ->createEmptyDtoFromEntityFqn($entityFqn)
                                                        ->setString('name ' . microtime(true));
            foreach ($loaded as $entity) {
                $dto->setId($entity->getId());
                $entity->update($dto);
            }
            $saver->saveAll($loaded);
            $reLoaded = $this->getRepositoryFactory()->getRepository($entityFqn)->findAll();
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
        $entityFqn = $this->getCopiedFqn(self::TEST_ENTITIES[0]);
        $saver     = $this->getEntitySpecificSaver($entityFqn);
        /**
         * @var AbstractEntityRepository $repo
         */
        $repo                                = $this->getRepositoryFactory()->getRepository($entityFqn);
        $loaded                              = $repo->findAll();
        $this->generatedEntities[$entityFqn] = $this->cloneEntities($loaded);
        $dto                                 = $this->getEntityDtoFactory()
                                                    ->createEmptyDtoFromEntityFqn($entityFqn)
                                                    ->setString('name ' . microtime(true));
        foreach ($loaded as $entity) {
            $dto->setId($entity->getId());
            $entity->update($dto);
        }
        foreach ($loaded as $entity) {
            $saver->save($entity);
        }
        $reLoaded = $this->getRepositoryFactory()->getRepository($entityFqn)->findAll();
        self::assertNotSame($this->generatedEntities[$entityFqn], $reLoaded);
    }
}
