<?php declare(strict_types=1);

namespace DSM\Test\Project\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaValidator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Faker;
use Faker\ORM\Doctrine\Populator;
use PHPUnit\Framework\TestCase;


abstract class AbstractEntityTest extends TestCase
{
    protected $testedEntityFqn;

    protected $testedEntityReflectionClass;

    protected $generator;

    /**
     * @var EntityManager
     */
    protected $em;

    protected $schemaErrors = [];

    protected function setup()
    {
        $this->getEntityManager(true);
    }

    /**
     * Use Doctrine's standard schema validation to get errors for the whole schema
     *
     * @param bool $update
     *
     * @return array
     * @throws \Exception
     */
    protected function getSchemaErrors(bool $update = false): array
    {
        if (empty($this->schemaErrors) || true === $update) {
            $em                 = $this->getEntityManager();
            $validator          = new SchemaValidator($em);
            $this->schemaErrors = $validator->validateMapping();
        }

        return $this->schemaErrors;
    }

    /**
     * If a global function dsmGetEntityManagerFactory is defined, we use this
     *
     * Otherwise, we use the standard DevEntityManagerFactory,
     * we define a DB name which is the main DB from env but with `_test` suffixed
     *
     * @param bool $new
     *
     * @return EntityManager
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     * @throws \Exception
     * @throws \ReflectionException
     */
    protected function getEntityManager(bool $new = false): EntityManager
    {
        if (null === $this->em || true === $new) {
            if (function_exists('dsmGetEntityManagerFactory')) {
                $this->em = dsmGetEntityManagerFactory();
            } else {
                SimpleEnv::setEnv(Config::getProjectRootDirectory().'/.env');
                $server                                 = $_SERVER;
                $server[ConfigInterface::PARAM_DB_NAME] .= '_test';
                $config                                 = new Config($server);
                $this->em                               = EntityManagerFactory::getEntityManager($config, false);

            }
        }

        return $this->em;
    }

    /**
     * Use Doctrine's built in schema validation tool to catch issues
     */
    public function testValidateSchema()
    {
        $errors  = $this->getSchemaErrors();
        $class   = $this->getTestedEntityFqn();
        $message = '';
        if (isset($errors[$class])) {
            $message = "Failed ORM Validate Schema:\n";
            foreach ($errors[$class] as $err) {
                $message .= "\n * $err \n";
            }
        }
        $this->assertEmpty($message);
    }

    /**
     * Test that we have correctly generated an instance of our test entity
     */
    public function testGeneratedCreate()
    {
        $em        = $this->getEntityManager();
        $class     = $this->getTestedEntityFqn();
        $generated = $this->generateEntity($class);
        $this->assertInstanceOf($class, $generated);
        $meta = $em->getClassMetadata($class);
        foreach ($meta->getFieldNames() as $f) {
            $method = 'get'.$f;
            $this->assertNotEmpty($generated->$method(), "$f getter returned empty");
        }
        $em->persist($generated);
        $em->flush();
        $em     = $this->getEntityManager(true);
        $loaded = $em->getRepository($class)->find($generated->getId());
        $this->assertInstanceOf($class, $loaded);
        foreach ($meta->getAssociationMappings() as $mapping) {
            $getter = 'get'.$mapping['fieldName'];
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $collection = $loaded->$getter()->toArray();
                $this->assertNotEmpty(
                    $collection,
                    'Failed to load the collection of the associated entity ['.$mapping['fieldName']
                    .'] from the generated '.$class
                    .', make sure you have reciprocal adding of the association'
                );
                $this->assertCorrectMapping($class, $mapping, $em);
            } else {
                $association = $loaded->$getter();
                $this->assertNotEmpty($association,
                                      'Failed to load the associated entity: ['.$mapping['fieldName']
                                      .'] from the generated '.$class);
                $this->assertNotEmpty($association->getId(),
                                      'Failed to get the ID of the associated entity: ['.$mapping['fieldName']
                                      .'] from the generated '.$class);
            }
        }
    }

    /**
     * Check the mapping of our class and the associated entity to make sure it's configured properly on both sides.
     * Very easy to get wrong. This is in addition to the standard Schema Validation
     *
     * @param string        $classFqn
     * @param array         $mapping
     * @param EntityManager $entityManager
     */
    protected function assertCorrectMapping(string $classFqn, array $mapping, EntityManager $entityManager)
    {
        $pass                                 = false;
        $associationFqn                       = $mapping['targetEntity'];
        $associationMeta                      = $entityManager->getClassMetadata($associationFqn);
        $classTraits                          = $entityManager->getClassMetadata($classFqn)->getReflectionClass()->getTraits();
        $unidirectionalTraitShortNamePrefixes = [
            'Has'.$associationFqn::getSingular().RelationsGenerator::PREFIX_UNIDIRECTIONAL,
            'Has'.$associationFqn::getPlural().RelationsGenerator::PREFIX_UNIDIRECTIONAL,
        ];
        foreach ($classTraits as $trait) {
            foreach ($unidirectionalTraitShortNamePrefixes as $namePrefix) {
                if (0 === \strpos($trait->getShortName(), $namePrefix)) {
                    return;
                }
            }
        }
        foreach ($associationMeta->getAssociationMappings() as $associationMapping) {
            if ($classFqn === $associationMapping['targetEntity']) {
                if (empty($mapping['joinTable'])) {
                    $this->assertArrayNotHasKey(
                        'joinTable',
                        $associationMapping,
                        $classFqn.' join table is empty,
                        but association '.$mapping['targetEntity'].' join table is not empty'
                    );
                } else {
                    $this->assertNotEmpty(
                        $associationMapping['joinTable'],
                        "$classFqn joinTable is set to ".$mapping['joinTable']['name']
                        ." \n association ".$mapping['targetEntity']." join table is empty"
                    );
                    $this->assertSame(
                        $mapping['joinTable']['name'],
                        $associationMapping['joinTable']['name'],
                        "join tables not the same: \n * $classFqn = ".$mapping['joinTable']['name']
                        ." \n * association ".$mapping['targetEntity']
                        ." = ".$associationMapping['joinTable']['name']
                    );
                    $this->assertArrayHasKey(
                        'inverseJoinColumns',
                        $associationMapping['joinTable'],
                        "join table join columns not the same: \n * $classFqn joinColumn = "
                        .$mapping['joinTable']['joinColumns'][0]['name']
                        ." \n * association ".$mapping['targetEntity']
                        ." inverseJoinColumn is not set"
                    );
                    $this->assertSame(
                        $mapping['joinTable']['joinColumns'][0]['name'],
                        $associationMapping['joinTable']['inverseJoinColumns'][0]['name'],
                        "join table join columns not the same: \n * $classFqn joinColumn = "
                        .$mapping['joinTable']['joinColumns'][0]['name']
                        ." \n * association ".$mapping['targetEntity']
                        ." inverseJoinColumn = ".$associationMapping['joinTable']['inverseJoinColumns'][0]['name']
                    );
                    $this->assertSame(
                        $mapping['joinTable']['inverseJoinColumns'][0]['name'],
                        $associationMapping['joinTable']['joinColumns'][0]['name'],
                        "join table join columns  not the same: \n * $classFqn inverseJoinColumn = "
                        .$mapping['joinTable']['inverseJoinColumns'][0]['name']
                        ." \n * association ".$mapping['targetEntity']." joinColumn = "
                        .$associationMapping['joinTable']['joinColumns'][0]['name']
                    );
                }
                $pass = true;
                break;
            }
        }
        $this->assertTrue($pass, 'Failed finding association mapping to test for '."\n".$mapping['targetEntity']);
    }

    /**
     * @param string $class
     * @param bool   $generateAssociations
     *
     * @return object
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     * @throws \Exception
     * @throws \ReflectionException
     */
    protected function generateEntity(string $class, bool $generateAssociations = true)
    {
        $em = $this->getEntityManager();
        if (!$this->generator) {
            $this->generator = Faker\Factory::create();
        }
        $customColumnFormatters = $this->generateAssociationColumnFormatters($em, $class);
        $populator              = new Populator($this->generator, $em);
        $populator->addEntity($class, 1, $customColumnFormatters);
        $generated = $populator->execute()[$class][0];
        if ($generateAssociations) {
            $this->addAssociationEntities($em, $generated);
        }

        return $generated;
    }

    /**
     * @param EntityManager $em
     * @param string        $class
     *
     * @return array
     */
    protected function generateAssociationColumnFormatters(EntityManager $em, string $class): array
    {
        $return   = [];
        $meta     = $em->getClassMetadata($class);
        $mappings = $meta->getAssociationMappings();
        if ($mappings) {
            foreach ($mappings as $mapping) {
                if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                    $return[$mapping['fieldName']] = new ArrayCollection();
                } else {
                    $return[$mapping['fieldName']] = null;
                }
            }
        }

        return $return;
    }


    /**
     * @param EntityManager $em
     * @param object        $generated
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     * @throws \Exception
     * @throws \ReflectionException
     */
    protected function addAssociationEntities(EntityManager $em, $generated)
    {
        $entityReflection = $this->getTestedEntityReflectionClass();
        $class            = $entityReflection->getName();
        $meta             = $em->getClassMetadata($class);
        $mappings         = $meta->getAssociationMappings();
        if (!$mappings) {
            return;
        }
        $namespaceHelper = new NamespaceHelper();
        $methods         = array_map('strtolower', get_class_methods($generated));
        foreach ($mappings as $mapping) {
            $mappingEntityClass = $mapping['targetEntity'];
            $mappingEntity      = $this->generateEntity($mappingEntityClass, false);
            $errorMessage       = "Error adding association entity $mappingEntityClass to $class: %s";
            $em->persist($mappingEntity);
            $mappingEntityPluralInterface = $namespaceHelper->getHasPluralInterfaceFqnForEntity($mappingEntityClass);
            if ($entityReflection->implementsInterface($mappingEntityPluralInterface)) {
                $this->assertEquals
                (
                    $mappingEntityClass::getPlural(),
                    $mapping['fieldName'],
                    sprintf($errorMessage, ' mapping should be plural')
                );
                $method = 'add'.$mappingEntityClass::getSingular();
            } else {
                $this->assertEquals(
                    $mappingEntityClass::getSingular(),
                    $mapping['fieldName'],
                    sprintf($errorMessage, ' mapping should be singular')
                );
                $method = 'set'.$mappingEntityClass::getSingular();
            }
            $this->assertContains(
                strtolower($method),
                $methods,
                sprintf($errorMessage, $method.' method is not defined')
            );
            $generated->$method($mappingEntity);
        }
    }


    /**
     * Get the fully qualified name of the Entity we are testing,
     * assumes EntityNameTest as the entity class short name
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getTestedEntityFqn(): string
    {
        if (!$this->testedEntityFqn) {
            $ref                   = new \ReflectionClass($this);
            $namespace             = $ref->getNamespaceName();
            $shortName             = $ref->getShortName();
            $className             = substr($shortName, 0, strpos($shortName, 'Test'));
            $this->testedEntityFqn = $namespace.'\\'.$className;
        }

        return $this->testedEntityFqn;
    }

    /**
     * Get a \ReflectionClass for the currently tested Entity
     *
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    protected function getTestedEntityReflectionClass(): \ReflectionClass
    {
        if (!$this->testedEntityReflectionClass) {
            $this->testedEntityReflectionClass = new \ReflectionClass($this->getTestedEntityFqn());
        }

        return $this->testedEntityReflectionClass;
    }
}
