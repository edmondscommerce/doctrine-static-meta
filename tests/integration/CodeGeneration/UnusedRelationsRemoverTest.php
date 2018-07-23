<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use Symfony\Component\Finder\Finder;

class UnusedRelationsRemoverTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/UnusedRelationsRemoverTest';

    public const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME;

    public const TEST_ENTITIES = [
        self::TEST_ENTITY_FQN_BASE.'\\Blah\\Foo',
        self::TEST_ENTITY_FQN_BASE.'\\Bar\\Baz',
        self::TEST_ENTITY_FQN_BASE.'\\No\\Relative',
        self::TEST_ENTITY_FQN_BASE.'\\Meh',
        self::TEST_ENTITY_FQN_BASE.'\\Nested\\Something\\Ho\\Hum',
    ];
    /**
     * @var UnusedRelationsRemover
     */
    private $remover;
    /**
     * @var RelationsGenerator
     */
    private $relationsGenerator;

    /**
     * @var bool
     */
    private $built = false;

    public function setup()
    {
        parent::setup();
        if (!$this->built) {
            $this->relationsGenerator = $this->getRelationsGenerator();
            $entityGenerator          = $this->getEntityGenerator();
            foreach (self::TEST_ENTITIES as $fqn) {
                $entityGenerator->generateEntity($fqn);
                $this->relationsGenerator->generateRelationCodeForEntity($fqn);
            }
            $this->built = true;
        }
    }

    protected function setupCopiedWorkDir(): string
    {
        $return        = parent::setupCopiedWorkDir();
        $this->remover = new UnusedRelationsRemover();
        $this->relationsGenerator->setPathToProjectRoot($this->copiedWorkDir);

        return $return;
    }

    private function finder(): Finder
    {
        return new Finder();
    }

    private function finderToArrayOfPaths(Finder $finder): array
    {
        $return = [];
        foreach ($finder as $fileInfo) {
            $return[] = $fileInfo->getRealPath();
        }

        return $return;
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testItShouldRemoveAllRelationsIfNoneAreUsed(): void
    {
        $this->setupCopiedWorkDir();
        $expectedFilesRemovedCount = 75;
        $actualFilesRemoved        = $this->remover->run($this->copiedWorkDir, $this->getCopiedNamespaceRoot());
        self::assertCount($expectedFilesRemovedCount, $actualFilesRemoved);
        $expectedFilesFound = [];
        $actualFilesFound   = $this->finderToArrayOfPaths(
            $this->finder()->files()->in($this->copiedWorkDir.'/src/Entity/Relations/')
        );
        self::assertSame($expectedFilesFound, $actualFilesFound);
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testItShouldNotRemoveUsedRelations(): void
    {
        $this->relationsGenerator->setEntityHasRelationToEntity(
            self::TEST_ENTITIES[0], //Blah\Foo
            RelationsGenerator::HAS_ONE_TO_MANY,
            self::TEST_ENTITIES[1] //Bar\Baz
        );
        $this->setupCopiedWorkDir();

        $actualFilesRemoved          = $this->remover->run($this->copiedWorkDir, $this->getCopiedNamespaceRoot());
        $actualFilesRemovedBasenames = array_map('basename', $actualFilesRemoved);
        self::assertNotContains('HasBlahFooManyToOne.php', $actualFilesRemovedBasenames);
        self::assertNotContains('HasBarBazsOneToMany.php', $actualFilesRemovedBasenames);

        $expectedFilesRemovedCount = 65;
        self::assertCount($expectedFilesRemovedCount, $actualFilesRemoved);
        $expectedFilesLeftCount = 10;
        $actualFilesLeft        = $this->finderToArrayOfPaths(
            $this->finder()->files()->in($this->copiedWorkDir.'/src/Entity/Relations/')
        );
        self::assertCount($expectedFilesLeftCount, $actualFilesLeft);
    }
}
