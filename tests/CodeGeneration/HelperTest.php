<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;

class NamespaceHelperTest extends AbstractTest
{

    const WORK_DIR = VAR_PATH . '/NamespaceHelperTest';

    const TEST_ENTITIES = [
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Blah\\Foo',
        self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Bar\\Baz'
    ];

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
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        $relationsGenerator = new RelationsGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        foreach (self::TEST_ENTITIES as $fqn) {
            $entityGenerator->generateEntity($fqn);
            $relationsGenerator->generateRelationCodeForEntity($fqn);
        }
        $relationsGenerator->setEntityHasRelationToEntity(self::TEST_ENTITIES[0], RelationsGenerator::HAS_MANY_TO_MANY, self::TEST_ENTITIES[1]);
    }

    public function testCalculateEntityNamespaceRootFromTwoEntityFqns()
    {
        $entity1Fqn = self::TEST_ENTITIES[0];
        $entity2Fqn = self::TEST_ENTITIES[1];
        $expected   = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $actual     = $this->helper->calculateEntityNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);

        $entity1Fqn = 'Test\\Thing\\Namespace\\Thingies\\Blah\\Foo';
        $entity2Fqn = 'Test\\Thing\\Namespace\\Thingies\\Bar\\Baz';
        $expected   = 'Test\\Thing\\Namespace\\Thingies';
        $actual     = $this->helper->calculateEntityNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);
    }

    public function testCalculateProjectNamespaceRootFromTwoEntityFqns()
    {
        $entity1Fqn = self::TEST_ENTITIES[0];
        $entity2Fqn = self::TEST_ENTITIES[1];
        $expected   = self::TEST_PROJECT_ROOT_NAMESPACE;
        $actual     = $this->helper->calculateProjectNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);

        $entity1Fqn = 'Test\\Thing\\Namespace\\Thingies\\Blah\\Foo';
        $entity2Fqn = 'Test\\Thing\\Namespace\\Thingies\\Bar\\Baz';
        $expected   = 'Test\\Thing\\Namespace';
        $actual     = $this->helper->calculateProjectNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);
        $this->assertEquals($expected, $actual);
    }

    public function testParseFullyQualifiedName()
    {
        $entity1Fqn           = self::TEST_ENTITIES[0];
        $srcOrTestSubFolder   = 'src';
        $projectRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE;
        $expected             = [
            'Foo',
            $projectRootNamespace . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Blah',
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
        $actual         = $this->helper->calculateOwnedHasName($hasType, $ownedEntityFqn);
        $this->assertEquals($expected, $actual);
    }

    public function testGetEntitySubNamespace()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $expected              = 'Blah';
        $actual                = $this->helper->getEntitySubNamespace($entityFqn, $entitiesRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testGetInterfacesNamespaceForEntity()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $expected              = $entitiesRootNamespace . '\\Relations\\Blah\\Interfaces';
        $actual                = $this->helper->getInterfacesNamespaceForEntity($entityFqn, $entitiesRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testGetTraitsNamespaceForEntity()
    {
        $entityFqn             = self::TEST_ENTITIES[0];
        $entitiesRootNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $expected              = $entitiesRootNamespace . '\\Relations\\Blah\\Traits';
        $actual                = $this->helper->getTraitsNamespaceForEntity($entityFqn, $entitiesRootNamespace);
        $this->assertEquals($expected, $actual);
    }

    public function testGetEntityNamespaceRootFromEntityReflection()
    {
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
        $entityReflection = new \ReflectionClass(self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\Meh');
        $expected         = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $actual           = $this->helper->getEntityNamespaceRootFromEntityReflection($entityReflection);
        $this->assertEquals($expected, $actual);

    }

}
