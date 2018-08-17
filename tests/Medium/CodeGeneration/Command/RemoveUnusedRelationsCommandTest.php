<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\RemoveUnusedRelationsCommand;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use Symfony\Component\Finder\Finder;

class RemoveUnusedRelationsCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE .
                            '/RemoveUnusedRelationsCommandTest/';

    public function testGenerateRelationsNoFiltering(): void
    {
        $entityFqns = $this->generateEntities();
        $this->getRelationsGenerator()->setEntityHasRelationToEntity(
            $entityFqns[0],
            RelationsGenerator::HAS_ONE_TO_ONE,
            $entityFqns[1]
        );
        $this->setupCopiedWorkDir();
        $command = $this->container->get(RemoveUnusedRelationsCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . RemoveUnusedRelationsCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                '-' . RemoveUnusedRelationsCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->getCopiedNamespaceRoot(),
            ]
        );
        $expectedFilesFoundCount = 10;
        $actualFilesFound        = $this->finderToArrayOfPaths(
            $this->finder()->files()->in(
                $this->copiedWorkDir . '/src/Entity/Relations/'
            )
        );
        self::assertCount($expectedFilesFoundCount, $actualFilesFound);
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
}
