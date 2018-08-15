<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Utility\PersisterHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

/**
 * Class AbstractEntityTest
 *
 * This abstract test is designed to give you a good level of test coverage for your entities without any work required.
 *
 * You should extend the test with methods that test your specific business logic, your validators and anything else.
 *
 * You can override the methods, properties and constants as you see fit.
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class AbstractEntityTest extends TestCase implements EntityTestInterface
{
    /**
     * The fully qualified name of the Entity being tested, as calculated by the test class name
     *
     * @var string
     */
    protected $testedEntityFqn;

    /**
     * Reflection of the tested entity
     *
     * @var \ts\Reflection\ReflectionClass
     */
    protected $testedEntityReflectionClass;


    /**
     * @var EntityValidatorFactory
     */
    protected $entityValidatorFactory;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $schemaErrors = [];


    /**
     * @var TestEntityGenerator
     */
    protected $testEntityGenerator;

    /**
     * @var EntitySaverFactory
     */
    protected $entitySaverFactory;

    /**
     * @var CodeHelper
     */
    protected $codeHelper;

    /**
     * @throws ConfigException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setup()
    {
        $this->getEntityManager(true);
        $this->entityValidatorFactory = new EntityValidatorFactory(new DoctrineCache(new ArrayCache()));
        $this->entitySaverFactory     = new EntitySaverFactory(
            $this->entityManager,
            new EntitySaver($this->entityManager),
            new NamespaceHelper()
        );
        $this->testEntityGenerator    = new TestEntityGenerator(
            static::SEED,
            static::FAKER_DATA_PROVIDERS,
            $this->getTestedEntityReflectionClass(),
            $this->entitySaverFactory,
            $this->entityValidatorFactory
        );
        $this->codeHelper             = new CodeHelper(new NamespaceHelper());
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
     * @return EntityManagerInterface
     * @throws ConfigException
     * @throws \Exception
     * @SuppressWarnings(PHPMD)
     */
    protected function getEntityManager(bool $new = false): EntityManagerInterface
    {
        if (null === $this->entityManager || true === $new) {
            if (\function_exists(self::GET_ENTITY_MANAGER_FUNCTION_NAME)) {
                $this->entityManager = \call_user_func(self::GET_ENTITY_MANAGER_FUNCTION_NAME);
            } else {
                SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
                $testConfig                                 = $_SERVER;
                $testConfig[ConfigInterface::PARAM_DB_NAME] = $_SERVER[ConfigInterface::PARAM_DB_NAME] . '_test';
                $config                                     = new Config($testConfig);
                $this->entityManager                        = (new EntityManagerFactory(new ArrayCache()))
                    ->getEntityManager($config);
            }
        }

        return $this->entityManager;
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
        self::assertEmpty($message, $message);
    }


    /**
     * @param string        $class
     * @param int|string    $id
     * @param EntityManagerInterface $entityManager
     *
     * @return EntityInterface|null
     */
    protected function loadEntity(string $class, $id, EntityManagerInterface $entityManager): ?EntityInterface
    {
        return $entityManager->getRepository($class)->find($id);
    }

    public function testConstructor(): EntityInterface
    {
        $class  = $this->getTestedEntityFqn();
        $entity = new $class($this->entityValidatorFactory);
        self::assertInstanceOf($class, $entity);

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     *
     * @throws ConfigException
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \ReflectionException
     * @depends testConstructor
     */
    public function testGetDefaults(EntityInterface $entity)
    {
        $this->callEntityGettersAndAssertNotNull($entity);
    }

    /**
     * Loop through Entity fields, call the getter and where possible assert there is a value returned
     *
     * @param EntityInterface $entity
     *
     * @throws ConfigException
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function callEntityGettersAndAssertNotNull(EntityInterface $entity): void
    {
        $class         = $this->getTestedEntityFqn();
        $entityManager = $this->getEntityManager();
        $meta          = $entityManager->getClassMetadata($class);
        foreach ($meta->getFieldNames() as $fieldName) {
            $type   = PersisterHelper::getTypeOfField($fieldName, $meta, $entityManager)[0];
            $method = $this->getGetterNameForField($fieldName, $type);
            if (\ts\stringContains($method, '.')) {
                list($getEmbeddableMethod,) = explode('.', $method);
                $embeddable = $entity->$getEmbeddableMethod();
                self::assertInstanceOf(AbstractEmbeddableObject::class, $embeddable);
                continue;
            }
            $reflectionMethod = new \ReflectionMethod($entity, $method);
            if ($reflectionMethod->hasReturnType()) {
                $returnType = $reflectionMethod->getReturnType();
                $allowsNull = $returnType->allowsNull();
                if ($allowsNull) {
                    // As we can't assert anything here so simply call
                    // the method and allow the type hint to raise any
                    // errors.
                    $entity->$method();
                    continue;
                }
                self::assertNotNull($entity->$method(), "$fieldName getter returned null");
                continue;
            }
            // If there is no return type then we can't assert anything,
            // but again we can just call the getter to check for errors
            $entity->$method();
        }
        if (0 === $this->getCount()) {
            self::markTestSkipped('No assertable getters in this Entity');
        }
    }

    /**
     * Generate a new entity and then update our Entity with the values from the generated one
     *
     * @param EntityInterface $entity
     *
     * @throws ConfigException
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function updateEntityFields(EntityInterface $entity): void
    {
        $class         = $this->getTestedEntityFqn();
        $entityManager = $this->getEntityManager();
        $meta          = $entityManager->getClassMetadata($class);
        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $generated     = $this->testEntityGenerator->generateEntity($entityManager, $class, 10);
        $identifiers   = \array_flip($meta->getIdentifier());
        foreach ($meta->getFieldNames() as $fieldName) {
            if (isset($identifiers[$fieldName])) {
                continue;
            }
            if (true === $this->isUniqueField($meta, $fieldName)) {
                continue;
            }
            $setter = 'set' . $fieldName;
            if (!\method_exists($entity, $setter)) {
                continue;
            }
            $type   = PersisterHelper::getTypeOfField($fieldName, $meta, $entityManager)[0];
            $getter = $this->getGetterNameForField($fieldName, $type);
            if (\ts\stringContains($getter, '.')) {
                list($getEmbeddableMethod, $fieldInEmbeddable) = explode('.', $getter);
                $getterInEmbeddable  = 'get' . $fieldInEmbeddable;
                $setterInEmbeddable  = 'set' . $fieldInEmbeddable;
                $generatedEmbeddable = $generated->$getEmbeddableMethod();
                $embeddable          = $entity->$getEmbeddableMethod();
                if (\method_exists($embeddable, $setterInEmbeddable)
                    && \method_exists($embeddable, $getterInEmbeddable)
                ) {
                    $embeddable->$setterInEmbeddable($generatedEmbeddable->$getterInEmbeddable());
                }
                continue;
            }
            $entity->$setter($generated->$getter());
        }
    }

    protected function isUniqueField(ClassMetadata $meta, string $fieldName): bool
    {
        $fieldMapping = $meta->getFieldMapping($fieldName);
        if (array_key_exists('unique', $fieldMapping) && true === $fieldMapping['unique']) {
            return true;
        }

        return false;
    }

    /**
     * Test that we have correctly generated an instance of our test entity
     *
     * @throws ConfigException
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Exception
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testGeneratedCreate(): EntityInterface
    {
        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $generated     = $this->testEntityGenerator->generateEntity($entityManager, $class);
        self::assertInstanceOf($class, $generated);
        $this->testEntityGenerator->addAssociationEntities($entityManager, $generated);
        $this->validateEntity($generated);
        $this->callEntityGettersAndAssertNotNull($generated);
        $this->entitySaverFactory->getSaverForEntity($generated)->save($generated);

        return $generated;
    }

    /**
     * Test that we can load the entity and then get and set
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface|null
     * @throws ConfigException
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @depends testGeneratedCreate
     */
    public function testLoadedEntity(EntityInterface $entity): EntityInterface
    {
        $class         = $this->getTestedEntityFqn();
        $entityManager = $this->getEntityManager();
        $loaded        = $this->loadEntity($class, $entity->getId(), $entityManager);
        self::assertSame($entity->getId(), $loaded->getId());
        self::assertInstanceOf($class, $loaded);
        $this->updateEntityFields($loaded);
        $this->assertAllAssociationsAreNotEmpty($loaded);
        $this->validateEntity($loaded);
        $this->removeAllAssociations($loaded);
        $this->assertAllAssociationsAreEmpty($loaded);
        $this->entitySaverFactory->getSaverForEntity($loaded)->save($loaded);

        return $loaded;
    }

    /**
     * @param EntityInterface $entity
     *
     * @throws ConfigException
     * @depends testLoadedEntity
     */
    public function testReloadedEntityHasNoAssociations(EntityInterface $entity): void
    {
        $class         = $this->getTestedEntityFqn();
        $entityManager = $this->getEntityManager();
        $reLoaded      = $this->loadEntity($class, $entity->getId(), $entityManager);
        self::assertEquals($entity->__toString(), $reLoaded->__toString());
        $this->assertAllAssociationsAreEmpty($reLoaded);
    }

    protected function assertAllAssociationsAreNotEmpty(EntityInterface $entity)
    {
        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $meta          = $entityManager->getClassMetadata($class);
        foreach ($meta->getAssociationMappings() as $mapping) {
            $getter = 'get' . $mapping['fieldName'];
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $collection = $entity->$getter()->toArray();
                self::assertCorrectMappings($class, $mapping, $entityManager);
                self::assertNotEmpty(
                    $collection,
                    'Failed to load the collection of the associated entity [' . $mapping['fieldName']
                    . '] from the generated ' . $class
                    . ', make sure you have reciprocal adding of the association'
                );

                continue;
            }
            $association = $entity->$getter();
            self::assertNotEmpty(
                $association,
                'Failed to load the associated entity: [' . $mapping['fieldName']
                . '] from the generated ' . $class
            );
            self::assertNotEmpty(
                $association->getId(),
                'Failed to get the ID of the associated entity: [' . $mapping['fieldName']
                . '] from the generated ' . $class
            );
        }
    }

    /**
     * @param EntityInterface $entity
     *
     * @throws ConfigException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function removeAllAssociations(EntityInterface $entity)
    {
        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $meta          = $entityManager->getClassMetadata($class);
        $identifiers = array_flip($meta->getIdentifier());
        foreach ($meta->getAssociationMappings() as $mapping) {
            if (isset($identifiers[$mapping['fieldName']])) {
                continue;
            }
            $remover = 'remove' . Inflector::singularize($mapping['fieldName']);
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $getter    = 'get' . $mapping['fieldName'];
                $relations = $entity->$getter();
                foreach ($relations as $relation) {
                    $entity->$remover($relation);
                }
                continue;
            }
            $entity->$remover();
        }
        $this->assertAllAssociationsAreEmpty($entity);
    }

    protected function assertAllAssociationsAreEmpty(EntityInterface $entity)
    {
        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $meta          = $entityManager->getClassMetadata($class);
        $identifiers = array_flip($meta->getIdentifier());
        foreach ($meta->getAssociationMappings() as $mapping) {
            if (isset($identifiers[$mapping['fieldName']])) {
                continue;
            }

            $getter = 'get' . $mapping['fieldName'];
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $collection = $entity->$getter()->toArray();
                self::assertEmpty(
                    $collection,
                    'Collection of the associated entity [' . $mapping['fieldName']
                    . '] is not empty after calling remove'
                );
                continue;
            }
            $association = $entity->$getter();
            self::assertEmpty(
                $association,
                'Failed to remove associated entity: [' . $mapping['fieldName']
                . '] from the generated ' . $class
            );
        }
    }

    /**
     * @depends testConstructor
     *
     * @param EntityInterface $entity
     */
    public function testGetGetters(EntityInterface $entity)
    {
        $getters = $entity->getGetters();
        self::assertNotEmpty($getters);
        foreach ($getters as $getter) {
            self::assertRegExp('%^(get|is|has).+%', $getter);
        }
    }

    /**
     * @depends testConstructor
     *
     * @param EntityInterface $entity
     */
    public function testSetSetters(EntityInterface $entity)
    {
        $setters = $entity->getSetters();
        self::assertNotEmpty($setters);
        foreach ($setters as $setter) {
            self::assertRegExp('%^(set|add).+%', $setter);
        }
    }


    protected function getGetterNameForField(string $fieldName, string $type): string
    {
        if ($type === 'boolean') {
            return $this->codeHelper->getGetterMethodNameForBoolean($fieldName);
        }

        return 'get' . $fieldName;
    }

    /**
     * Loop through entity fields and find unique ones
     *
     * Then ensure that the unique rule is being enforced as expected
     *
     * @throws ConfigException
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function testUniqueFieldsMustBeUnique(): void
    {
        $class         = $this->getTestedEntityFqn();
        $entityManager = $this->getEntityManager();
        $meta          = $entityManager->getClassMetadata($class);
        $uniqueFields  = [];
        foreach ($meta->getFieldNames() as $fieldName) {
            if (true === $this->isUniqueField($meta, $fieldName)) {
                $uniqueFields[] = $fieldName;
            }
        }
        if ([] === $uniqueFields) {
            self::markTestSkipped('No unique fields to check');

            return;
        }
        foreach ($uniqueFields as $fieldName) {
            $primary      = $this->testEntityGenerator->generateEntity($entityManager, $class);
            $secondary    = $this->testEntityGenerator->generateEntity($entityManager, $class);
            $getter       = 'get' . $fieldName;
            $setter       = 'set' . $fieldName;
            $primaryValue = $primary->$getter();
            $secondary->$setter($primaryValue);
            $saver = $this->entitySaverFactory->getSaverForEntity($primary);
            $this->expectException(UniqueConstraintViolationException::class);
            $saver->saveAll([$primary, $secondary]);
        }
    }

    /**
     * Check the mapping of our class and the associated entity to make sure it's configured properly on both sides.
     * Very easy to get wrong. This is in addition to the standard Schema Validation
     *
     * @param string                 $classFqn
     * @param array                  $mapping
     * @param EntityManagerInterface $entityManager
     */
    protected function assertCorrectMappings(string $classFqn, array $mapping, EntityManagerInterface $entityManager)
    {
        $pass                                 = false;
        $associationFqn                       = $mapping['targetEntity'];
        $associationMeta                      = $entityManager->getClassMetadata($associationFqn);
        $classTraits                          = $entityManager->getClassMetadata($classFqn)
                                                              ->getReflectionClass()
                                                              ->getTraits();
        $unidirectionalTraitShortNamePrefixes = [
            'Has' . $associationFqn::getSingular() . RelationsGenerator::PREFIX_UNIDIRECTIONAL,
            'Has' . $associationFqn::getPlural() . RelationsGenerator::PREFIX_UNIDIRECTIONAL,
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
                $pass = self::assertCorrectMapping($mapping, $associationMapping, $classFqn);
                break;
            }
        }
        self::assertTrue($pass, 'Failed finding association mapping to test for ' . "\n" . $mapping['targetEntity']);
    }

    /**
     * @param array  $mapping
     * @param array  $associationMapping
     * @param string $classFqn
     *
     * @return bool
     */
    protected function assertCorrectMapping(array $mapping, array $associationMapping, string $classFqn): bool
    {
        if (empty($mapping['joinTable'])) {
            self::assertArrayNotHasKey(
                'joinTable',
                $associationMapping,
                $classFqn . ' join table is empty,
                        but association ' . $mapping['targetEntity'] . ' join table is not empty'
            );

            return true;
        }
        self::assertNotEmpty(
            $associationMapping['joinTable'],
            "$classFqn joinTable is set to " . $mapping['joinTable']['name']
            . " \n association " . $mapping['targetEntity'] . ' join table is empty'
        );
        self::assertSame(
            $mapping['joinTable']['name'],
            $associationMapping['joinTable']['name'],
            "join tables not the same: \n * $classFqn = " . $mapping['joinTable']['name']
            . " \n * association " . $mapping['targetEntity']
            . ' = ' . $associationMapping['joinTable']['name']
        );
        self::assertArrayHasKey(
            'inverseJoinColumns',
            $associationMapping['joinTable'],
            "join table join columns not the same: \n * $classFqn joinColumn = "
            . $mapping['joinTable']['joinColumns'][0]['name']
            . " \n * association " . $mapping['targetEntity']
            . ' inverseJoinColumn is not set'
        );
        self::assertSame(
            $mapping['joinTable']['joinColumns'][0]['name'],
            $associationMapping['joinTable']['inverseJoinColumns'][0]['name'],
            "join table join columns not the same: \n * $classFqn joinColumn = "
            . $mapping['joinTable']['joinColumns'][0]['name']
            . " \n * association " . $mapping['targetEntity']
            . ' inverseJoinColumn = ' . $associationMapping['joinTable']['inverseJoinColumns'][0]['name']
        );
        self::assertSame(
            $mapping['joinTable']['inverseJoinColumns'][0]['name'],
            $associationMapping['joinTable']['joinColumns'][0]['name'],
            "join table join columns  not the same: \n * $classFqn inverseJoinColumn = "
            . $mapping['joinTable']['inverseJoinColumns'][0]['name']
            . " \n * association " . $mapping['targetEntity'] . ' joinColumn = '
            . $associationMapping['joinTable']['joinColumns'][0]['name']
        );

        return true;
    }


    protected function validateEntity(EntityInterface $entity): void
    {
        $entity->validate();
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
     * Get a \ReflectionClass for the currently tested Entity
     *
     * @return \ts\Reflection\ReflectionClass
     * @throws \ReflectionException
     */
    protected function getTestedEntityReflectionClass(): \ts\Reflection\ReflectionClass
    {
        if (null === $this->testedEntityReflectionClass) {
            $this->testedEntityReflectionClass = new \ts\Reflection\ReflectionClass(
                $this->getTestedEntityFqn()
            );
        }

        return $this->testedEntityReflectionClass;
    }
}
