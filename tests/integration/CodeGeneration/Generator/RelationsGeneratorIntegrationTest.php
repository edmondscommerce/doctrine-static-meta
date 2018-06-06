<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;

class RelationsGeneratorIntegrationTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/RelationsGeneratorTest/';

    public const TEST_PROJECT_ROOT_NAMESPACE = AbstractIntegrationTest::TEST_PROJECT_ROOT_NAMESPACE
                                               .'\\RelationsGeneratorTest';

    public const TEST_ENTITY_BASKET = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                      .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Basket';

    public const TEST_ENTITY_BASKET_ITEM = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                           .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Basket\\Item';

    public const TEST_ENTITY_BASKET_ITEM_OFFER = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                                 .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Basket\\Item\\Offer';

    public const TEST_ENTITY_NESTED_THING = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                            .AbstractGenerator::ENTITIES_FOLDER_NAME
                                            .'\\GeneratedRelations\\Testing\\RelationsTestEntity';

    public const TEST_ENTITY_NESTED_THING2 = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                             .AbstractGenerator::ENTITIES_FOLDER_NAME
                                             .'\\GeneratedRelations\\ExtraTesting\\Test\\AnotherRelationsTestEntity';

    public const TEST_ENTITY_NAMESPACING_COMPANY = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                                   .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Company';

    public const TEST_ENTITY_NAMESPACING_SOME_CLIENT = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                                       .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Some\\Client';

    public const TEST_ENTITY_NAMESPACING_ANOTHER_CLIENT = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                                          .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Another\\Client';

    public const TEST_ENTITIES = [
        self::TEST_ENTITY_BASKET,
        self::TEST_ENTITY_BASKET_ITEM,
        self::TEST_ENTITY_BASKET_ITEM_OFFER,
        self::TEST_ENTITY_NESTED_THING,
        self::TEST_ENTITY_NESTED_THING2,
    ];

    public const TEST_ENTITIES_NAMESPACING = [
        self::TEST_ENTITY_NAMESPACING_COMPANY,
        self::TEST_ENTITY_NAMESPACING_SOME_CLIENT,
        self::TEST_ENTITY_NAMESPACING_ANOTHER_CLIENT,
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
    protected $reflection;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var bool
     */
    private $built = false;

    /**
     * @var string
     */
    private $copiedExtraSuffix = '';

    public function setup()
    {
        if (true !== $this->built) {
            parent::setup();
            $this->entityGenerator    = $this->getEntityGenerator();
            $this->relationsGenerator = $this->getRelationsGenerator();
            foreach (self::TEST_ENTITIES as $fqn) {
                $this->entityGenerator->generateEntity($fqn);
                $this->relationsGenerator->generateRelationCodeForEntity($fqn);
            }
            $this->built = true;
        }
        $this->setupCopiedWorkDir();
        $this->relationsGenerator->setPathToProjectRoot($this->copiedWorkDir)
                                 ->setProjectRootNamespace($this->copiedRootNamespace);
    }

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
        $this->assertSame(
            $hasTypesCounted,
            $hasTypesDefinedInConstantArray,
            'The number of defined in the constant array RelationsGenerator::HAS_TYPES is not correct:'
            ." \n\nfull diff:\n "
            .print_r($fullDiff($hasTypes, RelationsGenerator::HAS_TYPES), true)
        );
    }

    /**
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    protected function getReflection(): \ReflectionClass
    {
        if (null === $this->reflection) {
            $this->reflection = new \ReflectionClass(RelationsGenerator::class);
        }

        return $this->reflection;
    }

    /**
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testGenerateRelations()
    {
        /**
         * @var \SplFileInfo $i
         */
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            foreach ($this->relationsGenerator->getRelativePathRelationsGenerator() as $relativePath => $i) {
                if ($i->isDir()) {
                    continue;
                }
                $entityRefl          = new \ReflectionClass($entityFqn);
                $namespace           = $entityRefl->getNamespaceName();
                $className           = $entityRefl->getShortName();
                $namespaceNoEntities = substr($namespace, strpos(
                                                              $namespace,
                                                              AbstractGenerator::ENTITIES_FOLDER_NAME
                                                          ) + \strlen(AbstractGenerator::ENTITIES_FOLDER_NAME));
                $subPathNoEntites    = str_replace('\\', '/', $namespaceNoEntities);
                $plural              = ucfirst($entityFqn::getPlural());
                $singular            = ucfirst($entityFqn::getSingular());
                $relativePath        = str_replace(
                    ['TemplateEntity', 'TemplateEntities'],
                    [$singular, $plural],
                    $relativePath
                );
                $createdFile         = realpath($this->copiedWorkDir)
                                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                       .'/'.AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME
                                       .'/'.$subPathNoEntites.'/'
                                       .$className.'/'.$relativePath;
                $this->assertNoMissedReplacements($createdFile);
            }
        }
        $this->qaGeneratedCode();
    }

    /**
     * Get a an array of interfaces (short names) by reading the PHP file itself
     *
     * @param string $classFilePath
     *
     * @return array
     */
    protected function getImplementedInterfacesFromClassFile(string $classFilePath): array
    {
        $contents = file_get_contents($classFilePath);
        preg_match('%implements([^{]+){%', $contents, $matches);
        if (isset($matches[1])) {
            return array_map('trim', explode(',', $matches[1]));
        }

        return [];
    }

    /**
     * Get an array of all the interfaces for a class
     *
     * @param string $classFqn
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getOwningEntityInterfaces(string $classFqn): array
    {
        $owningReflection = new \ReflectionClass($classFqn);

        return $this->getImplementedInterfacesFromClassFile($owningReflection->getFileName());
    }

    /**
     * Get an array of the interfaces we expect an entity to implement based on the has type
     *
     * @param string $entityFqn
     * @param string $hasType
     *
     * @return array
     */
    protected function getExpectedInterfacesForEntityFqn(string $entityFqn, string $hasType): array
    {
        $expectedInterfaces   = [];
        $expectedInterfaces[] = \in_array($hasType, RelationsGenerator::HAS_TYPES_PLURAL, true)
            ? 'Has'.\ucwords($entityFqn::getPlural()).'Interface'
            : 'Has'.\ucwords($entityFqn::getSingular()).'Interface';
        if (!\in_array($hasType, RelationsGenerator::HAS_TYPES_UNIDIRECTIONAL, true)
            || \in_array($hasType, RelationsGenerator::HAS_TYPES_RECIPROCATED, true)
        ) {
            $expectedInterfaces[] = 'Reciprocates'.\ucwords($entityFqn::getSingular()).'Interface';
        }

        return $expectedInterfaces;
    }

    /**
     * Get the inverse has type name, or false if there is no inverse has type
     *
     * @param string $hasType
     *
     * @return bool|mixed|null|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getInverseHasType(string $hasType)
    {
        $inverseHasType = null;
        switch ($hasType) {
            case RelationsGenerator::HAS_ONE_TO_ONE:
            case RelationsGenerator::HAS_MANY_TO_MANY:
                return \str_replace(
                    RelationsGenerator::PREFIX_OWNING,
                    RelationsGenerator::PREFIX_INVERSE,
                    $hasType
                );
                break;

            case RelationsGenerator::HAS_INVERSE_ONE_TO_ONE:
            case RelationsGenerator::HAS_INVERSE_MANY_TO_MANY:
                $inverseHasType = \str_replace(
                    RelationsGenerator::PREFIX_INVERSE,
                    RelationsGenerator::PREFIX_OWNING,
                    $hasType
                );
                break;

            case RelationsGenerator::HAS_MANY_TO_ONE:
                $inverseHasType = RelationsGenerator::HAS_ONE_TO_MANY;
                break;
            case RelationsGenerator::HAS_ONE_TO_MANY:
                $inverseHasType = RelationsGenerator::HAS_MANY_TO_ONE;
                break;
            case RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_ONE:
            case RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_MANY:
            case RelationsGenerator::HAS_UNIDIRECTIONAL_MANY_TO_ONE:
                $inverseHasType = false;
                break;
            default:
                $this->fail('Failed getting $inverseHasType for $hasType '.$hasType);
        }

        return $inverseHasType;
    }

    /**
     * Inspect the generated class and ensure that all required interfaces have been implemented
     *
     * @param string $owningEntityFqn
     * @param string $hasType
     * @param string $ownedEntityFqn
     * @param bool   $assertInverse
     *
     * @return mixed
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD)
     */
    public function assertCorrectInterfacesSet(
        string $owningEntityFqn,
        string $hasType,
        string $ownedEntityFqn,
        bool $assertInverse = true
    ) {
        $owningInterfaces   = $this->getOwningEntityInterfaces($owningEntityFqn);
        $expectedInterfaces = $this->getExpectedInterfacesForEntityFqn($ownedEntityFqn, $hasType);

        $missingOwningInterfaces = [];
        foreach ($expectedInterfaces as $expectedInterface) {
            if (!\in_array($expectedInterface, $owningInterfaces, true)) {
                $missingOwningInterfaces[] = $expectedInterface;
            }
        }
        $this->assertEmpty(
            $missingOwningInterfaces,
            'Entity '.$owningEntityFqn.' has some expected owning interfaces missing for hasType: '
            .$hasType."\n\n"
            .print_r($missingOwningInterfaces, true)
        );

        if ($assertInverse) {
            $inverseHasType = $this->getInverseHasType($hasType);
            if (false === $inverseHasType) {
                return;
            }
            $this->assertCorrectInterfacesSet(
                $ownedEntityFqn,
                $inverseHasType,
                $owningEntityFqn,
                false
            );
        }
    }

    protected function getCopiedExtra(): string
    {
        return parent::getCopiedExtra().$this->copiedExtraSuffix;
    }

    public function testSetRelationsBetweenEntities()
    {
        $errors = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            try {
                if (false !== strpos($hasType, RelationsGenerator::PREFIX_INVERSE)) {
                    //inverse types are tested implicitly
                    continue;
                }
                $this->built             = false;
                $this->copiedExtraSuffix = $hasType;
                $this->setup();

                $this->relationsGenerator->setEntityHasRelationToEntity(
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                    $hasType,
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM)
                );
                $this->assertCorrectInterfacesSet(
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                    $hasType,
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM)
                );

                $this->relationsGenerator->setEntityHasRelationToEntity(
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                    $hasType,
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM_OFFER)
                );
                $this->assertCorrectInterfacesSet(
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                    $hasType,
                    $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM_OFFER)
                );

                $this->relationsGenerator->setEntityHasRelationToEntity(
                    $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING),
                    $hasType,
                    $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING2)
                );
                $this->assertCorrectInterfacesSet(
                    $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING),
                    $hasType,
                    $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING2)
                );
                $this->qaGeneratedCode();
            } catch (DoctrineStaticMetaException $e) {
                $errors[] = [
                    'Failed setting relations using' =>
                        [
                            $this->getCopiedFqn(self::TEST_ENTITIES[0]),
                            $hasType,
                            $this->getCopiedFqn(self::TEST_ENTITIES[1]),
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
        $this->copiedExtraSuffix   = null;
        $this->copiedRootNamespace = null;
    }

    public function testNamingCollisions()
    {
        $entityGenerator = $this->getEntityGenerator();
        $entityGenerator->setPathToProjectRoot($this->copiedWorkDir)
                        ->setProjectRootNamespace($this->copiedRootNamespace);

        foreach (self::TEST_ENTITIES_NAMESPACING as $fqn) {
            $fqn = $this->getCopiedFqn($fqn);
            $entityGenerator->generateEntity($fqn);
        }

        $this->assertNull($this->relationsGenerator->setEntityHasRelationToEntity(
            $this->getCopiedFqn(self::TEST_ENTITY_NAMESPACING_COMPANY),
            'OneToMany',
            $this->getCopiedFqn(self::TEST_ENTITY_NAMESPACING_SOME_CLIENT)
        ));

        $this->assertNull($this->relationsGenerator->setEntityHasRelationToEntity(
            $this->getCopiedFqn(self::TEST_ENTITY_NAMESPACING_COMPANY),
            'OneToMany',
            $this->getCopiedFqn(self::TEST_ENTITY_NAMESPACING_ANOTHER_CLIENT)
        ));

        $this->getSchema()->validate();
    }


}
