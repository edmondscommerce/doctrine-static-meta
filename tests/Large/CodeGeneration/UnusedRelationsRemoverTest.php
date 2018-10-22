<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Symfony\Component\Finder\Finder;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover
 * @uses \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper
 * @uses \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator
 */
class UnusedRelationsRemoverTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/UnusedRelationsRemoverTest';

    public const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE .
                                        '\\' .
                                        AbstractGenerator::ENTITIES_FOLDER_NAME;

    protected static $buildOnce = true;
    /**
     * @var UnusedRelationsRemover
     */
    private $remover;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR, self::TEST_PROJECT_ROOT_NAMESPACE);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->remover = new UnusedRelationsRemover(
            $this->container->get(NamespaceHelper::class),
            $this->container->get(Config::class)
        );
    }


    private function finderToArrayOfPaths(Finder $finder): array
    {
        $return = [];
        foreach ($finder as $fileInfo) {
            $return[] = $fileInfo->getRealPath();
        }

        return $return;
    }

    private function finder(): Finder
    {
        return new Finder();
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover
     */
    public function itShouldRemoveRelationsThatAreNotBeingUsed(): void
    {
        $actualFilesRemoved          = $this->remover->run($this->copiedWorkDir, $this->getCopiedNamespaceRoot());
        $actualFilesRemovedBasenames = array_map('basename', $actualFilesRemoved);
        self::assertNotContains('HasSomeClientOwningOneToOne.php', $actualFilesRemovedBasenames);
        self::assertNotContains('HasAnotherDeeplyNestedClientOwningOneToOne.php', $actualFilesRemovedBasenames);

        $expectedFilesRemovedCount = 184;
        self::assertCount($expectedFilesRemovedCount, $actualFilesRemoved);
        $expectedFilesLeftCount = 152;
        $actualFilesLeft        = $this->finderToArrayOfPaths(
            $this->finder()->files()->in($this->copiedWorkDir . '/src/Entity/Relations/')
        );
        self::assertCount($expectedFilesLeftCount, $actualFilesLeft);
    }
}
