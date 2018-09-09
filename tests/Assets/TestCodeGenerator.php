<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use Composer\Autoload\ClassLoader;
use EdmondsCommerce\DoctrineStaticMeta\Builder\Builder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Filesystem\Filesystem;

class TestCodeGenerator
{
    public const TEST_PROJECT_ROOT_NAMESPACE = 'Test\\Code\\Generator';
    public const TEST_ENTITY_NAMESPACE_BASE  = self::TEST_PROJECT_ROOT_NAMESPACE
                                               . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME;

    public const TEST_ENTITY_PERSON                      = self::TEST_ENTITY_NAMESPACE_BASE . '\\Person';
    public const TEST_ENTITY_ATTRIBUTES_ADDRESS          = self::TEST_ENTITY_NAMESPACE_BASE . '\\Attributes\\Address';
    public const TEST_ENTITY_EMAIL                       = self::TEST_ENTITY_NAMESPACE_BASE . '\\Attributes\\Email';
    public const TEST_ENTITY_COMPANY                     = self::TEST_ENTITY_NAMESPACE_BASE . '\\Company';
    public const TEST_ENTITY_DIRECTOR                    = self::TEST_ENTITY_NAMESPACE_BASE . '\\Company\\Director';
    public const TEST_ENTITY_ORDER                       = self::TEST_ENTITY_NAMESPACE_BASE . '\\Order';
    public const TEST_ENTITY_ORDER_ADDRESS               = self::TEST_ENTITY_NAMESPACE_BASE . '\\Order\\Address';
    public const TEST_ENTITY_NAME_SPACING_SOME_CLIENT    = self::TEST_ENTITY_NAMESPACE_BASE . '\\Some\\Client';
    public const TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT = self::TEST_ENTITY_NAMESPACE_BASE
                                                           . '\\Another\\Deeply\\Nested\\Client';
    public const TEST_ENTITY_LARGE_DATA                  = self::TEST_ENTITY_NAMESPACE_BASE . '\\Large\\Data';
    public const TEST_ENTITY_LARGE_PROPERTIES            = self::TEST_ENTITY_NAMESPACE_BASE . '\\Large\\Property';
    public const TEST_ENTITY_LARGE_RELATIONS             = self::TEST_ENTITY_NAMESPACE_BASE . '\\Large\\Relation';
    public const TEST_ENTITIES                           = [
        self::TEST_ENTITY_PERSON,
        self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
        self::TEST_ENTITY_EMAIL,
        self::TEST_ENTITY_COMPANY,
        self::TEST_ENTITY_DIRECTOR,
        self::TEST_ENTITY_ORDER,
        self::TEST_ENTITY_ORDER_ADDRESS,
        self::TEST_ENTITY_NAME_SPACING_SOME_CLIENT,
        self::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT,
        self::TEST_ENTITY_LARGE_DATA,
        self::TEST_ENTITY_LARGE_PROPERTIES,
        self::TEST_ENTITY_LARGE_RELATIONS,
    ];
    public const TEST_FIELD_NAMESPACE_BASE               = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Fields';
    public const TEST_FIELD_TRAIT_NAMESPACE              = self::TEST_FIELD_NAMESPACE_BASE . '\\Traits\\';

    public const TEST_RELATIONS = [
        [
            self::TEST_ENTITY_PERSON,
            RelationsGenerator::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
            self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
        ],
        [self::TEST_ENTITY_PERSON, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_EMAIL],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_MANY_TO_MANY, self::TEST_ENTITY_DIRECTOR],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_ATTRIBUTES_ADDRESS],
        [self::TEST_ENTITY_COMPANY, RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_MANY, self::TEST_ENTITY_EMAIL],
        [self::TEST_ENTITY_DIRECTOR, RelationsGenerator::HAS_ONE_TO_ONE, self::TEST_ENTITY_PERSON],
        [self::TEST_ENTITY_ORDER, RelationsGenerator::HAS_MANY_TO_ONE, self::TEST_ENTITY_PERSON],
        [self::TEST_ENTITY_ORDER, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_ORDER_ADDRESS],
        [
            self::TEST_ENTITY_ORDER_ADDRESS,
            RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_ONE,
            self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
        ],
        [
            self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_ONE_TO_ONE,
            self::TEST_ENTITY_NAME_SPACING_SOME_CLIENT,
        ],
        [
            self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_ONE_TO_ONE,
            self::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT,
        ],
        [
            self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
            self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
        ],
        [self::TEST_ENTITY_LARGE_RELATIONS, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_EMAIL],
        [self::TEST_ENTITY_LARGE_RELATIONS, RelationsGenerator::HAS_MANY_TO_MANY, self::TEST_ENTITY_DIRECTOR],
        [
            self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_MANY,
            self::TEST_ENTITY_LARGE_DATA,
        ],
        [self::TEST_ENTITY_LARGE_RELATIONS, RelationsGenerator::HAS_ONE_TO_ONE, self::TEST_ENTITY_PERSON],
        [self::TEST_ENTITY_LARGE_RELATIONS, RelationsGenerator::HAS_MANY_TO_ONE, self::TEST_ENTITY_LARGE_PROPERTIES],
        [self::TEST_ENTITY_LARGE_RELATIONS, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_ORDER_ADDRESS],
        [
            self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_UNIDIRECTIONAL_ONE_TO_ONE,
            self::TEST_ENTITY_COMPANY,
        ],
    ];

    public const LARGE_DATA_FIELDS = [
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataOne',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataTwo',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataThree',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataFour',
    ];

    public const LARGE_PROPERTIES_FIELDS = [
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData001',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData002',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData003',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData004',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData005',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData006',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData007',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData008',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData009',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData010',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData011',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData012',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData013',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData014',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData015',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData016',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData017',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData018',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData019',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData020',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData021',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData022',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData023',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData024',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData025',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData026',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData027',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData028',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData029',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData030',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData031',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData032',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData033',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData034',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData035',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData036',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData037',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData038',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Properties\\LargeData039',
    ];

    public const TEST_FIELD_FQN_BASE = self::TEST_FIELD_NAMESPACE_BASE . '\\Traits';
    public const BUILD_DIR           = AbstractTest::VAR_PATH . '/testCode';

    /**
     * @var Builder
     */
    protected $builder;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var FindAndReplaceHelper
     */
    protected $findAndReplaceHelper;

    public function __construct(Builder $builder, Filesystem $filesystem, FindAndReplaceHelper $findAndReplaceHelper)
    {
        $this->filesystem = $filesystem;
        $this->initBuildDir();
        $this->builder = $builder->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE)
                                 ->setPathToProjectRoot(self::BUILD_DIR);
        $this->buildOnce();
        $this->findAndReplaceHelper = $findAndReplaceHelper;
    }

    private function initBuildDir(): void
    {

        if (!is_dir(self::BUILD_DIR)) {
            $this->filesystem->mkdir(self::BUILD_DIR);
        }
    }

    public function buildOnce(): void
    {
        if ($this->isBuilt()) {
            return;
        }
        $this->extendAutoloader();
        $this->buildEntitiesAndAssignCommonFields($this->buildCommonTypeFields());
        $this->updateLargeDataEntity();
        $this->updateLargePropertiesEntity();
        $this->setRelations();
        $this->resetAutoloader();
    }

    private function isBuilt(): bool
    {
        return is_dir(self::BUILD_DIR . '/src');
    }

    private function extendAutoloader(): void
    {
        $testLoader = new class(self::TEST_PROJECT_ROOT_NAMESPACE) extends ClassLoader
        {
            /**
             * @var string
             */
            protected $namespace;

            public function __construct(string $namespace)
            {
                $this->namespace = $namespace;
            }

            public function loadClass($class)
            {
                if (false === strpos($class, $this->namespace)) {
                    return false;
                }
                $found = parent::loadClass($class);
                if (false === $found || null === $found) {
                    //good point to set a breakpoint
                    return $found;
                }

                return $found;
            }
        };
        $testLoader->addPsr4(self::TEST_PROJECT_ROOT_NAMESPACE . '\\', self::BUILD_DIR . '/src', true);
        $testLoader->addPsr4(self::TEST_PROJECT_ROOT_NAMESPACE . '\\', self::BUILD_DIR . '/tests', true);
        $testLoader->register();
    }

    private function buildEntitiesAndAssignCommonFields(array $fields): void
    {

        $entityGenerator = $this->builder->getEntityGenerator();
        $fieldSetter     = $this->builder->getFieldSetter();

        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityGenerator->generateEntity($entityFqn);
            foreach ($fields as $fieldFqn) {
                $fieldSetter->setEntityHasField($entityFqn, $fieldFqn);
            }
        }
    }

    /**
     * Build the fields and return an array of Field Trait FQNs
     *
     * @return array|string[]
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    private function buildCommonTypeFields(): array
    {
        $fields         = [];
        $fieldGenerator = $this->builder->getFieldGenerator();

        foreach (MappingHelper::COMMON_TYPES as $type) {
            $fields[] = $fieldGenerator->generateField(
                self::TEST_FIELD_FQN_BASE . '\\' . ucwords($type),
                $type
            );
        }

        return $fields;
    }

    private function updateLargeDataEntity(): void
    {
        $fieldGenerator = $this->builder->getFieldGenerator();
        $fieldSetter    = $this->builder->getFieldSetter();
        foreach (self::LARGE_DATA_FIELDS as $field) {
            $fieldSetter->setEntityHasField(
                self::TEST_ENTITY_LARGE_DATA,
                $fieldGenerator->generateField($field, MappingHelper::TYPE_TEXT)
            );
        }
    }

    private function updateLargePropertiesEntity(): void
    {
        $fieldGenerator = $this->builder->getFieldGenerator();
        $fieldSetter    = $this->builder->getFieldSetter();
        foreach (self::LARGE_PROPERTIES_FIELDS as $field) {
            $fieldSetter->setEntityHasField(
                self::TEST_ENTITY_LARGE_DATA,
                $fieldGenerator->generateField($field, MappingHelper::TYPE_BOOLEAN)
            );
        }
    }

    private function setRelations(): void
    {
        $relationsGenerator = $this->builder->getRelationsGenerator();

        foreach (self::TEST_RELATIONS as $relation) {
            $relationsGenerator->setEntityHasRelationToEntity(...$relation);
        }
    }

    private function resetAutoloader(): void
    {
        $registered = \spl_autoload_functions();
        $loader     = array_pop($registered);
        \spl_autoload_unregister($loader);
    }

    public function copyTo(
        string $destinationPath,
        string $replaceNamespace = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE
    ): void {
        $this->filesystem->mirror(self::BUILD_DIR, $destinationPath);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($destinationPath));

        foreach ($iterator as $info) {
            /**
             * @var \SplFileInfo $info
             */
            if (false === $info->isFile()) {
                continue;
            }
            $contents = file_get_contents($info->getPathname());

            $updated = \preg_replace(
                '%(use|namespace)\s+?'
                . $this->findAndReplaceHelper->escapeSlashesForRegex(self::TEST_PROJECT_ROOT_NAMESPACE)
                . '\\\\%',
                '$1 ' . $replaceNamespace . '\\',
                $contents
            );
            file_put_contents($info->getPathname(), $updated);
        }
    }
}
