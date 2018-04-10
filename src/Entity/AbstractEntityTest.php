<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaValidator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractSaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Faker;
use Faker\ORM\Doctrine\Populator;

/**
 * Class AbstractEntityTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractEntityTest extends AbstractTest
{
    public const GET_ENTITY_MANAGER_FUNCTION_NAME = 'dsmGetEntityManagerFactory';

    protected $testedEntityFqn;

    protected $saverFqn;

    protected $testedEntityReflectionClass;

    protected $generator;

    /**
     * @var EntityValidator
     */
    protected $entityValidator;

    /**
     * @var EntityValidatorFactory
     */
    protected $entityValidatorFactory;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    protected $schemaErrors = [];

    /**
     * @var AbstractSaver
     */
    protected $entitySaver;

    protected function setup()
    {
        $this->getEntityManager(true);
        $this->entityValidatorFactory = new EntityValidatorFactory(new ArrayCache());
        $this->entityValidator = $this->entityValidatorFactory->getEntityValidator();
    }

    /**
     * Use Doctrine's standard schema validation to get errors for the whole schema
     *
     * @param bool $update
     *
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function getSchemaErrors(bool $update = false): array
    {
        if (empty($this->schemaErrors) || true === $update) {
            $entityManager      = $this->getEntityManager();
            $validator          = new SchemaValidator($entityManager);
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
     * @throws ConfigException
     * @throws \Exception
     * @SuppressWarnings(PHPMD)
     */
    protected function getEntityManager(bool $new = false): EntityManager
    {
        if (null === $this->entityManager || true === $new) {
            if (\function_exists(self::GET_ENTITY_MANAGER_FUNCTION_NAME)) {
                $this->entityManager = \call_user_func(self::GET_ENTITY_MANAGER_FUNCTION_NAME);
            } else {
                SimpleEnv::setEnv(Config::getProjectRootDirectory().'/.env');
                $testConfig                                 = $_SERVER;
                $testConfig[ConfigInterface::PARAM_DB_NAME] = $_SERVER[ConfigInterface::PARAM_DB_NAME].'_test';
                $config                                 = new Config($testConfig);
                $this->entityManager                    = (new EntityManagerFactory(new ArrayCache()))
                    ->getEntityManager($config);
            }
        }

        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     * @return AbstractSaver
     * @throws \ReflectionException
     */
    protected function getSaver(EntityManager $entityManager): AbstractSaver
    {
        if (! $this->entitySaver) {
            $saverFqn = $this->getSaverFqn();
            $this->entitySaver = new $saverFqn($entityManager, $this->entityValidatorFactory);
        }

        return $this->entitySaver;
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
     * @param string        $class
     * @param int|string    $id
     * @param EntityManager $entityManager
     *
     * @return IdFieldInterface|null
     */
    protected function loadEntity(string $class, $id, EntityManager $entityManager): ?IdFieldInterface
    {
        return $entityManager->getRepository($class)->find($id);
    }

    /**
     * Test that we have correctly generated an instance of our test entity
     */
    public function testGeneratedCreate()
    {
        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $generated     = $this->generateEntity($class);
        $saver         = $this->getSaver($entityManager);
        $this->addAssociationEntities($entityManager, $generated);
        $this->assertInstanceOf($class, $generated);
        $this->validateEntity($generated);
        $meta = $entityManager->getClassMetadata($class);
        foreach ($meta->getFieldNames() as $f) {
            $method = 'get'.$f;
            $reflectionMethod = new \ReflectionMethod($generated, $method);
            if ($reflectionMethod->hasReturnType()) {
                $returnType = $reflectionMethod->getReturnType();
                $allowsNull = $returnType->allowsNull();
                if ($allowsNull) {
                    // As we can't assert anything here so simply call
                    // the method and allow the type hint to raise any
                    // errors.
                    $generated->$method();
                    continue;
                }
            }
            $this->assertNotEmpty($generated->$method(), "$f getter returned empty");
        }
//        $entityManager->persist($generated);
//        $entityManager->flush();
        $saver->save($generated);
        $entityManager = $this->getEntityManager(true);
        $loaded        = $this->loadEntity($class, $generated->getId(), $entityManager);
        $this->assertInstanceOf($class, $loaded);
        $this->validateEntity($loaded);
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
                $this->assertCorrectMapping($class, $mapping, $entityManager);
                continue;
            }
            $association = $loaded->$getter();
            $this->assertNotEmpty(
                $association,
                'Failed to load the associated entity: ['.$mapping['fieldName']
                .'] from the generated '.$class
            );
            $this->assertNotEmpty(
                $association->getId(),
                'Failed to get the ID of the associated entity: ['.$mapping['fieldName']
                .'] from the generated '.$class
            );
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
        $classTraits                          = $entityManager->getClassMetadata($classFqn)
                                                              ->getReflectionClass()
                                                              ->getTraits();
        $unidirectionalTraitShortNamePrefixes = [
            'Has'.$associationFqn::getSingular().RelationsGenerator::PREFIX_UNIDIRECTIONAL,
            'Has'.$associationFqn::getPlural().RelationsGenerator::PREFIX_UNIDIRECTIONAL,
        ];
        foreach ($classTraits as $trait) {
            foreach ($unidirectionalTraitShortNamePrefixes as $namePrefix) {
                if (0 === \stripos($trait->getShortName(), $namePrefix)) {
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
                    $pass = true;
                    break;
                }
                $this->assertNotEmpty(
                    $associationMapping['joinTable'],
                    "$classFqn joinTable is set to ".$mapping['joinTable']['name']
                    ." \n association ".$mapping['targetEntity'].' join table is empty'
                );
                $this->assertSame(
                    $mapping['joinTable']['name'],
                    $associationMapping['joinTable']['name'],
                    "join tables not the same: \n * $classFqn = ".$mapping['joinTable']['name']
                    ." \n * association ".$mapping['targetEntity']
                    .' = '.$associationMapping['joinTable']['name']
                );
                $this->assertArrayHasKey(
                    'inverseJoinColumns',
                    $associationMapping['joinTable'],
                    "join table join columns not the same: \n * $classFqn joinColumn = "
                    .$mapping['joinTable']['joinColumns'][0]['name']
                    ." \n * association ".$mapping['targetEntity']
                    .' inverseJoinColumn is not set'
                );
                $this->assertSame(
                    $mapping['joinTable']['joinColumns'][0]['name'],
                    $associationMapping['joinTable']['inverseJoinColumns'][0]['name'],
                    "join table join columns not the same: \n * $classFqn joinColumn = "
                    .$mapping['joinTable']['joinColumns'][0]['name']
                    ." \n * association ".$mapping['targetEntity']
                    .' inverseJoinColumn = '.$associationMapping['joinTable']['inverseJoinColumns'][0]['name']
                );
                $this->assertSame(
                    $mapping['joinTable']['inverseJoinColumns'][0]['name'],
                    $associationMapping['joinTable']['joinColumns'][0]['name'],
                    "join table join columns  not the same: \n * $classFqn inverseJoinColumn = "
                    .$mapping['joinTable']['inverseJoinColumns'][0]['name']
                    ." \n * association ".$mapping['targetEntity'].' joinColumn = '
                    .$associationMapping['joinTable']['joinColumns'][0]['name']
                );
                $pass = true;
                break;
            }
        }
        $this->assertTrue($pass, 'Failed finding association mapping to test for '."\n".$mapping['targetEntity']);
    }

    /**
     * @param string $class
     *
     * @return IdFieldInterface
     * @throws ConfigException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function generateEntity(string $class): IdFieldInterface
    {
        $entityManager = $this->getEntityManager();
        if (!$this->generator) {
            $this->generator = Faker\Factory::create();
        }
        $customColumnFormatters = $this->generateAssociationColumnFormatters($entityManager, $class);
        $populator              = new Populator($this->generator, $entityManager);
        $populator->addEntity($class, 1, $customColumnFormatters);

        $entity = $populator->execute()[$class][0];

        return $entity;
    }

    protected function validateEntity($entity): void
    {
        $errors = $this->entityValidator->setEntity($entity)->validate();
        $this->assertEmpty($errors);
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $class
     *
     * @return array
     */
    protected function generateAssociationColumnFormatters(EntityManager $entityManager, string $class): array
    {
        $return   = [];
        $meta     = $entityManager->getClassMetadata($class);
        $mappings = $meta->getAssociationMappings();
        if (empty($mappings)) {
            return $return;
        }
        foreach ($mappings as $mapping) {
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $return[$mapping['fieldName']] = new ArrayCollection();
                continue;
            }
            $return[$mapping['fieldName']] = null;
        }

        return $return;
    }

    /**
     * @param EntityManager    $entityManager
     * @param IdFieldInterface $generated
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws ConfigException
     * @throws \Exception
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function addAssociationEntities(
        EntityManager $entityManager,
        IdFieldInterface $generated,
        AbstractSaver $saver
    ) {
        $entityReflection = $this->getTestedEntityReflectionClass();
        $class            = $entityReflection->getName();
        $meta             = $entityManager->getClassMetadata($class);
        $mappings         = $meta->getAssociationMappings();
        if (empty($mappings)) {
            return;
        }
        $namespaceHelper = new NamespaceHelper();
        $methods         = array_map('strtolower', get_class_methods($generated));
        foreach ($mappings as $mapping) {
            $mappingEntityClass = $mapping['targetEntity'];
            $mappingEntity      = $this->generateEntity($mappingEntityClass);
            $errorMessage       = "Error adding association entity $mappingEntityClass to $class: %s";
//            $entityManager->persist($mappingEntity);
            $saver->save($mappingEntity);
            $mappingEntityPluralInterface = $namespaceHelper->getHasPluralInterfaceFqnForEntity($mappingEntityClass);
            if ($entityReflection->implementsInterface($mappingEntityPluralInterface)) {
                $this->assertEquals(
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
     * Get the fully qualified name of the saver for the entity we are testing.
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getSaverFqn(): string
    {
        if (! $this->saverFqn) {
            $ref             = new \ReflectionClass($this);
            $entityNamespace = $ref->getNamespaceName();
            $saverNamespace  = \str_replace(
                'Entities',
                'Entity\\Savers',
                $entityNamespace
            );
            $shortName       = $ref->getShortName();
            $className       = substr($shortName, 0, strpos($shortName, 'Test'));
            $this->saverFqn  = $saverNamespace.'\\'.$className.'Saver';
        }

        return $this->saverFqn;
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
