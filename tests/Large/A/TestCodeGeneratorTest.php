<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\A;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ErrorException;
use ReflectionException;

/**
 * @coversNothing
 * @large
 */
class TestCodeGeneratorTest extends AbstractLargeTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/TestCodeGeneratorTest';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDirAndCreateDatabase();
    }

    /**
     * We need to ensure that the test code that is used everywhere is actually valid
     *
     * That's what this test is for
     *
     * @test
     */
    public function testCodeIsValid(): void
    {
        $this->qaGeneratedCode();
        $this->getSchema()->validate();
    }

    public function allEntityFqns(): array
    {
        $return = [];
        foreach (TestCodeGenerator::TEST_ENTITIES as $entityFqn) {
            $return[$entityFqn] = [$this->getEntityFqn($entityFqn)];
        }

        return $return;
    }

    private function getEntityFqn(string $testEntitySubFqn): string
    {
        return self::TEST_PROJECT_ROOT_NAMESPACE . $testEntitySubFqn;
    }

    /**
     * @test
     * @dataProvider allEntityFqns
     *
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function canCreateSaveAndLoadAllEntities(string $entityFqn): void
    {
        $entityFqn = $this->getCopiedFqn($entityFqn);
        $entity    = $this->getTestEntityGeneratorFactory()->createForEntityFqn($entityFqn)->generateEntity();
        self::assertInstanceOf($entityFqn, $entity);
        $this->getEntitySaver()->save($entity);
        $loaded = $this->getRepositoryFactory()->getRepository($entityFqn)->findAll()[0];
        self::assertInstanceOf($entityFqn, $loaded);
    }
}
