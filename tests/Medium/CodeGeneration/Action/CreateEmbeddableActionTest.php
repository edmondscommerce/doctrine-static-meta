<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEmbeddableAction;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use RuntimeException;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEmbeddableAction
 */
class CreateEmbeddableActionTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/CreateEmbeddableActionTest';

    /**
     * @var CreateEmbeddableAction
     */
    private mixed $action;

    public function setup():void
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
        $this->action = $this->container->get(CreateEmbeddableAction::class);
        $this->action->setProjectRootDirectory($this->copiedWorkDir)
                     ->setProjectRootNamespace($this->copiedRootNamespace);
    }

    /**
     * @test
     */
    public function itCanCreateASkeleton(): void
    {
        $this->action->setCatName('Food')
                     ->setName('Cheese')
                     ->run();

        $expectedPaths = [
            'src/Entity/Embeddable/FakerData/Food/CheeseEmbeddableFakerData.php',
            'src/Entity/Embeddable/Interfaces/Objects/Food/CheeseEmbeddableInterface.php',
            'src/Entity/Embeddable/Interfaces/Food/HasCheeseEmbeddableInterface.php',
            'src/Entity/Embeddable/Objects/Food/CheeseEmbeddable.php',
            'src/Entity/Embeddable/Traits/Food/HasCheeseEmbeddableTrait.php',
        ];
        foreach ($expectedPaths as $path) {
            self::assertFileExists($this->copiedWorkDir . $path);
        }
    }

    /**
     * @test
     */
    public function itThrowsExceptionIfNoCatName(): void
    {
        $this->expectException(RuntimeException::class);
        $this->action->setName('Cheese')
                     ->run();
    }

    /**
     * @test
     */
    public function itThrowsExceptionIfNoName(): void
    {
        $this->expectException(RuntimeException::class);
        $this->action->setCatName('Cheese')
                     ->run();
    }
}
