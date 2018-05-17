<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;

class AbstractEntityRepositoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/AbstractEntityRepositoryTest';

    public function testLoadingWithoutMetaData()
    {
        $entityFqn     = static::TEST_PROJECT_ROOT_NAMESPACE
                         .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
                         .'\\Yet\\Another\\TestEntity';
        $repositoryFqn = static::TEST_PROJECT_ROOT_NAMESPACE
                         .'\\'.AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                         .'\\Yet\\Another\\TestEntityRepository';
        $this->getEntityGenerator()->generateEntity($entityFqn);
        $this->setupCopiedWorkDir();
        $entityFqn     = $this->getCopiedFqn($entityFqn);
        $repositoryFqn = $this->getCopiedFqn($repositoryFqn);
        /**
         * @var AbstractEntityRepository $repository
         */
        $repository = new $repositoryFqn($this->getEntityManager());
        $this->assertInstanceOf($repositoryFqn, $repository);
        $expected = ltrim($entityFqn, '\\');
        $actual   = $repository->getClassName();
        $this->assertSame($expected, $actual);
    }
}
