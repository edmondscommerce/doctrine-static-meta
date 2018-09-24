<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction
 * @medium
 */
class CreateConstraintActionTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . AbstractTest::TEST_TYPE_MEDIUM
                            . '/CreateConstraintActionTest';

    private const CONSTRAINTS_PATH = self::WORK_DIR . '/src/Validation/Constraints';


    /**
     * @test
     */
    public function itCanCreateConstraintsWithoutSuffix(): void
    {
        $this->getAction()
             ->setConstraintShortName('IsGreen')
             ->run();
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsGreenConstraint.php');
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsGreenConstraintValidator.php');
    }

    private function getAction(): CreateConstraintAction
    {
        /**
         * @var CreateConstraintAction $action
         */
        $action = $this->container->get(CreateConstraintAction::class)
                                  ->setProjectRootDirectory(self::WORK_DIR)
                                  ->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE);

        return $action;
    }

    /**
     * @test
     */
    public function itCanCreateConstraintsWithSuffix(): void
    {
        $this->getAction()
             ->setConstraintShortName('IsRedConstraint')
             ->run();
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsRedConstraint.php');
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsRedConstraintValidator.php');
    }

    public function isThrowsAnExceptionIfConstraintShortNameNotSet()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectException('You must call setContraintShortname before calling run');
        $this->getAction()->run();
    }
}
