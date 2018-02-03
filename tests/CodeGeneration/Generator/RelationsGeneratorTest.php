<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class RelationsGeneratorTest extends AbstractTest
{
    const WORK_DIR = VAR_PATH.'/RelationsGeneratorTest/';

    const TEST_ENTITY_BASKET = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                               .self::TEST_PROJECT_ENTITIES_FOLDER.'\\Basket';

    const TEST_ENTITY_BASKET_ITEM = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                    .self::TEST_PROJECT_ENTITIES_FOLDER.'\\Basket\\Item';

    const TEST_ENTITY_BASKET_ITEM_OFFER = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                          .self::TEST_PROJECT_ENTITIES_FOLDER.'\\Basket\\Item\\Offer';

    const TEST_ENTITY_NESTED_THING = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                     .self::TEST_PROJECT_ENTITIES_FOLDER
                                     .'\\GeneratedRelations\\Testing\\RelationsTestEntity';

    const TEST_ENTITY_NESTED_THING2 = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                      .self::TEST_PROJECT_ENTITIES_FOLDER
                                      .'\\GeneratedRelations\\ExtraTesting\\Test\\AnotherRelationsTestEntity';

    const TEST_ENTITIES = [
        self::TEST_ENTITY_BASKET,
        self::TEST_ENTITY_BASKET_ITEM,
        self::TEST_ENTITY_BASKET_ITEM_OFFER,
        self::TEST_ENTITY_NESTED_THING,
        self::TEST_ENTITY_NESTED_THING2,
    ];


    /**
     * @var EntityGenerator
     */
    protected $entityGenerator;

    /**
     * @var RelationsGenerator
     */
    protected $relationsGenerator;

    /**
     * @var \ReflectionClass
     */
    protected $reflection = null;

    /**
     */
    public function testAllHasTypesInConstantArrays()
    {
        $hasTypes  = [];
        $constants = $this->getReflection()->getConstants();
        foreach ($constants as $constantName => $constantValue) {
            if (0 === strpos($constantName, 'HAS') && false === strpos($constantName, 'HAS_TYPES')) {
                $hasTypes[$constantName] = $constantValue;
            }
        }
        $hasTypesCounted                = count($hasTypes);
        $hasTypesDefinedInConstantArray = count(RelationsGenerator::HAS_TYPES);
        $fullDiff                       = function (array $arrayX, array $arrayY): array {
            $intersect = array_intersect($arrayX, $arrayY);

            return array_merge(array_diff($arrayX, $intersect), array_diff($arrayY, $intersect));
        };
        $this->assertEquals(
            $hasTypesCounted,
            $hasTypesDefinedInConstantArray,
            "The number of defined in the constant array RelationsGenerator::HAS_TYPES is not correct:"
            ." \n\nfull diff:\n "
            .print_r($fullDiff($hasTypes, RelationsGenerator::HAS_TYPES), true)
        );
    }

    protected function getReflection(): \ReflectionClass
    {
        if (null === $this->reflection) {
            $this->reflection = new \ReflectionClass(RelationsGenerator::class);
        }

        return $this->reflection;
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateRelations()
    {
        /**
         * @var \SplFileInfo $i
         */
        foreach (self::TEST_ENTITIES as $entityFqn) {
            foreach ($this->relationsGenerator->getRelativePathRelationsGenerator() as $relativePath => $i) {
                if ($i->isDir()) {
                    continue;
                }
                $entityRefl          = new \ReflectionClass($entityFqn);
                $namespace           = $entityRefl->getNamespaceName();
                $className           = $entityRefl->getShortName();
                $namespaceNoEntities = substr($namespace, strpos($namespace,
                                                                 self::TEST_PROJECT_ENTITIES_FOLDER) + strlen(self::TEST_PROJECT_ENTITIES_FOLDER));
                $subPathNoEntites    = str_replace('\\', '/', $namespaceNoEntities);
                $plural              = ucfirst($entityFqn::getPlural());
                $singular            = ucfirst($entityFqn::getSingular());
                $relativePath        = str_replace('TemplateEntity', $singular, $relativePath);
                $relativePath        = str_replace('TemplateEntities', $plural, $relativePath);
                $createdFile         = realpath(static::WORK_DIR)
                                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                       .'/'.self::TEST_PROJECT_ENTITIES_FOLDER
                                       .'/Relations/'.$subPathNoEntites.'/'
                                       .$className.'/'.$relativePath;
                $this->assertTemplateCorrect($createdFile);
            }
        }
    }

    /**
     */
    public function testSetRelationsBetweenEntities()
    {
        $errors = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
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
            } catch (DoctrineStaticMetaException $e) {
                $errors[] = [
                    'Failed setting relations using' =>
                        [
                            self::TEST_ENTITIES[0],
                            $hasType,
                            self::TEST_ENTITIES[1],
                        ],
                    'Exception message'              => $e->getMessage(),
                    'Exception trace'                => $e->getTraceAsStringRelativePath(),
                ];
            }
        }
        $this->assertEmpty(
            $errors,
            'Found '.count($errors).' errors: '
            .print_r($errors, true)
        );
    }

    public function setup()
    {
        parent::setup();
        $this->entityGenerator    = $this->getEntityGenerator();
        $this->relationsGenerator = $this->getRelationsGenerator();
        foreach (self::TEST_ENTITIES as $fqn) {
            $this->entityGenerator->generateEntity($fqn);
            $this->relationsGenerator->generateRelationCodeForEntity($fqn);
        }
    }
}
