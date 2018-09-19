<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction
 * @medium
 */
class CreateEntityActionTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . AbstractTest::TEST_TYPE_MEDIUM
                            . '/CreateEntityActionTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . '\\ActionEntity';

    private const TEST_ENTITY_NESTED = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                       '\\Nested\\Entities\\Blah\\Blah\\Foo\\ActionEntity';

    /**
     * @test
     */
    public function itCanCreateAnEntity(): void
    {
        $this->getAction()->setEntityFqn(self::TEST_ENTITY)->run();
        foreach (
            [
                self::WORK_DIR . '/src/Entities/ActionEntity.php',
                self::WORK_DIR . '/src/Entity/Factories/AbstractEntityFactory.php',
                self::WORK_DIR . '/src/Entity/Factories/ActionEntityFactory.php',
                self::WORK_DIR . '/src/Entity/Interfaces/ActionEntityInterface.php',
                self::WORK_DIR . '/src/Entity/Repositories/AbstractEntityRepository.php',
                self::WORK_DIR . '/src/Entity/Repositories/ActionEntityRepository.php',
                self::WORK_DIR . '/tests/Assets/EntityFixtures/ActionEntityFixture.php',
                self::WORK_DIR . '/tests/Entities/AbstractEntityTest.php',
                self::WORK_DIR . '/tests/Entities/ActionEntityTest.php',
            ]
            as $expectedFilePath
        ) {
            self::assertFileExists($expectedFilePath);
        }
    }

    private function getAction(): CreateEntityAction
    {
        /**
         * @var CreateEntityAction $action
         */
        $action = $this->container->get(CreateEntityAction::class)
                                  ->setProjectRootDirectory(self::WORK_DIR)
                                  ->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE);

        return $action;
    }
}