<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository
 */
class AbstractEntityRepositoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/AbstractEntityRepositoryTest';

    /**
     * @test
     * @medium
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function loadingWithoutMetaData(): void
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
