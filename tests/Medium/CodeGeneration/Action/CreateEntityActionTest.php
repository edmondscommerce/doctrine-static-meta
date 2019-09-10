<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use InvalidArgumentException;

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
                                       '\\Nested\\Blah\\Blah\\Foo\\ActionEntity';

    public function providePluralEntityFqns(): array
    {
        return [
            'Products' => [
                self::TEST_ENTITIES_ROOT_NAMESPACE . '\\Products',
                'Your Entity Name must be Singular, eg not Products but Product',
            ],
            'Person'   => [
                self::TEST_ENTITIES_ROOT_NAMESPACE . '\\People',
                'Your Entity Name must be Singular, eg not People but Person',
            ],
            'Cars'     => [
                self::TEST_ENTITIES_ROOT_NAMESPACE . '\\Cars',
                'Your Entity Name must be Singular, eg not Cars but Car',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providePluralEntityFqns
     *
     * @param string $entityFqn
     * @param string $expectedExceptionMessage
     */
    public function itEnsuresSingularEntityNames(string $entityFqn, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->getAction()->setEntityFqn($entityFqn);
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

    /**
     * @test
     */
    public function itCanCreateAnEntity(): void
    {
        $this->getAction()->setEntityFqn(self::TEST_ENTITY)->run();
        foreach ([
                     self::WORK_DIR . '/src/Entities/ActionEntity.php',
                     self::WORK_DIR . '/src/Entity/Factories/AbstractEntityFactory.php',
                     self::WORK_DIR . '/src/Entity/Factories/ActionEntityFactory.php',
                     self::WORK_DIR . '/src/Entity/Factories/ActionEntityDtoFactory.php',
                     self::WORK_DIR . '/src/Entity/Interfaces/ActionEntityInterface.php',
                     self::WORK_DIR . '/src/Entity/DataTransferObjects/ActionEntityDto.php',
                     self::WORK_DIR . '/src/Entity/Repositories/AbstractEntityRepository.php',
                     self::WORK_DIR . '/src/Entity/Repositories/ActionEntityRepository.php',
                     self::WORK_DIR . '/tests/Assets/Entity/Fixtures/ActionEntityFixture.php',
                     self::WORK_DIR . '/tests/Entities/AbstractEntityTest.php',
                     self::WORK_DIR . '/tests/Entities/ActionEntityTest.php',
                 ] as $expectedFilePath) {
            self::assertFileExists($expectedFilePath);
        }
        $this->qaGeneratedCode();
    }

    /**
     * @test
     */
    public function itCanCreateANestedEntity(): void
    {
        $this->getAction()->setEntityFqn(self::TEST_ENTITY_NESTED)->run();
        foreach ([
                     self::WORK_DIR . '/src/Entities/Nested/Blah/Blah/Foo/ActionEntity.php',
                     self::WORK_DIR . '/src/Entity/Factories/AbstractEntityFactory.php',
                     self::WORK_DIR . '/src/Entity/Factories/Nested/Blah/Blah/Foo/ActionEntityFactory.php',
                     self::WORK_DIR . '/src/Entity/Factories/Nested/Blah/Blah/Foo/ActionEntityDtoFactory.php',
                     self::WORK_DIR . '/src/Entity/Interfaces/Nested/Blah/Blah/Foo/ActionEntityInterface.php',
                     self::WORK_DIR . '/src/Entity/Repositories/AbstractEntityRepository.php',
                     self::WORK_DIR .
                     '/src/Entity/Repositories/Nested/Blah/Blah/Foo/ActionEntityRepository.php',
                     self::WORK_DIR .
                     '/tests/Assets/Entity/Fixtures/Nested/Blah/Blah/Foo/ActionEntityFixture.php',
                     self::WORK_DIR . '/tests/Entities/AbstractEntityTest.php',
                     self::WORK_DIR . '/tests/Entities/Nested/Blah/Blah/Foo/ActionEntityTest.php',
                 ] as $expectedFilePath) {
            self::assertFileExists($expectedFilePath);
        }
        $this->qaGeneratedCode();
    }
}
