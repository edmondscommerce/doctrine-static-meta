<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\FullProjectBuildFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class AbstractEntityRepositoryFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/AbstractEntityRepositoryFunctionalTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\TestEntity';

    private const TEST_FIELD_FQN_BASE = FullProjectBuildFunctionalTest::TEST_FIELD_NAMESPACE_BASE.'\\Traits';

    private $built = false;

    private $fields = [];

    public function buildEntityAndCopyCode(string $extra)
    {
        if (false === $this->built) {
            $entityGenerator = $this->getEntityGenerator();
            $entityGenerator->generateEntity(self::TEST_ENTITY_FQN);
            $fieldGenerator = $this->getFieldGenerator();
            foreach (MappingHelper::COMMON_TYPES as $type) {
                $this->fields[] = $fieldFqn = $fieldGenerator->generateField(
                    self::TEST_FIELD_FQN_BASE.'\\'.ucwords($type),
                    $type
                );
                $fieldGenerator->setEntityHasField(self::TEST_ENTITY_FQN, $fieldFqn);
            }

            $entityGenerator = new TestEntityGenerator(
                AbstractEntityTest::SEED,
                [],
                new \ReflectionClass(self::TEST_ENTITY_FQN),
                new EntitySaverFactory(
                    $this->getEntityManager(),
                    new EntitySaver($this->getEntityManager())
                )
            );
            foreach (range(0, 20) as $num) {
                $entityGenerator->generateEntity($this->getEntityManager(), self::TEST_ENTITY_FQN);
            }
        }
        $this->setupCopiedWorkDirAndCreateDatabase($extra);
    }

    protected function getRepository(string $extra): AbstractEntityRepository
    {
        return $this->getEntityManager()->getRepository($this->getCopiedFqn(self::TEST_ENTITY_FQN, $extra));
    }

    public function testFind()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);

    }

    public function testFindAll()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testFindBy()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testFindOneBy()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testGetClassName()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testMatching()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testCreateQueryBuilder()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testCreateNamedQuery()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testClear()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }

    public function testCount()
    {
        $this->buildEntityAndCopyCode(__FUNCTION__);
    }
}
