<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use Composer\Autoload\ClassLoader;
use EdmondsCommerce\DoctrineStaticMeta\Builder\Builder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Attribute\HasWeightEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EmailAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestCodeGenerator
{

    public const   TEST_PROJECT_ROOT_NAMESPACE = 'Test\\Code\\Generator';
    public const   TEST_ENTITY_NAMESPACE_BASE  = '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME;
    private const  TEST_FIELD_NAMESPACE_BASE   = '\\Entity\\Fields';
    private const  TEST_FIELD_FQN_BASE         = self::TEST_FIELD_NAMESPACE_BASE . '\\Traits';

    public const TEST_ENTITY_PERSON                      = '\\Person';
    public const TEST_ENTITY_ATTRIBUTES_ADDRESS          = '\\Attributes\\Address';
    public const TEST_ENTITY_EMAIL                       = '\\Attributes\\Email';
    public const TEST_ENTITY_COMPANY                     = '\\Company';
    public const TEST_ENTITY_DIRECTOR                    = '\\Company\\Director';
    public const TEST_ENTITY_ORDER                       = '\\Order';
    public const TEST_ENTITY_ORDER_ADDRESS               = '\\Order\\Address';
    public const TEST_ENTITY_NAME_SPACING_SOME_CLIENT    = '\\Some\\Client';
    public const TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT = '\\Another\\Deeply\\Nested\\Client';
    public const TEST_ENTITY_LARGE_DATA                  = '\\Large\\Data';
    public const TEST_ENTITY_LARGE_PROPERTIES            = '\\Large\\Property';
    public const TEST_ENTITY_LARGE_RELATIONS             = '\\Large\\Relation';
    public const TEST_ENTITY_ALL_ARCHETYPE_FIELDS        = '\\All\\StandardLibraryFields\\TestEntity';
    public const TEST_ENTITY_ALL_EMBEDDABLES             = '\\AllEmbeddable';

    public const TEST_ENTITIES = [
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_PERSON,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_EMAIL,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_COMPANY,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_DIRECTOR,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ORDER,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ORDER_ADDRESS,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_NAME_SPACING_SOME_CLIENT,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_DATA,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_PROPERTIES,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ALL_ARCHETYPE_FIELDS,
        self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ALL_EMBEDDABLES,
    ];


    private const TEST_RELATIONS = [
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_PERSON,
            RelationsGenerator::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
            false,
        ],
//        [
//            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_PERSON,
//            RelationsGenerator::HAS_REQUIRED_ONE_TO_MANY,
//            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ALL_ARCHETYPE_FIELDS,
//            false,
//        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_PERSON,
            RelationsGenerator::HAS_REQUIRED_ONE_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_EMAIL,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_REQUIRED_MANY_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_DIRECTOR,
            true,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_REQUIRED_ONE_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_EMAIL,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_DIRECTOR,
            RelationsGenerator::HAS_REQUIRED_ONE_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_PERSON,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ORDER,
            RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_MANY_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_PERSON,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ORDER,
            RelationsGenerator::HAS_REQUIRED_ONE_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ORDER_ADDRESS,
            true,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ORDER_ADDRESS,
            RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_ONE_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_NAME_SPACING_SOME_CLIENT,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_COMPANY,
            RelationsGenerator::HAS_ONE_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ATTRIBUTES_ADDRESS,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_REQUIRED_ONE_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_EMAIL,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_MANY_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_DIRECTOR,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_DATA,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_ONE_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_PERSON,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_REQUIRED_MANY_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_PROPERTIES,
            true,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_ONE_TO_MANY,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_ORDER_ADDRESS,
            false,
        ],
        [
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_LARGE_RELATIONS,
            RelationsGenerator::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_ONE,
            self::TEST_ENTITY_NAMESPACE_BASE . self::TEST_ENTITY_COMPANY,
            false,
        ],
    ];

    private const LARGE_DATA_FIELDS = [
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataOne',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataTwo',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataThree',
        self::TEST_FIELD_FQN_BASE . '\\Large\\Data\\LargeDataFour',
    ];

    private const LARGE_PROPERTIES_FIELDS = [
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

    private const  BUILD_DIR                      = AbstractTest::VAR_PATH . '/../testCode';
    private const  BUILD_DIR_TMP_B1               = AbstractTest::VAR_PATH . '/../testCodeTmp1';
    private const  BUILD_DIR_TMP_B2               = AbstractTest::VAR_PATH . '/../testCodeTmp2';
    private const  TEST_PROJECT_ROOT_NAMESPACE_B1 = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Tmp1';
    private const  TEST_ENTITY_NAMESPACE_BASE_B1  = self::TEST_PROJECT_ROOT_NAMESPACE_B1 .
                                                    self::TEST_ENTITY_NAMESPACE_BASE;
    private const  TEST_PROJECT_ROOT_NAMESPACE_B2 = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Tmp2';
    private const  BUILD_HASH_FILE                = self::BUILD_DIR . '/.buildHash';

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
    /**
     * @var CodeCopier
     */
    private $codeCopier;

    public function __construct(
        Builder $builder,
        Filesystem $filesystem,
        FindAndReplaceHelper $findAndReplaceHelper
    ) {
        $this->builder    = $builder;
        $this->filesystem = $filesystem;
        $this->codeCopier = new CodeCopier($this->filesystem, $findAndReplaceHelper);
        $this->initBuildDir();
        $this->buildOnce();
    }

    private function initBuildDir(): void
    {

        if (!is_dir(self::BUILD_DIR)) {
            $this->filesystem->mkdir(self::BUILD_DIR);
        }
    }

    private function buildOnce(): void
    {
        if ($this->isBuilt()) {
            return;
        }
        $this->firstBuild();
        $this->secondBuild();
        $this->filesystem->remove(self::BUILD_DIR_TMP_B1);
        $this->filesystem->remove(self::BUILD_DIR_TMP_B2);
        $this->setBuildHash();
    }

    /**
     * Check that a file exists in the build directory which contains the md5 hash of this class.
     *
     * This means that if we update this class, the hash changes and the built files are invalid and will be nuked
     *
     * @return bool
     */
    private function isBuilt(): bool
    {
        return file_exists(self::BUILD_HASH_FILE) && $this->validateBuildHash();
    }

    private function validateBuildHash(): bool
    {
        return md5(\ts\file_get_contents(__FILE__)) === \ts\file_get_contents(self::BUILD_HASH_FILE);
    }

    private function firstBuild(): void
    {
        $this->emptyDir(self::BUILD_DIR_TMP_B1);
        $this->builder->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE_B1)
                      ->setPathToProjectRoot(self::BUILD_DIR_TMP_B1);
        $this->filesystem->remove(self::BUILD_DIR_TMP_B1);
        $this->filesystem->mkdir(self::BUILD_DIR_TMP_B1);
        $this->extendAutoloader(self::TEST_PROJECT_ROOT_NAMESPACE_B1, self::BUILD_DIR_TMP_B1);
        $fields = $this->buildCommonTypeFields();
        $this->buildEntitiesAndAssignCommonFields($fields);
        $this->updateLargeDataEntity();
        $this->updateLargePropertiesEntity();
        $this->updateAllArchetypeFieldsEntity();
        $this->updateEmailEntity();
        $this->updateAllEmbeddablesEntity();
        $this->setRelations();
        $this->resetAutoloader();
    }

    private function emptyDir(string $path): void
    {
        $this->filesystem->remove($path);
        $this->filesystem->mkdir($path);
    }

    private function extendAutoloader(string $namespace, string $buildDir): void
    {
        $testLoader = new class($namespace) extends ClassLoader
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
        $testLoader->addPsr4($namespace . '\\', $buildDir . '/src', true);
        $testLoader->addPsr4($namespace . '\\', $buildDir . '/tests', true);
        $testLoader->register();
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
                self::TEST_PROJECT_ROOT_NAMESPACE_B1 . self::TEST_FIELD_FQN_BASE . '\\' . ucwords($type),
                $type
            );
        }

        return $fields;
    }

    private function buildEntitiesAndAssignCommonFields(array $fields): void
    {

        $entityGenerator = $this->builder->getEntityGenerator();
        $fieldSetter     = $this->builder->getFieldSetter();

        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityGenerator->generateEntity(self::TEST_PROJECT_ROOT_NAMESPACE_B1 . $entityFqn);
            foreach ($fields as $fieldFqn) {
                $fieldSetter->setEntityHasField(
                    self::TEST_PROJECT_ROOT_NAMESPACE_B1 . $entityFqn,
                    $fieldFqn
                );
            }
        }
    }

    private function updateLargeDataEntity(): void
    {
        $fieldGenerator = $this->builder->getFieldGenerator();
        $fieldSetter    = $this->builder->getFieldSetter();
        foreach (self::LARGE_DATA_FIELDS as $field) {
            $fieldSetter->setEntityHasField(
                self::TEST_ENTITY_NAMESPACE_BASE_B1 . self::TEST_ENTITY_LARGE_DATA,
                $fieldGenerator->generateField(
                    self::TEST_PROJECT_ROOT_NAMESPACE_B1 . $field,
                    MappingHelper::TYPE_TEXT
                )
            );
        }
    }

    private function updateLargePropertiesEntity(): void
    {
        $fieldGenerator = $this->builder->getFieldGenerator();
        $fieldSetter    = $this->builder->getFieldSetter();
        foreach (self::LARGE_PROPERTIES_FIELDS as $field) {
            $fieldSetter->setEntityHasField(
                self::TEST_ENTITY_NAMESPACE_BASE_B1 . self::TEST_ENTITY_LARGE_DATA,
                $fieldGenerator->generateField(
                    self::TEST_PROJECT_ROOT_NAMESPACE_B1 . $field,
                    MappingHelper::TYPE_BOOLEAN
                )
            );
        }
    }

    private function updateAllArchetypeFieldsEntity(): void
    {
        $fieldSetter = $this->builder->getFieldSetter();
        foreach (FieldGenerator::STANDARD_FIELDS as $archetypeFieldFqn) {
            $fieldSetter->setEntityHasField(
                self::TEST_ENTITY_NAMESPACE_BASE_B1 . self::TEST_ENTITY_ALL_ARCHETYPE_FIELDS,
                $archetypeFieldFqn
            );
        }
    }

    private function updateEmailEntity(): void
    {
        $emailEntityFqn = self::TEST_ENTITY_NAMESPACE_BASE_B1 . self::TEST_ENTITY_EMAIL;
        $this->builder->getFieldSetter()->setEntityHasField($emailEntityFqn, EmailAddressFieldTrait::class);
    }

    private function updateAllEmbeddablesEntity(): void
    {
        $this->builder->setEmbeddablesToEntity(
            self::TEST_ENTITY_NAMESPACE_BASE_B1 . self::TEST_ENTITY_ALL_EMBEDDABLES,
            [
                HasMoneyEmbeddableTrait::class,
                HasAddressEmbeddableTrait::class,
                HasFullNameEmbeddableTrait::class,
                HasWeightEmbeddableTrait::class,
            ]
        );
    }

    private function setRelations(): void
    {
        $relationsGenerator = $this->builder->getRelationsGenerator();
        foreach (self::TEST_RELATIONS as $relation) {
            $relationsGenerator->setEntityHasRelationToEntity(
                self::TEST_PROJECT_ROOT_NAMESPACE_B1 . $relation[0],
                (string)$relation[1],
                self::TEST_PROJECT_ROOT_NAMESPACE_B1 . $relation[2],
                (bool)$relation[3]
            );
        }
    }

    private function resetAutoloader(): void
    {
        $registered = \spl_autoload_functions();
        $loader     = array_pop($registered);
        \spl_autoload_unregister($loader);
    }

    private function secondBuild(): void
    {
        $this->emptyDir(self::BUILD_DIR_TMP_B2);
        $this->codeCopier->copy(
            self::BUILD_DIR_TMP_B1,
            self::BUILD_DIR_TMP_B2,
            self::TEST_PROJECT_ROOT_NAMESPACE_B1,
            self::TEST_PROJECT_ROOT_NAMESPACE_B2
        );
        $this->extendAutoloader(self::TEST_PROJECT_ROOT_NAMESPACE_B2, self::BUILD_DIR_TMP_B2);
        $this->builder->setPathToProjectRoot(self::BUILD_DIR_TMP_B2)
                      ->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE_B2)
                      ->finaliseBuild();
        $this->emptyDir(self::BUILD_DIR);
        $this->codeCopier->copy(
            self::BUILD_DIR_TMP_B2,
            self::BUILD_DIR,
            self::TEST_PROJECT_ROOT_NAMESPACE_B2,
            self::TEST_PROJECT_ROOT_NAMESPACE
        );
    }

    private function setBuildHash(): void
    {
        \ts\file_put_contents(self::BUILD_HASH_FILE, md5(\ts\file_get_contents(__FILE__)));
    }

    public function copyTo(
        string $destinationPath,
        string $replaceNamespace = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE
    ): void {
        $this->codeCopier->copy(
            self::BUILD_DIR,
            $destinationPath,
            self::TEST_PROJECT_ROOT_NAMESPACE,
            $replaceNamespace
        );
    }
}
