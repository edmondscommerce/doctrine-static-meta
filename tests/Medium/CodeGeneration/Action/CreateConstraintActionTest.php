<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction
 */
class CreateConstraintActionTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . AbstractTest::TEST_TYPE_MEDIUM
                            . '/CreateConstraintActionTest';

    private const CONSTRAINTS_PATH = self::WORK_DIR . '/src/Validation/Constraints';

    /**
     * @test
     * @medium
     */
    public function itCanCreateConstraintsWithoutSuffix(): void
    {
        $this->getCreateConstraintAction()->run('IsGreen');
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsGreenConstraint.php');
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsGreenConstraintValidator.php');
    }

    private function getCreateConstraintAction(): CreateConstraintAction
    {
        /**
         * @var CreateConstraintAction $createContraintAction
         */
        $createContraintAction = $this->container->get(CreateConstraintAction::class);
        $createContraintAction->setProjectRootDirectory(self::WORK_DIR);
        $createContraintAction->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE);

        return $createContraintAction;
    }

    /**
     * @test
     * @medium
     */
    public function itCanCreateConstraintsWithSuffix(): void
    {
        $this->getCreateConstraintAction()->run('IsRedConstraint');
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsRedConstraint.php');
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsRedConstraintValidator.php');
    }
}
