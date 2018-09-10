<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Di\CompilerPass;

use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Di\CompilerPass\EntityDependencyPass
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory
 */
class EntityDependencyPassTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/EntityDependencyPassTest/';

    private const TEST_ENTITY_FQN = '';

    protected static $buildOnce = true;

    private $containerWithTaggedDependencies;
    /**
     * @var EntityFactory
     */
    private $entityFactory;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            $this->overrideEntityWithInjectMethod();
            self::$built = true;
        }
    }

    private function overrideEntityWithInjectMethod()
    {
    }

    /**
     * @test
     * @medium
     */
    public function theEntityIsLoadedFromTheFactoryWithDependenciesInjected()
    {
    }

    private function createContainerWithTaggedEntityDependency()
    {
        $this->containerWithTaggedDependencies = new Container();
        $this->containerWithTaggedDependencies->buildSymfonyContainer([]);
    }
}
