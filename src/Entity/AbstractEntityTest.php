<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Utility\PersisterHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Attribute\IpAddressFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractSaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Faker;
use Faker\ORM\Doctrine\Populator;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

/**
 * Class AbstractEntityTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractEntityTest extends AbstractTest
{
    public const GET_ENTITY_MANAGER_FUNCTION_NAME = 'dsmGetEntityManagerFactory';

    /**
     * The fully qualified name of the Entity being tested, as calculated by the test class name
     *
     * @var string
     */
    protected $testedEntityFqn;

    /**
     * Reflection of the tested entity
     *
     * @var \ReflectionClass
     */
    protected $testedEntityReflectionClass;

    /**
     * @var Faker\Generator
     */
    protected $generator;

    /**
     * @var EntityValidatorInterface
     */
    protected $entityValidator;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $schemaErrors = [];

    /**
     * Standard library faker data provider FQNs
     *
     * This const should be overridden in your child class and extended with any project specific field data providers
     * in addition to the standard library
     *
     * The key is the column/property name and the value is the FQN for the data provider
     */
    public const FAKER_DATA_PROVIDERS = [
        IpAddressFieldInterface::PROP_IP_ADDRESS => IpAddressFakerData::class,
    ];

    /**
     * Faker can be seeded with a number which makes the generation deterministic
     */
    public const SEED = 100111991161141051101013211511697116105993210910111697;

    /**
     * A cache of instantiated column data providers
     *
     * @var array
     */
    protected $fakerDataProviderObjects = [];

    protected function setup()
    {
        $this->getEntityManager(true);
        $this->entityValidator = (
        new EntityValidatorFactory(new DoctrineCache(new ArrayCache()))
        )->getEntityValidator();
        $this->generator       = Faker\Factory::create();
        $this->generator->seed(static::SEED);
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
                $config                                     = new Config($testConfig);
                $this->entityManager                        = (new EntityManagerFactory(new ArrayCache()))
                    ->getEntityManager($config);
            }
        }

        return $this->entityManager;
    }

    /**
     * @param EntityManager   $entityManager
     * @param EntityInterface $entity
     *
     * @return AbstractSaver
     * @throws \ReflectionException
     */
    protected function getSaver(
        EntityManager $entityManager,
        EntityInterface $entity
    ): AbstractSaver {
        $saverFqn = $this->getSaverFqn($entity);

        return new $saverFqn($entityManager);
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
     * @return EntityInterface|null
     */
    protected function loadEntity(string $class, $id, EntityManager $entityManager): ?EntityInterface
    {
        return $entityManager->getRepository($class)->find($id);
    }

    /**
     * Test that we have correctly generated an instance of our test entity
     *
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \ReflectionException
     */
    public function testGeneratedCreate()
    {
//        $this->markTestIncomplete(
//            'We need to configure Faker to populate the fields correctly now they are being validated'
//        );

        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $generated     = $this->generateEntity($class);
        $this->assertInstanceOf($class, $generated);
        $saver = $this->getSaver($entityManager, $generated);
        $this->addAssociationEntities($entityManager, $generated);
        $this->validateEntity($generated);
        $meta = $entityManager->getClassMetadata($class);
        foreach ($meta->getFieldNames() as $fieldName) {
            $type             = PersisterHelper::getTypeOfField($fieldName, $meta, $entityManager);
            $method           = ($type[0] === 'boolean' ? 'is' : 'get').$fieldName;
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
            $this->assertNotEmpty($generated->$method(), "$fieldName getter returned empty");
        }
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
     * @return EntityInterface
     * @throws ConfigException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function generateEntity(string $class): EntityInterface
    {
        $entityManager          = $this->getEntityManager();
        $customColumnFormatters = $this->generateColumnFormatters($entityManager, $class);
        $populator              = new Populator($this->generator, $entityManager);
        $populator->addEntity($class, 1, $customColumnFormatters);

        $entity = $populator->execute()[$class][0];

        return $entity;
    }

    protected function validateEntity(EntityInterface $entity): void
    {
        $entity->setValidator($this->entityValidator);
        $entity->validate();
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $class
     *
     * @return array
     */
    protected function generateColumnFormatters(EntityManager $entityManager, string $class): array
    {
        $columnFormatters = [];
        $meta             = $entityManager->getClassMetadata($class);
        $mappings         = $meta->getAssociationMappings();
        foreach ($mappings as $mapping) {
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $columnFormatters[$mapping['fieldName']] = new ArrayCollection();
                continue;
            }
            $columnFormatters[$mapping['fieldName']] = null;
        }
        $columns = $meta->getColumnNames();
        foreach ($columns as $column) {
            if (!isset($columnFormatters[$column])) {
                $this->setFakerDataProvider($columnFormatters, $column);
            }
        }

        return $columnFormatters;
    }

    /**
     * Add a faker data provider to the columnFormatters array (by reference) if there is one available
     *
     * Handles instantiating and caching of the data providers
     *
     * @param array  $columnFormatters
     * @param string $column
     */
    protected function setFakerDataProvider(array &$columnFormatters, string $column): void
    {
        if (!isset(static::FAKER_DATA_PROVIDERS[$column])) {
            return;
        }
        if (!isset($this->fakerDataProviderObjects[$column])) {
            $class                                   = static::FAKER_DATA_PROVIDERS[$column];
            $this->fakerDataProviderObjects[$column] = new $class($this->generator);
        }
        $columnFormatters[$column] = $this->fakerDataProviderObjects[$column];
    }

    /**
     * @param EntityManager   $entityManager
     * @param EntityInterface $generated
     *
     * @throws ConfigException
     * @throws \Exception
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function addAssociationEntities(
        EntityManager $entityManager,
        EntityInterface $generated
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
            $saver              = $this->getSaver($entityManager, $mappingEntity);
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
     */
    protected function getTestedEntityFqn(): string
    {
        if (null === $this->testedEntityFqn) {
            $this->testedEntityFqn = \substr(static::class, 0, -4);
        }

        return $this->testedEntityFqn;
    }

    /**
     * Get the fully qualified name of the saver for the entity we are testing.
     *
     * @param EntityInterface $entity
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getSaverFqn(
        EntityInterface $entity
    ): string {
        $ref             = new \ReflectionClass($entity);
        $entityNamespace = $ref->getNamespaceName();
        $saverNamespace  = \str_replace(
            'Entities',
            'Entity\\Savers',
            $entityNamespace
        );
        $shortName       = $ref->getShortName();

        return $saverNamespace.'\\'.$shortName.'Saver';
    }

    /**
     * Get a \ReflectionClass for the currently tested Entity
     *
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    protected function getTestedEntityReflectionClass(): \ReflectionClass
    {
        if (null === $this->testedEntityReflectionClass) {
            $this->testedEntityReflectionClass = new \ReflectionClass(
                $this->getTestedEntityFqn()
            );
        }

        return $this->testedEntityReflectionClass;
    }
}
