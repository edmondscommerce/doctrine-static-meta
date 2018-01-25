<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use Symfony\Component\Finder\SplFileInfo;

class RelationsGeneratorTest extends AbstractCodeGenerationTest
{
    const WORK_DIR = __DIR__ . '/../../../var/RelationsGeneratorTest';

    const TEST_ENTITY_BASKET = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
    . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Basket';

    const TEST_ENTITY_BASKET_ITEM = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
    . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Basket\\Item';

    const TEST_ENTITY_BASKET_ITEM_OFFER = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
    . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Basket\\Item\\Offer';

    const TEST_ENTITY_NESTED_THING = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
    . self::TEST_PROJECT_ENTITIES_NAMESPACE
    . '\\GeneratedRelations\\Testing\\RelationsTestEntity';

    const TEST_ENTITY_NESTED_THING2 = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
    . self::TEST_PROJECT_ENTITIES_NAMESPACE
    . '\\GeneratedRelations\\ExtraTesting\\Test\\AnotherRelationsTestEntity';

    const TEST_ENTITIES = [
        self::TEST_ENTITY_BASKET,
        self::TEST_ENTITY_BASKET_ITEM,
        self::TEST_ENTITY_BASKET_ITEM_OFFER,
        self::TEST_ENTITY_NESTED_THING,
        self::TEST_ENTITY_NESTED_THING2
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
    protected function getRelationsTraitsIterator(): \RecursiveIteratorIterator
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
        $this->entityGenerator    = new EntityGenerator(
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
        foreach (self::TEST_ENTITIES as $entityFqn) {
            foreach ($this->getRelationsTraitsIterator() as $path => $i) {
                if ($i->isDir()) {
                    continue;
                }
                $relativePath        = rtrim(
                    $this->getFileSystem()->makePathRelative($path, AbstractGenerator::RELATIONS_TEMPLATE_PATH),
                    '/'
                );
                $entityRefl          = new \ReflectionClass($entityFqn);
                $namespace           = $entityRefl->getNamespaceName();
                $className           = $entityRefl->getShortName();
                $namespaceNoEntities = substr($namespace, strpos($namespace, self::TEST_PROJECT_ENTITIES_NAMESPACE) + strlen(self::TEST_PROJECT_ENTITIES_NAMESPACE));
                $subPathNoEntites    = str_replace('\\', '/', $namespaceNoEntities);
                $plural              = ucfirst($entityFqn::getPlural());
                $singular            = ucfirst($entityFqn::getSingular());
                $relativePath        = str_replace('TemplateEntity', $singular, $relativePath);
                $relativePath        = str_replace('TemplateEntities', $plural, $relativePath);
                $createdFile         = realpath(self::WORK_DIR)
                    . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                    . '/' . self::TEST_PROJECT_ENTITIES_NAMESPACE
                    . '/Traits/Relations/' . $subPathNoEntites . '/'
                    . $className . '/' . $relativePath;
                $this->assertTemplateCorrect($createdFile);
            }
        }
    }

    public function testSetRelationsBetweenEntities()
    {
        $errors = [];
        foreach (RelationsGenerator::RELATION_TYPES as $hasType) {
            try {
                if (false !== strpos($hasType, RelationsGenerator::PREFIX_INVERSE)) {
                    //inverse types are tested implicitly
                    continue;
                }
                $this->setup();
                $this->relationsGenerator->setEntityHasRelationToEntity(
                    self::TEST_ENTITY_BASKET,
                    $hasType,
                    self::TEST_ENTITY_BASKET_ITEM
                );
                $this->relationsGenerator->setEntityHasRelationToEntity(
                    self::TEST_ENTITY_BASKET_ITEM,
                    $hasType,
                    self::TEST_ENTITY_BASKET_ITEM_OFFER
                );
                $this->relationsGenerator->setEntityHasRelationToEntity(
                    self::TEST_ENTITY_NESTED_THING,
                    $hasType,
                    self::TEST_ENTITY_NESTED_THING2
                );
                $entityManager = $this->getTestEntityManager();
                $this->assertCanBuildSchema($entityManager);
            } catch (\Exception $e) {
                $errors[] = [
                    'Failed setting relations using' =>
                        [
                            self::TEST_ENTITIES[0],
                            $hasType,
                            self::TEST_ENTITIES[1]
                        ],
                    'Exception message'              => $e->getMessage(),
                    'Exception trace'                => $e->getTraceAsString()
                ];
            }
        }
        $this->assertEmpty(
            $errors,
            'Found ' . count($errors) . ' errors: '
            . print_r($errors, true)
        );
    }
}
