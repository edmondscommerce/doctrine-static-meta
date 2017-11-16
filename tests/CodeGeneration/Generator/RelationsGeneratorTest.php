<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use Symfony\Component\Finder\SplFileInfo;

class RelationsGeneratorTest extends AbstractCodeGenerationTest
{
    public function testGenerateRelations()
    {
        $fqn = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
            .self::TEST_PROJECT_ENTITIES_NAMESPACE
            .'\\GeneratedRelations\\Testing\\RelationsTestEntity';

        (new EntityGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        ))->generateEntity($fqn);

        (new RelationsGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        ))->generateRelationTraitsForEntity($fqn);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                realpath(AbstractGenerator::RELATIONS_TEMPLATE_PATH),
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );
        /**
         * @var SplFileInfo $iterator []
         */
        foreach ($iterator as $path => $i) {
            if ($i->isDir()) {
                continue;
            }
            $relativePath = rtrim(
                $this->getFileSystem()->makePathRelative($path, AbstractGenerator::RELATIONS_TEMPLATE_PATH),
                '/'
            );
            $relativePath = str_replace('TemplateEntity', 'RelationsTestEntity', $relativePath);
            $relativePath = str_replace('TemplateEntities', 'RelationsTestEntities', $relativePath);
            $createdFile  = realpath(self::WORK_DIR)
                .'/'.self::TEST_PROJECT_ENTITIES_NAMESPACE
                .'/Traits/Relations/GeneratedRelations/Testing/RelationsTestEntity/'
                .$relativePath;
            $this->assertTemplateCorrect($createdFile);
        }
    }
}
