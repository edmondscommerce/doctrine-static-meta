<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\F\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use ReflectionException;
use SplFileInfo;
use ts\Reflection\ReflectionClass;

use function in_array;
use function str_replace;
use function strlen;
use function ucwords;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Relations\GenerateRelationCodeForEntity
 * @large
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class RelationsGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/RelationsGeneratorTest/';

    public const TEST_ENTITY_BASKET = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                      . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Basket';

    public const TEST_ENTITY_BASKET_ITEM = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                           . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Basket\\Item';

    public const TEST_ENTITY_BASKET_ITEM_OFFER = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                                 . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Basket\\Item\\Offer';

    public const TEST_ENTITY_NESTED_THING = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                            . AbstractGenerator::ENTITIES_FOLDER_NAME
                                            . '\\GeneratedRelations\\Testing\\RelationsTestEntity';

    public const TEST_ENTITY_NESTED_THING2 = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                             . AbstractGenerator::ENTITIES_FOLDER_NAME
                                             . '\\GeneratedRelations\\ExtraTesting\\Test\\AnotherRelationsTestEntity';

    public const TEST_ENTITY_NAMESPACING_COMPANY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                                   . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Company';

    public const TEST_ENTITY_NAMESPACING_SOME_CLIENT = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                                       . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Some\\Client';

    public const TEST_ENTITY_NAMESPACING_ANOTHER_CLIENT = self::TEST_PROJECT_ROOT_NAMESPACE .
                                                          '\\'
                                                          .
                                                          AbstractGenerator::ENTITIES_FOLDER_NAME .
                                                          '\\Another\\Client';

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
    protected static $buildOnce = true;
    protected static $built     = false;
    /**
     * @var EntityGenerator
     */
    private $entityGenerator;
    /**
     * @var RelationsGenerator
     */
    private $relationsGenerator;
    /**
     * @var  ReflectionClass
     */
    private $reflection;
    /**
     * @var string
     */
    private $copiedExtraSuffix = '';

    /**
     * @test
     * @large
     * @coversNothing
     */
    public function allHasTypesInConstantArrays(): void
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
        $fullDiff                       = static function (array $arrayX, array $arrayY): array {
            $intersect = array_intersect($arrayX, $arrayY);

            return array_merge(array_diff($arrayX, $intersect), array_diff($arrayY, $intersect));
        };
        self::assertSame(
            $hasTypesCounted,
            $hasTypesDefinedInConstantArray,
            'The number of defined in the constant array RelationsGenerator::HAS_TYPES is not correct:'
            . " \n\nfull diff:\n "
            . print_r($fullDiff($hasTypes, RelationsGenerator::HAS_TYPES), true)
        );
    }

    /**
     * @return  ReflectionClass
     * @throws ReflectionException
     */
    private function getReflection(): ReflectionClass
    {
        if (null === $this->reflection) {
            $this->reflection = new  ReflectionClass(RelationsGenerator::class);
        }

        return $this->reflection;
    }

    /**
     * @test
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function generateRelations(): void
    {
        /**
         * @var SplFileInfo $i
         */
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityFqn = $this->getCopiedFqn($entityFqn);
            foreach ($this->relationsGenerator->getRelativePathRelationsGenerator() as $relativePath => $i) {
                if ($i->isDir()) {
                    continue;
                }
                $entityRefl          = new  ReflectionClass($entityFqn);
                $namespace           = $entityRefl->getNamespaceName();
                $className           = $entityRefl->getShortName();
                $namespaceNoEntities = substr(
                    $namespace,
                    strpos(
                        $namespace,
                        AbstractGenerator::ENTITIES_FOLDER_NAME
                    ) + strlen(AbstractGenerator::ENTITIES_FOLDER_NAME)
                );
                $subPathNoEntites    = str_replace('\\', '/', $namespaceNoEntities);
                $plural              = ucfirst($entityFqn::getDoctrineStaticMeta()->getPlural());
                $singular            = ucfirst($entityFqn::getDoctrineStaticMeta()->getSingular());
                $relativePath        = str_replace(
                    ['TemplateEntity', 'TemplateEntities'],
                    [$singular, $plural],
                    $relativePath
                );
                $createdFile         = realpath($this->copiedWorkDir)
                                       . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
                                       . '/' . AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME
                                       . '/' . $subPathNoEntites . '/'
                                       . $className . '/' . $relativePath;
                $this->assertNoMissedReplacements($createdFile);
            }
        }
        $this->qaGeneratedCode();
    }

    /**
     * @test
     * @large
     *      * @throws ReflectionException
     * @throws DoctrineStaticMetaException
     */
    public function setRelationsBetweenEntities(): void
    {
        $errors = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            foreach ([true, false] as $requiredReciprocation) {
                try {
                    if (false !== strpos($hasType, RelationsGenerator::PREFIX_INVERSE)) {
                        //inverse types are tested implicitly
                        continue;
                    }
                    $this->copiedExtraSuffix =
                        $hasType . ($requiredReciprocation ? 'RecipRequired' : 'RecipNotRequired');
                    $this->setUp();

                    $this->relationsGenerator->setEntityHasRelationToEntity(
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                        $hasType,
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM),
                        $requiredReciprocation
                    );
                    $this->assertCorrectInterfacesSet(
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                        $hasType,
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM),
                        $requiredReciprocation
                    );

                    $this->relationsGenerator->setEntityHasRelationToEntity(
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                        $hasType,
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM_OFFER),
                        $requiredReciprocation
                    );
                    $this->assertCorrectInterfacesSet(
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET),
                        $hasType,
                        $this->getCopiedFqn(self::TEST_ENTITY_BASKET_ITEM_OFFER),
                        $requiredReciprocation
                    );

                    $this->relationsGenerator->setEntityHasRelationToEntity(
                        $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING),
                        $hasType,
                        $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING2),
                        $requiredReciprocation
                    );
                    $this->assertCorrectInterfacesSet(
                        $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING),
                        $hasType,
                        $this->getCopiedFqn(self::TEST_ENTITY_NESTED_THING2),
                        $requiredReciprocation
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
        }
        self::assertEmpty(
            $errors,
            'Found ' . count($errors) . ' errors: '
            . print_r($errors, true)
        );
        $this->copiedExtraSuffix   = null;
        $this->copiedRootNamespace = null;
    }

    public function setUp()
    {
        parent::setUp();
        $this->entityGenerator    = $this->getEntityGenerator();
        $this->relationsGenerator = $this->getRelationsGenerator();
        if (false === self::$built) {
            foreach (self::TEST_ENTITIES as $fqn) {
                $this->entityGenerator->generateEntity($fqn);
                $this->relationsGenerator->generateRelationCodeForEntity($fqn);
            }
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->relationsGenerator->setPathToProjectRoot($this->copiedWorkDir)
                                 ->setProjectRootNamespace($this->copiedRootNamespace);
    }

    /**
     * Inspect the generated class and ensure that all required interfaces have been implemented
     *
     * @param string $owningEntityFqn
     * @param string $hasType
     * @param string $ownedEntityFqn
     * @param bool   $requiredReciprocation
     * @param bool   $assertInverse
     *
     * @return void
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD)
     */
    private function assertCorrectInterfacesSet(
        string $owningEntityFqn,
        string $hasType,
        string $ownedEntityFqn,
        bool $requiredReciprocation,
        bool $assertInverse = true
    ): void {
        $owningInterfaces   = $this->getOwningEntityInterfaces($owningEntityFqn);
        $expectedInterfaces = $this->getExpectedInterfacesForEntityFqn($ownedEntityFqn, $hasType);

        $missingOwningInterfaces = [];
        foreach ($expectedInterfaces as $expectedInterface) {
            if (!in_array($expectedInterface, $owningInterfaces, true)) {
                $missingOwningInterfaces[] = $expectedInterface;
            }
        }
        self::assertEmpty(
            $missingOwningInterfaces,
            'Entity ' . $owningEntityFqn . ' has some expected owning interfaces missing for hasType: '
            . $hasType . "\n\n"
            . print_r($missingOwningInterfaces, true)
        );

        if ($assertInverse) {
            $inverseHasType = $this->getInverseHasType($hasType, $requiredReciprocation);
            if (null === $inverseHasType) {
                return;
            }
            $this->assertCorrectInterfacesSet(
                $ownedEntityFqn,
                $inverseHasType,
                $owningEntityFqn,
                false,
                false
            );
        }
    }

    /**
     * Get an array of all the interfaces for a class
     *
     * @param string $classFqn
     *
     * @return array
     * @throws ReflectionException
     */
    private function getOwningEntityInterfaces(string $classFqn): array
    {
        $owningReflection = new  ReflectionClass($classFqn);

        return $this->getImplementedInterfacesFromClassFile($owningReflection->getFileName());
    }

    /**
     * Get a an array of interfaces (short names) by reading the PHP file itself
     *
     * @param string $classFilePath
     *
     * @return array
     */
    private function getImplementedInterfacesFromClassFile(string $classFilePath): array
    {
        $interfaceFilePath = str_replace(
            [
                '/Entities/',
                '.php',
            ],
            [
                '/Entity/Interfaces/',
                'Interface.php',
            ],
            $classFilePath
        );
        $contents          = file_get_contents($interfaceFilePath);
        preg_match('%extends([^{]+){%', $contents, $matches);
        if (isset($matches[1])) {
            return array_map('trim', explode(',', $matches[1]));
        }

        return [];
    }

    /**
     * Get an array of the interfaces we expect an entity to implement based on the has type
     *
     * @param string $entityFqn
     * @param string $hasType
     *
     * @return array
     */
    private function getExpectedInterfacesForEntityFqn(string $entityFqn, string $hasType): array
    {
        $expectedInterfaces   = [];
        $required             = (
        0 === strpos($hasType, RelationsGenerator::PREFIX_REQUIRED)
            ? RelationsGenerator::PREFIX_REQUIRED
            : ''
        );
        $expectedInterfaces[] = in_array($hasType, RelationsGenerator::HAS_TYPES_PLURAL, true)
            ? 'Has' . $required . ucwords($entityFqn::getDoctrineStaticMeta()->getPlural()) . 'Interface'
            : 'Has' . $required . ucwords($entityFqn::getDoctrineStaticMeta()->getSingular()) . 'Interface';
        if (
            !in_array($hasType, RelationsGenerator::HAS_TYPES_UNIDIRECTIONAL, true)
            || in_array($hasType, RelationsGenerator::HAS_TYPES_RECIPROCATED, true)
        ) {
            $expectedInterfaces[] = 'Reciprocates' . ucwords($entityFqn::getDoctrineStaticMeta()->getSingular())
                                    . 'Interface';
        }

        return $expectedInterfaces;
    }

    /**
     * Get the inverse has type name, or false if there is no inverse has type
     *
     * @param string $hasType
     *
     * @param bool   $requiredReciprocation
     *
     * @return string|false
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function getInverseHasType(string $hasType, bool $requiredReciprocation): ?string
    {
        $inverseHasType = null;
        switch ($hasType) {
            case RelationsGenerator::HAS_ONE_TO_ONE:
            case RelationsGenerator::HAS_MANY_TO_MANY:
            case RelationsGenerator::HAS_REQUIRED_ONE_TO_ONE:
            case RelationsGenerator::HAS_REQUIRED_MANY_TO_MANY:
                $inverseHasType = str_replace(
                    RelationsGenerator::PREFIX_OWNING,
                    RelationsGenerator::PREFIX_INVERSE,
                    $hasType
                );
                break;

            case RelationsGenerator::HAS_INVERSE_ONE_TO_ONE:
            case RelationsGenerator::HAS_INVERSE_MANY_TO_MANY:
            case RelationsGenerator::HAS_REQUIRED_INVERSE_ONE_TO_ONE:
            case RelationsGenerator::HAS_REQUIRED_INVERSE_MANY_TO_MANY:
                $inverseHasType = str_replace(
                    RelationsGenerator::PREFIX_INVERSE,
                    RelationsGenerator::PREFIX_OWNING,
                    $hasType
                );
                break;

            case RelationsGenerator::HAS_MANY_TO_ONE:
            case RelationsGenerator::HAS_REQUIRED_MANY_TO_ONE:
                $inverseHasType = RelationsGenerator::HAS_ONE_TO_MANY;
                break;
            case RelationsGenerator::HAS_ONE_TO_MANY:
            case RelationsGenerator::HAS_REQUIRED_ONE_TO_MANY:
                $inverseHasType = RelationsGenerator::HAS_MANY_TO_ONE;
                break;
            case RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_ONE:
            case RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_MANY:
            case RelationsGenerator::HAS_UNIDIRECTIONAL_MANY_TO_ONE:
            case RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_ONE:
            case RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_MANY:
            case RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_MANY_TO_ONE:
                return null;
            default:
                $this->fail('Failed getting $inverseHasType for $hasType ' . $hasType);
        }
        if (true === $requiredReciprocation && 0 === strpos($inverseHasType, RelationsGenerator::PREFIX_REQUIRED)) {
            return $inverseHasType;
        }
        if (true === $requiredReciprocation && 0 !== strpos($inverseHasType, RelationsGenerator::PREFIX_REQUIRED)) {
            return RelationsGenerator::PREFIX_REQUIRED . $inverseHasType;
        }
        if (false === $requiredReciprocation && 0 !== strpos($inverseHasType, RelationsGenerator::PREFIX_REQUIRED)) {
            return $inverseHasType;
        }
        if (false === $requiredReciprocation && 0 === strpos($inverseHasType, RelationsGenerator::PREFIX_REQUIRED)) {
            return substr($inverseHasType, 8);
        }
    }

    protected function getCopiedNamespaceRoot(): string
    {
        return parent::getCopiedNamespaceRoot() . $this->copiedExtraSuffix;
    }
}
