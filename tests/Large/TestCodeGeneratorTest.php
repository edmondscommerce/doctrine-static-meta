<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @coversNothing
 * @large
 */
class TestCodeGeneratorTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/TestCodeGeneratorTest';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
    }

    /**
     * We need to ensure that the test code that is used everywhere is actually valid
     *
     * That's what this test is for
     *
     * @test
     */
    public function testCodeIsValid()
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
     */
    public function canCreateAllEntities(string $entityFqn)
    {
        $entity = $this->getEntityFactory()->create($entityFqn, $this->getDtoForEntityFqn($entityFqn));
        self::assertInstanceOf($entityFqn, $entity);
    }

    private function getDtoForEntityFqn(string $entityFqn): DataTransferObjectInterface
    {
        $dto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($entityFqn);
        switch ($entityFqn) {
            case TestCodeGenerator::TEST_ENTITY_NAMESPACE_BASE . TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS:
                $dto->setShortIndexedRequiredString('foo');
                break;
        }

        return $dto;
    }
}
