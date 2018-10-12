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
    }

    public function allEntityFqns(): array
    {
        $return = [];
        foreach (TestCodeGenerator::TEST_ENTITIES as $entityFqn) {
            $return[$entityFqn] = [$this->getEntityFqn($entityFqn), $this->getDtoForEntityFqn($entityFqn)];
        }

        return $return;
    }

    private function getEntityFqn(string $testEntitySubFqn): string
    {
        return self::TEST_PROJECT_ROOT_NAMESPACE . $testEntitySubFqn;
    }

    private function getDtoForEntityFqn(string $entityFqn): ?DataTransferObjectInterface
    {
        switch ($entityFqn) {
            case TestCodeGenerator::TEST_ENTITY_NAMESPACE_BASE . TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS:
                return new class implements DataTransferObjectInterface
                {
                    public function getShortIndexedRequiredString(): string
                    {
                        return 'foo';
                    }
                };
                break;
        }

        return null;
    }

    /**
     * @test
     * @dataProvider allEntityFqns
     *
     * @param string                           $entityFqn
     * @param DataTransferObjectInterface|null $dto
     */
    public function canCreateAllEntities(string $entityFqn, ?DataTransferObjectInterface $dto)
    {
        $entity = $this->getEntityFactory()->create($entityFqn, $dto);
        self::assertInstanceOf($entityFqn, $entity);
    }
}
