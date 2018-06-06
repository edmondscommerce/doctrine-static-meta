<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Utility\PersisterHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
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
     * @var \ReflectionClass
     */
    protected $testedEntityReflectionClass;


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
        $this->entityValidator     = (
        new EntityValidatorFactory(new DoctrineCache(new ArrayCache()))
        )->getEntityValidator();
        $this->entitySaverFactory  = new EntitySaverFactory(
            $this->entityManager,
            new EntitySaver($this->entityManager),
            new NamespaceHelper()
        );
        $this->testEntityGenerator = new TestEntityGenerator(
            static::SEED,
            static::FAKER_DATA_PROVIDERS,
            $this->getTestedEntityReflectionClass(),
            $this->entitySaverFactory
        );
        $this->codeHelper          = new CodeHelper(new NamespaceHelper());
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
     * @throws ConfigException
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Exception
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testGeneratedCreate()
    {
        $entityManager = $this->getEntityManager();
        $class         = $this->getTestedEntityFqn();
        $generated     = $this->testEntityGenerator->generateEntity($entityManager, $class);
        $this->assertInstanceOf($class, $generated);
        $saver = $this->entitySaverFactory->getSaverForEntity($generated);
        $this->testEntityGenerator->addAssociationEntities($entityManager, $generated);
        $this->validateEntity($generated);
        $meta = $entityManager->getClassMetadata($class);
        foreach ($meta->getFieldNames() as $fieldName) {
            $type             = PersisterHelper::getTypeOfField($fieldName, $meta, $entityManager);
            $method           = $this->getGetterNameForField($fieldName, $type[0]);
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
                $this->assertCorrectMappings($class, $mapping, $entityManager);
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
        $this->assertUniqueFieldsMustBeUnique($meta);
    }


    protected function getGetterNameForField(string $fieldName, string $type): string
    {
        if ($type === 'boolean') {
            return $this->codeHelper->getGetterMethodNameForBoolean($fieldName);
        }

        return 'get'.$fieldName;
    }

    /**
     * Loop through entity fields and find unique ones
     *
     * Then ensure that the unique rule is being enforced as expected
     *
     * @param ClassMetadataInfo $meta
     *
     * @throws ConfigException
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function assertUniqueFieldsMustBeUnique(ClassMetadataInfo $meta): void
    {
        $uniqueFields = [];
        foreach ($meta->getFieldNames() as $fieldName) {
            $fieldMapping = $meta->getFieldMapping($fieldName);
            if (array_key_exists('unique', $fieldMapping) && true === $fieldMapping['unique']) {
                $uniqueFields[$fieldName] = $fieldMapping;
            }
        }
        if ([] === $uniqueFields) {
            return;
        }
        $class         = $this->getTestedEntityFqn();
        $entityManager = $this->getEntityManager();
        foreach ($uniqueFields as $fieldName => $fieldMapping) {
            $primary      = $this->testEntityGenerator->generateEntity($entityManager, $class);
            $secondary    = $this->testEntityGenerator->generateEntity($entityManager, $class);
            $getter       = 'get'.$fieldName;
            $setter       = 'set'.$fieldName;
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
     * @param string        $classFqn
     * @param array         $mapping
     * @param EntityManager $entityManager
     */
    protected function assertCorrectMappings(string $classFqn, array $mapping, EntityManager $entityManager)
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
                $pass = $this->assertCorrectMapping($mapping, $associationMapping, $classFqn);
                break;
            }
        }
        $this->assertTrue($pass, 'Failed finding association mapping to test for '."\n".$mapping['targetEntity']);
    }

    /**
     * @param array  $mapping
     * @param array  $associationMapping
     * @param string $classFqn
     *
     * @return bool
     */
    protected function assertCorrectMapping(array $mapping, array $associationMapping, string $classFqn)
    {
        if (empty($mapping['joinTable'])) {
            $this->assertArrayNotHasKey(
                'joinTable',
                $associationMapping,
                $classFqn.' join table is empty,
                        but association '.$mapping['targetEntity'].' join table is not empty'
            );

            return true;
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

        return true;
    }


    protected function validateEntity(EntityInterface $entity): void
    {
        $entity->injectValidator($this->entityValidator);
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
