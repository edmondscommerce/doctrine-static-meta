<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use Symfony\Component\Finder\SplFileInfo;

class RelationsGeneratorTest extends AbstractCodeGenerationTest
{
    const TEST_ENTITIES = [
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
        . self::TEST_PROJECT_ENTITIES_NAMESPACE
        . '\\GeneratedRelations\\Testing\\RelationsTestEntity',

        self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
        . self::TEST_PROJECT_ENTITIES_NAMESPACE
        . '\\GeneratedRelations\\ExtraTesting\\Test\\AnotherRelationsTestEntity'
    ];

    /**
     * @var \RecursiveIteratorIterator
     */
    protected $iterator;

    /**
     * @var EntityGenerator
     */
    protected $entityGenerator;

    /**
     * @var RelationsGenerator
     */
    protected $relationsGenerator;

    /**
     * @return \RecursiveIteratorIterator
     */
    protected function getIterator(): \RecursiveIteratorIterator
    {
        if (null === $this->iterator) {
            $this->iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    realpath(AbstractGenerator::RELATIONS_TEMPLATE_PATH),
                    \RecursiveDirectoryIterator::SKIP_DOTS
                )
            );
        }
        return $this->iterator;
    }

    public function setup()
    {
        parent::setup();
        $this->entityGenerator = new EntityGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        $this->relationsGenerator = new RelationsGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        foreach (self::TEST_ENTITIES as $fqn) {
            $this->entityGenerator->generateEntity($fqn);
            $this->relationsGenerator->generateRelationTraitsForEntity($fqn);
        }
    }

    public function testGenerateRelations()
    {
        /**
         * @var SplFileInfo $i
         */
        foreach ($this->getIterator() as $path => $i) {
            if ($i->isDir()) {
                continue;
            }
            $relativePath = rtrim(
                $this->getFileSystem()->makePathRelative($path, AbstractGenerator::RELATIONS_TEMPLATE_PATH),
                '/'
            );
            $relativePath = str_replace('TemplateEntity', 'RelationsTestEntity', $relativePath);
            $relativePath = str_replace('TemplateEntities', 'RelationsTestEntities', $relativePath);
            $createdFile = realpath(self::WORK_DIR)
                . '/' . self::TEST_PROJECT_ENTITIES_NAMESPACE
                . '/Traits/Relations/GeneratedRelations/Testing/RelationsTestEntity/'
                . $relativePath;
            $this->assertTemplateCorrect($createdFile);
        }
    }

    public function testSetRelationsBetweenEntities()
    {
        foreach (RelationsGenerator::RELATION_TYPES as $hasType) {
            if (false !== strpos($hasType, RelationsGenerator::PREFIX_INVERSE)) {
                //inverse types are tested implicitly
                continue;
            }
            $this->setup();
            try {
                $this->relationsGenerator->setEntityHasRelationToEntity(
                    self::TEST_ENTITIES[0],
                    $hasType,
                    self::TEST_ENTITIES[1]
                );
            } catch (\Exception $e) {
                throw new \Exception('Failed setting relations using '
                    . print_r(
                        [
                            self::TEST_ENTITIES[0],
                            $hasType,
                            self::TEST_ENTITIES[1]],
                        true
                    )
                    . "\n" . $e->getMessage()
                );
            }
        }
    }
}
