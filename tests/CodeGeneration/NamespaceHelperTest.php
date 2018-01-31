<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;

class NamespaceHelperTest extends AbstractTest
{

    const WORK_DIR = VAR_PATH . '/NamespaceHelperTest';

    const TEST_ENTITIES = [
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Blah\\Foo',
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Bar\\Baz'
    ];

    const TEST_ENTITY_POST_CREATED        = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Meh';
    const TEST_ENTITY_POST_CREATED_NESTED = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Nested\\Something\\Ho\\Hum';

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
        $this->helper       = new NamespaceHelper();
        $entityGenerator    = new EntityGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_FOLDER
        );
        $relationsGenerator = new RelationsGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_FOLDER
        );
        foreach (self::TEST_ENTITIES as $fqn) {
            $entityGenerator->generateEntity($fqn);
            $relationsGenerator->generateRelationCodeForEntity($fqn);
        }
        $relationsGenerator->setEntityHasRelationToEntity(self::TEST_ENTITIES[0], RelationsGenerator::HAS_MANY_TO_MANY, self::TEST_ENTITIES[1]);
        /**
         * Something is causing PHP files to be loaded by PHP as part of the creation.
         * Have not been able ot track this down.
         * Creating a new file is a workaround for this
         */
        file_put_contents(
            self::WORK_DIR . '/src/Entities/Meh.php', <<<PHP
<?php
declare(strict_types=1);

namespace DSM\Test\Project\Entities;

use DSM\Test\Project\Entities\Relations\Blah\Foo\Interfaces\HasFoos;
use DSM\Test\Project\Entities\Relations\Blah\Foo\Interfaces\ReciprocatesFoo;
use DSM\Test\Project\Entities\Relations\Blah\Foo\Traits\HasFoos\HasFoosInverseManyToMany;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

class Meh implements DSM\Interfaces\UsesPHPMetaDataInterface, HasFoos, ReciprocatesFoo {

	use DSM\Traits\UsesPHPMetaData;
	use DSM\Traits\Fields\IdField;
	use HasFoosInverseManyToMany;
}

PHP
        );
        $this->getFileSystem()->mkdir(self::WORK_DIR . '/src/Entities/Nested/Something/Ho');
        /**
         * Something is causing PHP files to be loaded by PHP as part of the creation.
         * Have not been able ot track this down.
         * Creating a new file is a workaround for this
         */
        file_put_contents(
            self::WORK_DIR . '/src/Entities/Nested/Something/Ho/Hum.php', <<<PHP
<?php
declare(strict_types=1);

namespace DSM\Test\Project\Entities\Nested\Something\Ho;

use DSM\Test\Project\Entities\Relations\Blah\Foo\Interfaces\HasFoos;
use DSM\Test\Project\Entities\Relations\Blah\Foo\Interfaces\ReciprocatesFoo;
use DSM\Test\Project\Entities\Relations\Blah\Foo\Traits\HasFoos\HasFoosInverseManyToMany;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

class Hum implements DSM\Interfaces\UsesPHPMetaDataInterface, HasFoos, ReciprocatesFoo {

	use DSM\Traits\UsesPHPMetaData;
	use DSM\Traits\Fields\IdField;
	use HasFoosInverseManyToMany;
}

PHP
        );
    }

    public function testCalculateEntityNamespaceRootFromTwoEntityFqns()
    {
        $entity1Fqn = self::TEST_ENTITIES[0];
        $entity2Fqn = self::TEST_ENTITIES[1];
        $expected   = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER;
        $actual     = $this->helper->getEntityNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);

        $entity1Fqn = 'Test\\Thing\\Namespace\\Thingies\\Blah\\Foo';
        $entity2Fqn = 'Test\\Thing\\Namespace\\Thingies\\Bar\\Baz';
        $expected   = 'Test\\Thing\\Namespace\\Thingies';
        $actual     = $this->helper->getEntityNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);
    }

    public function testCalculateProjectNamespaceRootFromTwoEntityFqns()
    {
        $entity1Fqn = self::TEST_ENTITIES[0];
        $entity2Fqn = self::TEST_ENTITIES[1];
        $expected   = self::TEST_PROJECT_ROOT_NAMESPACE;
        $actual     = $this->helper->getProjectNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);

        $entity1Fqn = 'Test\\Thing\\Namespace\\Thingies\\Blah\\Foo';
        $entity2Fqn = 'Test\\Thing\\Namespace\\Thingies\\Bar\\Baz';
        $expected   = 'Test\\Thing\\Namespace';
        $actual     = $this->helper->getProjectNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);

        $entity1Fqn = 'DSM\\Test\\Project\\Entities\\Company';
        $entity2Fqn = 'DSM\\Test\\Project\\Entities\\Relations\\Company\\Director\\Interfaces\\HasDirectors';
        $expected   = 'DSM\\Test\\Project';
        $actual     = $this->helper->getProjectNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);
    }

    public function testParseFullyQualifiedName()
    {
        $entity1Fqn           = self::TEST_ENTITIES[0];
        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE;
        $expected             = [
            'Foo',
            $projectRootNamespace . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Blah',
            [
                'src',
                'Entities',
                'Blah'
            ]
        ];
        $actual               = $this->helper->parseFullyQualifiedName($entity1Fqn, $srcOrTestSubFolder, $projectRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testCalculcateOwnedHasName()
    {
        $hasType        = RelationsGenerator::HAS_MANY_TO_MANY;
        $ownedEntityFqn = self::TEST_ENTITIES[0];
        $expected       = 'Foos';
        $actual         = $this->helper->getOwnedHasName($hasType, $ownedEntityFqn);
        $this->assertEquals($expected, $actual);
    }

    public function testGetEntitySubNamespace()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER;
        $expected              = 'Blah\\Foo';
        $actual                = $this->helper->getEntitySubNamespace($entityFqn, $entitiesRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testGetEntitySubPath()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER;
        $expected              = '/Blah/Foo.php';
        $actual                = $this->helper->getEntitySubPath($entityFqn, $entitiesRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testGetEntitySubPathNoExtension()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER;
        $expected              = '/Blah/Foo';
        $actual                = $this->helper->getEntitySubPath($entityFqn, $entitiesRootNamespace, false);
        $this->assertEquals($expected, $actual);
    }

    public function testGetInterfacesNamespaceForEntity()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER;
        $expected              = $entitiesRootNamespace . '\\Relations\\Blah\\Foo\\Interfaces';
        $actual                = $this->helper->getInterfacesNamespaceForEntity($entityFqn, $entitiesRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testGetTraitsNamespaceForEntity()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER;
        $expected              = $entitiesRootNamespace . '\\Relations\\Blah\\Foo\\Traits';
        $actual                = $this->helper->getTraitsNamespaceForEntity($entityFqn, $entitiesRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testGetEntityNamespaceRootFromEntityReflection()
    {

        $entityReflection = new \ReflectionClass(self::TEST_ENTITY_POST_CREATED);
        $expected         = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER;
        $actual           = $this->helper->getEntityNamespaceRootFromEntityReflection($entityReflection);
        $this->assertEquals($expected, $actual);
    }

    public function testgetHasPluralInterfaceFqnForEntity()
    {
        $entityFqn = self::TEST_ENTITY_POST_CREATED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Relations\\Meh\\Interfaces\\HasMehs';
        $actual    = $this->helper->getHasPluralInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = self::TEST_ENTITY_POST_CREATED_NESTED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Relations\\Nested\\Something\\Ho\\Hum\\Interfaces\\HasHums';
        $actual    = $this->helper->getHasPluralInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    public function testgetHasSingularInterfaceFqnForEntity()
    {
        $entityFqn = self::TEST_ENTITY_POST_CREATED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Relations\\Meh\\Interfaces\\HasMeh';
        $actual    = $this->helper->getHasSingularInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);

        $entityFqn = self::TEST_ENTITY_POST_CREATED_NESTED;
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_FOLDER . '\\Relations\\Nested\\Something\\Ho\\Hum\\Interfaces\\HasHum';
        $actual    = $this->helper->getHasSingularInterfaceFqnForEntity($entityFqn);
        $this->assertEquals($expected, $actual);
    }

    public function testGetProjectRootNamespaceFromComposerJson()
    {
        $expected = 'EdmondsCommerce\\DoctrineStaticMeta';
        $actual   = $this->helper->getProjectRootNamespaceFromComposerJson();
        $this->assertEquals($expected, $actual);
    }

}
