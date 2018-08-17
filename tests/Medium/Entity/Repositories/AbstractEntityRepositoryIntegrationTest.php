<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;

class AbstractEntityRepositoryIntegrationTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/AbstractEntityRepositoryTest';

    public function testLoadingWithoutMetaData(): void
    {
        $entityFqn     = static::TEST_PROJECT_ROOT_NAMESPACE
                         . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                         . '\\Yet\\Another\\TestEntity';
        $repositoryFqn = static::TEST_PROJECT_ROOT_NAMESPACE
                         . '\\' . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                         . '\\Yet\\Another\\TestEntityRepository';
        $this->getEntityGenerator()->generateEntity($entityFqn);
        $this->setupCopiedWorkDir();
        $entityFqn     = $this->getCopiedFqn($entityFqn);
        $repositoryFqn = $this->getCopiedFqn($repositoryFqn);
        /**
         * @var AbstractEntityRepository $repository
         */
        $repository = new $repositoryFqn($this->getEntityManager());
        self::assertInstanceOf($repositoryFqn, $repository);
        $expected = ltrim($entityFqn, '\\');
        $actual   = $repository->getClassName();
        self::assertSame($expected, $actual);
    }
}
