<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\F\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

use function get_class;

/**
 * Class NamespaceHelperTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Small\CodeGeneration
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper
 * @large
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class NamespaceHelperTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/NamespaceHelperTest';

    public const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE .
                                        '\\' .
                                        AbstractGenerator::ENTITIES_FOLDER_NAME;

    public const TEST_ENTITIES = [
        self::TEST_ENTITY_FQN_BASE . '\\Blah\\Foo',
        self::TEST_ENTITY_FQN_BASE . '\\Bar\\Baz',
        self::TEST_ENTITY_FQN_BASE . '\\No\\Relative',
        self::TEST_ENTITY_FQN_BASE . '\\Meh',
        self::TEST_ENTITY_FQN_BASE . '\\Nested\\Something\\Ho\\Hum',
    ];

    public const TEST_ENTITY_POST_CREATED        = self::TEST_ENTITY_FQN_BASE . '\\Meh';
    public const TEST_ENTITY_POST_CREATED_NESTED = self::TEST_ENTITY_FQN_BASE . '\\Nested\\Something\\Ho\\Hum';
    protected static $buildOnce = true;
    /**
     * @var NamespaceHelper
     */
    private static $helper;

    public function setup()
    {
        parent::setUp();
        if (true === self::$built) {
            return;
        }
        $entityGenerator    = $this->getEntityGenerator();
        $relationsGenerator = $this->getRelationsGenerator();
        foreach (self::TEST_ENTITIES as $fqn) {
            $entityGenerator->generateEntity($fqn);
            $relationsGenerator->generateRelationCodeForEntity($fqn);
        }
        $relationsGenerator->setEntityHasRelationToEntity(
            self::TEST_ENTITIES[0],
            RelationsGenerator::HAS_MANY_TO_MANY,
            self::TEST_ENTITIES[1]
        );
        self::$built = true;
    }

    public static function setupBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$helper = new NamespaceHelper();
    }

    /**
     * @test
     * @large
     *      */
    public function getObjectShortName(): void
    {

        $expectedToObjects = [
            'NamespaceHelperTest' => $this,
            'NamespaceHelper'     => self::$helper,
        ];
        $actual            = [];
        foreach ($expectedToObjects as $object) {
            $actual[self::$helper->getObjectShortName($object)] = $object;
        }
        self::assertSame($expectedToObjects, $actual);
    }

    /**
     * @test
     * @large
     *      */
    public function getObjectFqn(): void
    {

        $expectedToObjects = [
            get_class($this)         => $this,
            get_class(self::$helper) => self::$helper,
        ];
        $actual            = [];
        foreach ($expectedToObjects as $object) {
            $actual[self::$helper->getObjectFqn($object)] = $object;
        }
        self::assertSame($expectedToObjects, $actual);
    }

    /**
     * @test
     * @large
     *      */
    public function getClassShortName(): void
    {
        $expectedToFqns = [
            'NamespaceHelperTest' => get_class($this),
            'Cheese'              => '\\Super\\Cheese',
        ];
        $actual         = [];
        foreach ($expectedToFqns as $fqn) {
            $actual[self::$helper->getClassShortName($fqn)] = $fqn;
        }
        self::assertSame($expectedToFqns, $actual);
    }


    /**
     * @test
     * @large
     */
    public function testCalculateProjectNamespaceRootFromEntitFqn(): void
    {
        $entity1Fqn = self::TEST_ENTITIES[0];

        $expected = self::TEST_PROJECT_ROOT_NAMESPACE;
        $actual   = self::$helper->getProjectNamespaceRootFromEntityFqn($entity1Fqn);
        self::assertSame($expected, $actual);

        $entityFqnWithEntitiesInProjectName = self::TEST_ENTITIES[0];
        $expected                           = self::TEST_PROJECT_ROOT_NAMESPACE;
        $actual                             = self::$helper->getProjectNamespaceRootFromEntityFqn(
            $entityFqnWithEntitiesInProjectName
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    public function testParseFullyQualifiedName(): void
    {
        $entity1Fqn           = self::TEST_ENTITIES[0];
        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE;
        $expected             = [
            'Foo',
            $projectRootNamespace . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Blah',
            [
                'src',
                'Entities',
                'Blah',
            ],
        ];
        $actual               = self::$helper->parseFullyQualifiedName(
            $entity1Fqn,
            $srcOrTestSubFolder,
            $projectRootNamespace
        );
        self::assertSame($expected, $actual);

        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = '\\' . self::TEST_PROJECT_ROOT_NAMESPACE;
        $expected             = [
            'Foo',
            ltrim($projectRootNamespace . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Blah', '\\'),
            [
                'src',
                'Entities',
                'Blah',
            ],
        ];
        $actual               = self::$helper->parseFullyQualifiedName(
            self::TEST_ENTITIES[0],
            $srcOrTestSubFolder,
            $projectRootNamespace
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testCalculcateOwnedHasName(): void
    {
        $hasType              = RelationsGenerator::HAS_MANY_TO_MANY;
        $ownedEntityFqn       = self::TEST_ENTITIES[0];
        $expected             = 'BlahFoos';
        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = '\\' . self::TEST_PROJECT_ROOT_NAMESPACE;

        $actual = self::$helper->getOwnedHasName(
            $hasType,
            $ownedEntityFqn,
            $srcOrTestSubFolder,
            $projectRootNamespace
        );

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testGetEntitySubNamespace(): void
    {
        $entityFqn = self::TEST_ENTITIES[0];
        $expected  = 'Blah\\Foo';
        $actual    = self::$helper->getEntitySubNamespace($entityFqn);
        self::assertSame($expected, $actual);

        $entityFqn = '\\My\\Test\\Project\\Entities\\No\\Relatives';
        $expected  = 'No\\Relatives';
        $actual    = self::$helper->getEntitySubNamespace($entityFqn);
        self::assertSame($expected, $actual);

        $entityFqn = '\\My\\Test\\Project\\Entities\\Person';
        $expected  = 'Person';
        $actual    = self::$helper->getEntitySubNamespace($entityFqn);
        self::assertSame($expected, $actual);

        $entityFqn = '\\My\\Test\\EntitiesProject\\Entities\\Person';
        $expected  = 'Person';
        $actual    = self::$helper->getEntitySubNamespace($entityFqn);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testGetEntitySubFilePath(): void
    {
        $entityFqn = '\\My\\Test\\Project\\Entities\\Person';
        $expected  = '/Person.php';
        $actual    = self::$helper->getEntityFileSubPath($entityFqn);
        self::assertSame($expected, $actual);

        $entityFqn = '\\My\\Test\\EntitiesProject\\Entities\\Person';
        $expected  = '/Person.php';
        $actual    = self::$helper->getEntityFileSubPath($entityFqn);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testGetEntitySubPath(): void
    {
        $entityFqn = self::TEST_ENTITIES[0];
        $expected  = '/Blah/Foo';
        $actual    = self::$helper->getEntitySubPath($entityFqn);
        self::assertSame($expected, $actual);

        $entityFqn = '\\My\\Test\\EntitiesProject\\Entities\\Person';
        $expected  = '/Person';
        $actual    = self::$helper->getEntitySubPath($entityFqn);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testGetInterfacesNamespaceForEntity(): void
    {
        $entityFqn                    = self::TEST_ENTITIES[0];
        $entityRelationsRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE
                                        . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE;
        $expected                     = $entityRelationsRootNamespace . '\\Blah\\Foo\\Interfaces';
        $actual                       = self::$helper->getInterfacesNamespaceForEntity($entityFqn);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testGetTraitsNamespaceForEntity(): void
    {
        $entityFqn                    = self::TEST_ENTITIES[0];
        $entityRelationsRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE
                                        . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE;
        $expected                     = $entityRelationsRootNamespace . '\\Blah\\Foo\\Traits';
        $actual                       = self::$helper->getTraitsNamespaceForEntity($entityFqn);
        self::assertSame($expected, $actual);
    }


    /**
     * @test
     * @large
     */
    public function testGetHasPluralInterfaceFqnForEntity(): void
    {
        $entityFqn = self::TEST_ENTITY_POST_CREATED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     . '\\Meh\\Interfaces\\HasMehsInterface';
        $actual    = self::$helper->getHasPluralInterfaceFqnForEntity($entityFqn);
        self::assertSame($expected, $actual);

        $entityFqn = self::TEST_ENTITY_POST_CREATED_NESTED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     . '\\Nested\\Something\\Ho\\Hum\\Interfaces\\HasNestedSomethingHoHumsInterface';
        $actual    = self::$helper->getHasPluralInterfaceFqnForEntity($entityFqn);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testgetHasSingularInterfaceFqnForEntity(): void
    {
        $entityFqn = self::TEST_ENTITY_POST_CREATED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     . '\\Meh\\Interfaces\\HasMehInterface';
        $actual    = self::$helper->getHasSingularInterfaceFqnForEntity($entityFqn);
        self::assertSame($expected, $actual);

        $entityFqn = self::TEST_ENTITY_POST_CREATED_NESTED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     . '\\Nested\\Something\\Ho\\Hum\\Interfaces\\HasNestedSomethingHoHumInterface';
        $actual    = self::$helper->getHasSingularInterfaceFqnForEntity($entityFqn);
        self::assertSame($expected, $actual);
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    public function testGetProjectRootNamespaceFromComposerJson(): void
    {
        $expected = 'EdmondsCommerce\\DoctrineStaticMeta';
        $actual   = self::$helper->getProjectRootNamespaceFromComposerJson();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function testGetOwningTraitFqn(): void
    {
        $traitBase = '\\TemplateNamespace\\Entity\Relations\\TemplateEntity\\Traits';
        $expected  = [
            'OwningOneToOne'                  => $traitBase . '\\HasTemplateEntity\\HasTemplateEntityOwningOneToOne',
            'InverseOneToOne'                 => $traitBase . '\\HasTemplateEntity\\HasTemplateEntityInverseOneToOne',
            'UnidirectionalOneToOne'          => $traitBase .
                                                 '\\HasTemplateEntity\\HasTemplateEntityUnidirectionalOneToOne',
            'OneToMany'                       => $traitBase . '\\HasTemplateEntities\\HasTemplateEntitiesOneToMany',
            'UnidirectionalOneToMany'         => $traitBase .
                                                 '\\HasTemplateEntities\\HasTemplateEntitiesUnidirectionalOneToMany',
            'ManyToOne'                       => $traitBase . '\\HasTemplateEntity\\HasTemplateEntityManyToOne',
            'UnidirectionalManyToOne'         => $traitBase .
                                                 '\\HasTemplateEntity\\HasTemplateEntityUnidirectionalManyToOne',
            'OwningManyToMany'                => $traitBase .
                                                 '\\HasTemplateEntities\\HasTemplateEntitiesOwningManyToMany',
            'InverseManyToMany'               => $traitBase .
                                                 '\\HasTemplateEntities\\HasTemplateEntitiesInverseManyToMany',
            'RequiredOwningOneToOne'          => $traitBase .
                                                 '\\HasRequiredTemplateEntity\\HasRequiredTemplateEntityOwningOneToOne',
            'RequiredInverseOneToOne'         => $traitBase .
                                                 '\\HasRequiredTemplateEntity' .
                                                 '\\HasRequiredTemplateEntityInverseOneToOne',
            'RequiredUnidirectionalOneToOne'  => $traitBase .
                                                 '\\HasRequiredTemplateEntity' .
                                                 '\\HasRequiredTemplateEntityUnidirectionalOneToOne',
            'RequiredOneToMany'               => $traitBase .
                                                 '\\HasRequiredTemplateEntities' .
                                                 '\\HasRequiredTemplateEntitiesOneToMany',
            'RequiredUnidirectionalOneToMany' => $traitBase .
                                                 '\\HasRequiredTemplateEntities' .
                                                 '\\HasRequiredTemplateEntitiesUnidirectionalOneToMany',
            'RequiredManyToOne'               => $traitBase .
                                                 '\\HasRequiredTemplateEntity\\HasRequiredTemplateEntityManyToOne',
            'RequiredUnidirectionalManyToOne' => $traitBase .
                                                 '\\HasRequiredTemplateEntity' .
                                                 '\\HasRequiredTemplateEntityUnidirectionalManyToOne',
            'RequiredOwningManyToMany'        => $traitBase .
                                                 '\\HasRequiredTemplateEntities' .
                                                 '\\HasRequiredTemplateEntitiesOwningManyToMany',
            'RequiredInverseManyToMany'       => $traitBase .
                                                 '\\HasRequiredTemplateEntities' .
                                                 '\\HasRequiredTemplateEntitiesInverseManyToMany',
        ];
        $actual    = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            $actual[$hasType] = self::$helper->getOwningTraitFqn(
                $hasType,
                "\\TemplateNamespace\\Entities\\TemplateEntity",
                "\\TemplateNamespace"
            );
        }
        self::assertSame(
            $expected,
            $actual,
            "\nExpected:\n" . var_export($expected, true)
            . "\nActual:\n" . var_export($actual, true) . "\n"
        );
    }

    /**
     * @test
     * @large
     */
    public function testGetOwningInterfaceFqn(): void
    {
        $intBase  = '\\TemplateNamespace\\Entity\Relations\\TemplateEntity\\Interfaces';
        $expected = [
            'OwningOneToOne'                  => $intBase . '\\HasTemplateEntityInterface',
            'InverseOneToOne'                 => $intBase . '\\HasTemplateEntityInterface',
            'UnidirectionalOneToOne'          => $intBase . '\\HasTemplateEntityInterface',
            'OneToMany'                       => $intBase . '\\HasTemplateEntitiesInterface',
            'UnidirectionalOneToMany'         => $intBase . '\\HasTemplateEntitiesInterface',
            'ManyToOne'                       => $intBase . '\\HasTemplateEntityInterface',
            'UnidirectionalManyToOne'         => $intBase . '\\HasTemplateEntityInterface',
            'OwningManyToMany'                => $intBase . '\\HasTemplateEntitiesInterface',
            'InverseManyToMany'               => $intBase . '\\HasTemplateEntitiesInterface',
            'RequiredOwningOneToOne'          => $intBase . '\\HasRequiredTemplateEntityInterface',
            'RequiredInverseOneToOne'         => $intBase . '\\HasRequiredTemplateEntityInterface',
            'RequiredUnidirectionalOneToOne'  => $intBase . '\\HasRequiredTemplateEntityInterface',
            'RequiredOneToMany'               => $intBase . '\\HasRequiredTemplateEntitiesInterface',
            'RequiredUnidirectionalOneToMany' => $intBase . '\\HasRequiredTemplateEntitiesInterface',
            'RequiredManyToOne'               => $intBase . '\\HasRequiredTemplateEntityInterface',
            'RequiredUnidirectionalManyToOne' => $intBase . '\\HasRequiredTemplateEntityInterface',
            'RequiredOwningManyToMany'        => $intBase . '\\HasRequiredTemplateEntitiesInterface',
            'RequiredInverseManyToMany'       => $intBase . '\\HasRequiredTemplateEntitiesInterface',
        ];
        $actual   = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            $actual[$hasType] = self::$helper->getOwningInterfaceFqn(
                $hasType,
                "\\TemplateNamespace\\Entities\\TemplateEntity",
                "\\TemplateNamespace"
            );
        }
        self::assertSame(
            $expected,
            $actual,
            "\nExpected:\n" . var_export($expected, true)
            . "\nActual:\n" . var_export($actual, true) . "\n"
        );
    }
}
