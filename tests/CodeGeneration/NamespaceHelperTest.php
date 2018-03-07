<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;

/**
 * Class NamespaceHelperTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class NamespaceHelperTest extends AbstractTest
{

    public const WORK_DIR = AbstractTest::VAR_PATH.'/NamespaceHelperTest';

    public const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME;

    public const TEST_ENTITIES = [
        self::TEST_ENTITY_FQN_BASE.'\\Blah\\Foo',
        self::TEST_ENTITY_FQN_BASE.'\\Bar\\Baz',
        self::TEST_ENTITY_FQN_BASE.'\\No\\Relatives',
    ];

    public const TEST_ENTITY_WITH_ENTITIES_IN_PROJECT_NAME = '\\My\\EntitiesProject\\Entities\\Blah\\Foo';

    public const TEST_ENTITY_POST_CREATED        = self::TEST_ENTITY_FQN_BASE.'\\Meh';
    public const TEST_ENTITY_POST_CREATED_NESTED = self::TEST_ENTITY_FQN_BASE.'\\Nested\\Something\\Ho\\Hum';

    /**
     * @var NamespaceHelper
     */
    private $helper;

    /**
     *
     */
    public function setup()
    {
        parent::setup();
        $this->helper       = $this->container->get(NamespaceHelper::class);
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
        /**
         * Something is causing PHP files to be loaded by PHP as part of the creation.
         * Have not been able ot track this down.
         * Creating a new file is a workaround for this
         */
        file_put_contents(
            self::WORK_DIR.'/src/Entities/Meh.php',
            <<<PHP
<?php
declare(strict_types=1);

namespace DSM\Test\Project\Entities;

use DSM\Test\Project\Entity\Relations\Blah\Foo\Interfaces\HasFoosInterface;
use DSM\Test\Project\Entity\Relations\Blah\Foo\Interfaces\ReciprocatesFooInterface;
use DSM\Test\Project\Entity\Relations\Blah\Foo\Traits\HasFoos\HasFoosInverseManyToMany;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

class Meh implements DSM\Interfaces\UsesPHPMetaDataInterface, HasFoosInterface, ReciprocatesFooInterface {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Fields\Traits\IdFieldTrait;
	use HasFoosInverseManyToMany;
}

PHP
        );
        $this->getFileSystem()->mkdir(self::WORK_DIR.'/src/Entities/Nested/Something/Ho');
        /**
         * Something is causing PHP files to be loaded by PHP as part of the creation.
         * Have not been able ot track this down.
         * Creating a new file is a workaround for this
         */
        file_put_contents(
            self::WORK_DIR.'/src/Entities/Nested/Something/Ho/Hum.php',
            <<<PHP
<?php
declare(strict_types=1);

namespace DSM\Test\Project\Entities\Nested\Something\Ho;

use DSM\Test\Project\Entity\Relations\Blah\Foo\Interfaces\HasFoosInterface;
use DSM\Test\Project\Entity\Relations\Blah\Foo\Interfaces\ReciprocatesFooInterface;
use DSM\Test\Project\Entity\Relations\Blah\Foo\Traits\HasFoos\HasFoosInverseManyToMany;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

class Hum implements DSM\Interfaces\UsesPHPMetaDataInterface, HasFoosInterface, ReciprocatesFooInterface {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Fields\Traits\IdFieldTrait;
	use HasFoosInverseManyToMany;
}

PHP
        );
    }

    public function testTidy()
    {
        $namespaceToExpected = [
            'Test\\\\Multiple\\\\\\\Separators' => 'Test\\Multiple\\Separators',
            'No\\Changes\\Required'             => 'No\\Changes\\Required',
        ];
        foreach ($namespaceToExpected as $namespace => $expected) {
            $this->assertEquals($expected, $this->helper->tidy($namespace));
        }
    }

    public function testRoot()
    {
        $namespaceToExpected = [
            '\\Test\\\\Multiple\\\\\\\Separators' => 'Test\\Multiple\\Separators',
            'No\\Changes\\Required'               => 'No\\Changes\\Required',
        ];
        foreach ($namespaceToExpected as $namespace => $expected) {
            $this->assertEquals($expected, $this->helper->root($namespace));
        }
    }

    /**
     */
    public function testCalculateProjectNamespaceRootFromEntitFqn()
    {
        $entity1Fqn = self::TEST_ENTITIES[0];

        $expected = self::TEST_PROJECT_ROOT_NAMESPACE;
        $actual   = $this->helper->getProjectNamespaceRootFromEntityFqn($entity1Fqn);
        $this->assertEquals($expected, $actual);

        $entityFqnWithEntitiesInProjectName = self::TEST_ENTITY_WITH_ENTITIES_IN_PROJECT_NAME;
        $expected                           = '\\My\\EntitiesProject';
        $actual                             = $this->helper->getProjectNamespaceRootFromEntityFqn(
            $entityFqnWithEntitiesInProjectName
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function testParseFullyQualifiedName()
    {
        $entity1Fqn           = self::TEST_ENTITIES[0];
        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE;
        $expected             = [
            'Foo',
            $projectRootNamespace.'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Blah',
            [
                'src',
                'Entities',
                'Blah',
            ],
        ];
        $actual               = $this->helper->parseFullyQualifiedName(
            $entity1Fqn,
            $srcOrTestSubFolder,
            $projectRootNamespace
        );
        $this->assertEquals($expected, $actual);

        $entity1Fqn           = '\\'.self::TEST_ENTITIES[0];
        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = '\\'.self::TEST_PROJECT_ROOT_NAMESPACE;
        $expected             = [
            'Foo',
            ltrim($projectRootNamespace.'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Blah', '\\'),
            [
                'src',
                'Entities',
                'Blah',
            ],
        ];
        $actual               = $this->helper->parseFullyQualifiedName(
            self::TEST_ENTITIES[0],
            $srcOrTestSubFolder,
            $projectRootNamespace
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testCalculcateOwnedHasName()
    {
        $hasType              = RelationsGenerator::HAS_MANY_TO_MANY;
        $ownedEntityFqn       = self::TEST_ENTITIES[0];
        $expected             = 'BlahFoos';
        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = '\\'.self::TEST_PROJECT_ROOT_NAMESPACE;

        $actual = $this->helper->getOwnedHasName(
            $hasType,
            $ownedEntityFqn,
            $srcOrTestSubFolder,
            $projectRootNamespace
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testGetEntitySubNamespace()
    {
        $entityFqn = self::TEST_ENTITIES[0];
        $expected  = 'Blah\\Foo';
        $actual    = $this->helper->getEntitySubNamespace($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = '\\DSM\\Test\\Project\\Entities\\No\\Relatives';
        $expected  = 'No\\Relatives';
        $actual    = $this->helper->getEntitySubNamespace($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = '\\DSM\\Test\\Project\\Entities\\Person';
        $expected  = 'Person';
        $actual    = $this->helper->getEntitySubNamespace($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = '\\DSM\\Test\\EntitiesProject\\Entities\\Person';
        $expected  = 'Person';
        $actual    = $this->helper->getEntitySubNamespace($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testGetEntitySubFilePath()
    {
        $entityFqn = '\\DSM\\Test\\Project\\Entities\\Person';
        $expected  = '/Person.php';
        $actual    = $this->helper->getEntityFileSubPath($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = self::TEST_ENTITY_WITH_ENTITIES_IN_PROJECT_NAME;
        $expected  = '/Blah/Foo.php';
        $actual    = $this->helper->getEntityFileSubPath($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testGetEntitySubPath()
    {
        $entityFqn = self::TEST_ENTITIES[0];
        $expected  = '/Blah/Foo';
        $actual    = $this->helper->getEntitySubPath($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = self::TEST_ENTITY_WITH_ENTITIES_IN_PROJECT_NAME;
        $expected  = '/Blah/Foo';
        $actual    = $this->helper->getEntitySubPath($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testGetInterfacesNamespaceForEntity()
    {
        $entityFqn                    = self::TEST_ENTITIES[0];
        $entityRelationsRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE
                                        .AbstractGenerator::ENTITY_RELATIONS_NAMESPACE;
        $expected                     = $entityRelationsRootNamespace.'\\Blah\\Foo\\Interfaces';
        $actual                       = $this->helper->getInterfacesNamespaceForEntity($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testGetTraitsNamespaceForEntity()
    {
        $entityFqn                    = self::TEST_ENTITIES[0];
        $entityRelationsRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE
                                        .AbstractGenerator::ENTITY_RELATIONS_NAMESPACE;
        $expected                     = $entityRelationsRootNamespace.'\\Blah\\Foo\\Traits';
        $actual                       = $this->helper->getTraitsNamespaceForEntity($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetEntityNamespaceRootFromEntityReflection()
    {

        $entityReflection = new \ReflectionClass(self::TEST_ENTITY_POST_CREATED);
        $expected         = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME;
        $actual           = $this->helper->getEntityNamespaceRootFromEntityReflection($entityReflection);
        $this->assertEquals($expected, $actual);

        $entityFqn = '\\DSM\\Test\\Project\\Entities\\No\\Relatives';
        $actual    = $this->helper->getEntityNamespaceRootFromEntityReflection(
            new \ReflectionClass($entityFqn)
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testGetHasPluralInterfaceFqnForEntity()
    {
        $entityFqn = self::TEST_ENTITY_POST_CREATED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     .AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     .'\\Meh\\Interfaces\\HasMehsInterface';
        $actual    = $this->helper->getHasPluralInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = self::TEST_ENTITY_POST_CREATED_NESTED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     .AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     .'\\Nested\\Something\\Ho\\Hum\\Interfaces\\HasHumsInterface';
        $actual    = $this->helper->getHasPluralInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testgetHasSingularInterfaceFqnForEntity()
    {
        $entityFqn = self::TEST_ENTITY_POST_CREATED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     .AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     .'\\Meh\\Interfaces\\HasMehInterface';
        $actual    = $this->helper->getHasSingularInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = self::TEST_ENTITY_POST_CREATED_NESTED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE
                     .AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                     .'\\Nested\\Something\\Ho\\Hum\\Interfaces\\HasHumInterface';
        $actual    = $this->helper->getHasSingularInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function testGetProjectRootNamespaceFromComposerJson()
    {
        $expected = 'EdmondsCommerce\\DoctrineStaticMeta';
        $actual   = $this->helper->getProjectRootNamespaceFromComposerJson();
        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testStripPrefixFromHasType()
    {
        $expected = [
            'OwningOneToOne'          => 'OwningOneToOne',
            'InverseOneToOne'         => 'InverseOneToOne',
            'UnidirectionalOneToOne'  => 'UnidirectionalOneToOne',
            'OneToMany'               => 'OneToMany',
            'UnidirectionalOneToMany' => 'UnidirectionalOneToMany',
            'ManyToOne'               => 'ManyToOne',
            'UnidirectionalManyToOne' => 'UnidirectionalManyToOne',
            'OwningManyToMany'        => 'OwningManyToMany',
            'InverseManyToMany'       => 'InverseManyToMany',
        ];
        $actual   = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            $actual[$hasType] = $this->helper->stripPrefixFromHasType($hasType);
        }
        $this->assertEquals($expected, $actual);

        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = '\\'.self::TEST_PROJECT_ROOT_NAMESPACE;

        foreach ($actual as $hasType => $stripped) {
            $ownedHasName    = $this->helper->getOwnedHasName(
                $hasType,
                "\\TemplateNamespace\\Entities\\TemplateEntity",
                $srcOrTestSubFolder,
                $projectRootNamespace
            );
            $filePath        = realpath(AbstractGenerator::TEMPLATE_PATH)
                               .'/src/Entity/Relations/TemplateEntity/Traits/Has'
                               .$ownedHasName.'/Has'.$ownedHasName.$stripped.'.php';
            $longestExisting = '';
            foreach (explode('/', $filePath) as $part) {
                $maybeLongestExisting = $longestExisting.'/'.$part;
                if (is_file($maybeLongestExisting) || is_dir($maybeLongestExisting)) {
                    $longestExisting = $maybeLongestExisting;
                    continue;
                }
                break;
            }
            $longestExisting = substr($longestExisting, 1);
            $this->assertFileExists($filePath, "\n$filePath\nexists up to:\n$longestExisting\n");
        }
    }

    /**
     */
    public function testGetOwningTraitFqn()
    {
        $traitBase = '\\TemplateNamespace\\Entity\Relations\\TemplateEntity\\Traits';
        $expected  = [
            'OwningOneToOne'          => $traitBase.'\\HasTemplateEntity\\HasTemplateEntityOwningOneToOne',
            'InverseOneToOne'         => $traitBase.'\\HasTemplateEntity\\HasTemplateEntityInverseOneToOne',
            'UnidirectionalOneToOne'  => $traitBase.'\\HasTemplateEntity\\HasTemplateEntityUnidirectionalOneToOne',
            'OneToMany'               => $traitBase.'\\HasTemplateEntities\\HasTemplateEntitiesOneToMany',
            'UnidirectionalOneToMany' => $traitBase.'\\HasTemplateEntities\\HasTemplateEntitiesUnidirectionalOneToMany',
            'ManyToOne'               => $traitBase.'\\HasTemplateEntity\\HasTemplateEntityManyToOne',
            'UnidirectionalManyToOne' => $traitBase.'\\HasTemplateEntity\\HasTemplateEntityUnidirectionalManyToOne',
            'OwningManyToMany'        => $traitBase.'\\HasTemplateEntities\\HasTemplateEntitiesOwningManyToMany',
            'InverseManyToMany'       => $traitBase.'\\HasTemplateEntities\\HasTemplateEntitiesInverseManyToMany',
        ];
        $actual    = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            $actual[$hasType] = $this->helper->getOwningTraitFqn(
                $hasType,
                "\\TemplateNamespace\\Entities\\TemplateEntity",
                "\\TemplateNamespace"
            );
        }
        $this->assertEquals(
            $expected,
            $actual,
            "\nExpected:\n".var_export($actual, true)
            ."\nActual:\n".var_export($actual, true)."\n"
        );
    }

    /**
     */
    public function testGetOwningInterfaceFqn()
    {
        $intBase  = '\\TemplateNamespace\\Entity\Relations\\TemplateEntity\\Interfaces';
        $expected = [
            'OwningOneToOne'          => $intBase.'\\HasTemplateEntityInterface',
            'InverseOneToOne'         => $intBase.'\\HasTemplateEntityInterface',
            'UnidirectionalOneToOne'  => $intBase.'\\HasTemplateEntityInterface',
            'OneToMany'               => $intBase.'\\HasTemplateEntitiesInterface',
            'UnidirectionalOneToMany' => $intBase.'\\HasTemplateEntitiesInterface',
            'ManyToOne'               => $intBase.'\\HasTemplateEntityInterface',
            'UnidirectionalManyToOne' => $intBase.'\\HasTemplateEntityInterface',
            'OwningManyToMany'        => $intBase.'\\HasTemplateEntitiesInterface',
            'InverseManyToMany'       => $intBase.'\\HasTemplateEntitiesInterface',
        ];
        $actual   = [];
        foreach (RelationsGenerator::HAS_TYPES as $hasType) {
            $actual[$hasType] = $this->helper->getOwningInterfaceFqn(
                $hasType,
                "\\TemplateNamespace\\Entities\\TemplateEntity",
                "\\TemplateNamespace"
            );
        }
        $this->assertEquals(
            $expected,
            $actual,
            "\nExpected:\n".var_export($actual, true)
            ."\nActual:\n".var_export($actual, true)."\n"
        );
    }
}
