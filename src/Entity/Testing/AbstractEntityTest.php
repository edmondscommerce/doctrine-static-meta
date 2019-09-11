<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Utility\PersisterHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\RepositoryFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelperFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use ErrorException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionMethod;
use function stripos;
use function substr;

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
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class AbstractEntityTest extends TestCase implements EntityTestInterface
{
    /**
     * @var ContainerInterface
     */
    protected static $container;
    /**
     * The fully qualified name of the Entity being tested, as calculated by the test class name
     *
     * @var string
     */
    protected static $testedEntityFqn;

    /**
     * @var TestEntityGenerator
     */
    protected static $testEntityGenerator;

    /**
     * @var TestEntityGeneratorFactory
     */
    protected static $testEntityGeneratorFactory;

    /**
     * @var array
     */
    protected static $schemaErrors = [];

    public static function setUpBeforeClass(): void
    {
        if (null !== static::$container) {
            self::tearDownAfterClass();
        }
        static::initContainer();
        static::$testedEntityFqn            = substr(static::class, 0, -4);
        static::$testEntityGeneratorFactory = static::$container->get(TestEntityGeneratorFactory::class);
        static::$testEntityGenerator        =
            static::$testEntityGeneratorFactory->createForEntityFqn(static::$testedEntityFqn);
    }

    public static function tearDownAfterClass() :void
    {
        $entityManager = static::$container->get(EntityManagerInterface::class);
        $entityManager->close();
        $entityManager->getConnection()->close();
        self::$container   = null;
        static::$container = null;
    }

    public static function initContainer(): void
    {
        $testConfig        = self::getTestContainerConfig();
        static::$container = TestContainerFactory::getContainer($testConfig);
    }

    /**
     * @throws ConfigException
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getTestContainerConfig(): array
    {
        SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
        $testConfig = $_SERVER;
        if (preg_match('%_test$%m', $_SERVER[ConfigInterface::PARAM_DB_NAME]) !== 1) {
            $testConfig[ConfigInterface::PARAM_DB_NAME] = $_SERVER[ConfigInterface::PARAM_DB_NAME] . '_test';
        }

        return $testConfig;
    }

    /**
     * This test checks that the fixtures can be loaded properly
     *
     * The test returns the array of fixtures which means you could choose to add a test that `depends` on this test
     *
     * @test
     */
    public function theFixtureCanBeLoaded(): array
    {
        /**
         * @var FixturesHelper $fixtureHelper
         */
        $fixtureHelper = $this->getFixturesHelper();
        /**
         * This can seriously hurt performance, but is needed as a default
         */
        $fixtureHelper->setLoadFromCache(false);
        /**
         * @var AbstractEntityFixtureLoader $fixture
         */
        $fixture = $fixtureHelper->createFixtureInstanceForEntityFqn(static::$testedEntityFqn);
        $fixtureHelper->createDb($fixture);
        $loaded               = $this->loadAllEntities();
        $expectedAmountLoaded = $fixture::BULK_AMOUNT_TO_GENERATE;
        $actualAmountLoaded   = count($loaded);
        self::assertGreaterThanOrEqual(
            $expectedAmountLoaded,
            $actualAmountLoaded,
            "expected to load at least $expectedAmountLoaded but only loaded $actualAmountLoaded"
        );

        return $loaded;
    }

    /**
     * Test that we have correctly generated an instance of our test entity
     *
     * @param array $fixtureEntities
     *
     * @return EntityInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @depends theFixtureCanBeLoaded
     * @test
     */
    public function weCanGenerateANewEntityInstance(array $fixtureEntities): EntityInterface
    {
        $generated = current($fixtureEntities);
        self::assertInstanceOf(static::$testedEntityFqn, $generated);

        return $generated;
    }

    /**
     * @test
     * Use Doctrine's built in schema validation tool to catch issues
     */
    public function theSchemaIsValidForThisEntity()
    {
        $errors  = $this->getSchemaErrors();
        $message = '';
        if (isset($errors[static::$testedEntityFqn])) {
            $message = "Failed ORM Validate Schema:\n";
            foreach ($errors[static::$testedEntityFqn] as $err) {
                $message .= "\n * $err \n";
            }
        }
        self::assertEmpty($message, $message);
    }

    /**
     * Loop through Entity fields, call the getter and where possible assert there is a value returned
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     * @throws QueryException
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @test
     * @depends weCanGenerateANewEntityInstance
     */
    public function theEntityGettersReturnValues(EntityInterface $entity): EntityInterface
    {
        $meta = $this->getEntityManager()->getClassMetadata(static::$testedEntityFqn);
        $dto  = $this->getDtoFactory()->createDtoFromEntity($entity);
        foreach ($meta->getFieldNames() as $fieldName) {
            if ('id' === $fieldName) {
                continue;
            }
            $type   = PersisterHelper::getTypeOfField($fieldName, $meta, $this->getEntityManager())[0];
            $method = $this->getGetterNameForField($fieldName, $type);
            if (\ts\stringContains($method, '.')) {
                [$getEmbeddableMethod,] = explode('.', $method);
                $embeddable = $entity->$getEmbeddableMethod();
                self::assertInstanceOf(AbstractEmbeddableObject::class, $embeddable);
                continue;
            }
            $reflectionMethod = new ReflectionMethod($entity, $method);
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
                self::assertNotNull($dto->$method(), "$fieldName getter returned null");
                continue;
            }
            // If there is no return type then we can't assert anything,
            // but again we can just call the getter to check for errors
            $dto->$method();
        }
        if (0 === $this->getCount()) {
            self::assertTrue(true);
        }

        return $entity;
    }

    /**
     * @param EntityInterface $generated
     *
     * @return EntityInterface
     * @test
     * @depends theEntityGettersReturnValues
     * @throws ErrorException
     */
    public function weCanExtendTheEntityWithUnrequiredAssociationEntities(EntityInterface $generated): EntityInterface
    {
        if ([] === $this->getTestedEntityClassMetaData()->getAssociationMappings()) {
            $this->markTestSkipped('No associations to test');
        }
        $this->getTestEntityGenerator()->addAssociationEntities($generated);
        $this->assertAllAssociationsAreNotEmpty($generated);

        return $generated;
    }

    /**
     * @param EntityInterface $generated
     *
     * @return EntityInterface
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     * @test
     * @depends weCanExtendTheEntityWithUnrequiredAssociationEntities
     */
    public function theEntityCanBeSavedAndReloadedFromTheDatabase(EntityInterface $generated): EntityInterface
    {
        $this->getEntitySaver()->save($generated);
        $loaded = $this->loadEntity($generated->getId());
        self::assertSame((string)$generated->getId(), (string)$loaded->getId());
        self::assertInstanceOf(static::$testedEntityFqn, $loaded);

        return $loaded;
    }

    /**
     * @param EntityInterface $loaded
     *
     * @return EntityInterface
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     * @throws ValidationException
     * @depends theEntityCanBeSavedAndReloadedFromTheDatabase
     * @test
     */
    public function theLoadedEntityCanBeUpdatedAndResaved(EntityInterface $loaded): EntityInterface
    {
        $this->updateEntityFields($loaded);
        $this->assertAllAssociationsAreNotEmpty($loaded);
        $this->removeAllAssociations($loaded);
        $this->getEntitySaver()->save($loaded);

        return $loaded;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     * @depends theLoadedEntityCanBeUpdatedAndResaved
     * @test
     */
    public function theReloadedEntityHasNoAssociatedEntities(EntityInterface $entity): EntityInterface
    {
        $reLoaded     = $this->loadEntity($entity->getId());
        $entityDump   = $this->dump($entity);
        $reLoadedDump = $this->dump($reLoaded);
        self::assertEquals($entityDump, $reLoadedDump);
        $this->assertAllAssociationsAreEmpty($reLoaded);

        return $reLoaded;
    }

    /**
     * @depends weCanGenerateANewEntityInstance
     *
     * @param EntityInterface $entity
     *
     * @throws ReflectionException
     * @test
     */
    public function checkAllGettersCanBeReturnedFromDoctrineStaticMeta(EntityInterface $entity)
    {
        $getters = $entity::getDoctrineStaticMeta()->getGetters();
        self::assertNotEmpty($getters);
        foreach ($getters as $getter) {
            self::assertRegExp('%^(get|is|has).+%', $getter);
        }
    }

    /**
     * @depends weCanGenerateANewEntityInstance
     * @test
     *
     * @param EntityInterface $entity
     *
     * @throws ReflectionException
     */
    public function checkAllSettersCanBeReturnedFromDoctrineStaticMeta(EntityInterface $entity)
    {
        $setters = $entity::getDoctrineStaticMeta()->getSetters();
        self::assertNotEmpty($setters);
        foreach ($setters as $setter) {
            self::assertRegExp('%^(set|add).+%', $setter);
        }
    }

    /**
     * Loop through entity fields and find unique ones
     *
     * Then ensure that the unique rule is being enforced as expected
     *
     * @throws ReflectionException
     * @throws ValidationException
     * @test
     * @depends theReloadedEntityHasNoAssociatedEntities
     */
    public function checkThatWeCanNotSaveEntitiesWithDuplicateUniqueFieldValues(): void
    {
        $meta         = $this->getTestedEntityClassMetaData();
        $uniqueFields = [];
        foreach ($meta->getFieldNames() as $fieldName) {
            if (IdFieldInterface::PROP_ID === $fieldName) {
                continue;
            }
            if (true === $this->isUniqueField($fieldName)) {
                $uniqueFields[] = $fieldName;
            }
        }
        if ([] === $uniqueFields) {
            self::markTestSkipped('No unique fields to check');

            return;
        }
        foreach ($uniqueFields as $fieldName) {
            $primary      = $this->getTestEntityGenerator()->generateEntity();
            $secondary    = $this->getTestEntityGenerator()->generateEntity();
            $secondaryDto = $this->getDtoFactory()->createDtoFromEntity($secondary);
            $getter       = 'get' . $fieldName;
            $setter       = 'set' . $fieldName;
            $primaryValue = $primary->$getter();
            $secondaryDto->$setter($primaryValue);
            $secondary->update($secondaryDto);
            $saver = $this->getEntitySaver();
            $this->expectException(UniqueConstraintViolationException::class);
            $saver->saveAll([$primary, $secondary]);
        }
    }

    protected function getFixturesHelper(): FixturesHelper
    {
        return static::$container->get(FixturesHelperFactory::class)->getFixturesHelper();
    }

    protected function loadAllEntities(): array
    {
        return static::$container->get(RepositoryFactory::class)
                                 ->getRepository(static::$testedEntityFqn)
                                 ->findAll();
    }

    /**
     * Use Doctrine's standard schema validation to get errors for the whole schema
     *
     * We cache this as a class property because the schema only needs validating once as a whole, after that we can
     * pull out Entity specific issues as required
     *
     * @param bool $update
     *
     * @return array
     * @throws Exception
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function getSchemaErrors(bool $update = false): array
    {
        if ([] === static::$schemaErrors || true === $update) {
            $validator            = new SchemaValidator($this->getEntityManager());
            static::$schemaErrors = $validator->validateMapping();
        }

        return static::$schemaErrors;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return static::$container->get(EntityManagerInterface::class);
    }

    protected function getDtoFactory(): DtoFactory
    {
        return static::$container->get(DtoFactory::class);
    }

    protected function getGetterNameForField(string $fieldName, string $type): string
    {
        if ($type === 'boolean') {
            return static::$container->get(CodeHelper::class)->getGetterMethodNameForBoolean($fieldName);
        }

        return 'get' . $fieldName;
    }

    protected function getTestedEntityClassMetaData(): ClassMetadata
    {
        return $this->getEntityManager()->getClassMetadata(static::$testedEntityFqn);
    }

    protected function getTestEntityGenerator(): TestEntityGenerator
    {
        return static::$testEntityGenerator;
    }

    protected function assertAllAssociationsAreNotEmpty(EntityInterface $entity)
    {
        $meta = $this->getTestedEntityClassMetaData();
        foreach ($meta->getAssociationMappings() as $mapping) {
            $getter = 'get' . $mapping['fieldName'];
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $collection = $entity->$getter()->toArray();
                $this->assertCorrectMappings(static::$testedEntityFqn, $mapping);
                self::assertNotEmpty(
                    $collection,
                    'Failed to load the collection of the associated entity [' . $mapping['fieldName']
                    . '] from the generated ' . static::$testedEntityFqn
                    . ', make sure you have reciprocal adding of the association'
                );

                continue;
            }
            $association = $entity->$getter();
            self::assertNotEmpty(
                $association,
                'Failed to load the associated entity: [' . $mapping['fieldName']
                . '] from the generated ' . static::$testedEntityFqn
            );
            self::assertNotEmpty(
                $association->getId(),
                'Failed to get the ID of the associated entity: [' . $mapping['fieldName']
                . '] from the generated ' . static::$testedEntityFqn
            );
        }
    }

    /**
     * Check the mapping of our class and the associated entity to make sure it's configured properly on both sides.
     * Very easy to get wrong. This is in addition to the standard Schema Validation
     *
     * @param string $classFqn
     * @param array  $mapping
     */
    protected function assertCorrectMappings(string $classFqn, array $mapping)
    {
        $entityManager                        = $this->getEntityManager();
        $pass                                 = false;
        $associationFqn                       = $mapping['targetEntity'];
        $associationMeta                      = $entityManager->getClassMetadata($associationFqn);
        $classTraits                          = $entityManager->getClassMetadata($classFqn)
                                                              ->getReflectionClass()
                                                              ->getTraits();
        $unidirectionalTraitShortNamePrefixes = [
            'Has' . $associationFqn::getDoctrineStaticMeta()->getSingular() . RelationsGenerator::PREFIX_UNIDIRECTIONAL,
            'Has' . $associationFqn::getDoctrineStaticMeta()->getPlural() . RelationsGenerator::PREFIX_UNIDIRECTIONAL,
            'Has' . RelationsGenerator::PREFIX_REQUIRED .
            $associationFqn::getDoctrineStaticMeta()->getSingular() . RelationsGenerator::PREFIX_UNIDIRECTIONAL,
            'Has' . RelationsGenerator::PREFIX_REQUIRED .
            $associationFqn::getDoctrineStaticMeta()->getPlural() . RelationsGenerator::PREFIX_UNIDIRECTIONAL,
        ];
        foreach ($classTraits as $trait) {
            foreach ($unidirectionalTraitShortNamePrefixes as $namePrefix) {
                if (0 === stripos($trait->getShortName(), $namePrefix)) {
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
        self::assertTrue(
            $pass,
            'Failed finding association mapping to test for ' . "\n" . $mapping['targetEntity']
        );
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

    protected function getEntitySaver(): EntitySaverInterface
    {
        return static::$container->get(EntitySaverFactory::class)->getSaverForEntityFqn(static::$testedEntityFqn);
    }

    /**
     * @param mixed $id
     *
     * @return EntityInterface|null
     */
    protected function loadEntity($id): EntityInterface
    {
        return static::$container->get(RepositoryFactory::class)
                                 ->getRepository(static::$testedEntityFqn)
                                 ->get($id);
    }

    /**
     * Generate a new entity and then update our Entity with the values from the generated one
     *
     * @param EntityInterface $entity
     *
     * @throws ValidationException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function updateEntityFields(EntityInterface $entity): void
    {
        $dto = $this->getDtoFactory()->createDtoFromEntity($entity);
        $this->getTestEntityGenerator()->fakerUpdateDto($dto);
        $entity->update($dto);
    }

    /**
     * @param EntityInterface $entity
     *
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function removeAllAssociations(EntityInterface $entity)
    {
        $required    = $entity::getDoctrineStaticMeta()->getRequiredRelationProperties();
        $meta        = $this->getTestedEntityClassMetaData();
        $identifiers = array_flip($meta->getIdentifier());
        foreach ($meta->getAssociationMappings() as $mapping) {
            if (isset($identifiers[$mapping['fieldName']])) {
                continue;
            }
            if (isset($required[$mapping['fieldName']])) {
                continue;
            }
            $remover = 'remove' . MappingHelper::singularize($mapping['fieldName']);
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $getter    = 'get' . $mapping['fieldName'];
                $relations = $entity->$getter();
                foreach ($relations as $relation) {
                    $this->initialiseEntity($relation);
                    $entity->$remover($relation);
                }
                continue;
            }
            $entity->$remover();
        }
        $this->assertAllAssociationsAreEmpty($entity);
    }

    protected function initialiseEntity(EntityInterface $entity): void
    {
        static::$testEntityGeneratorFactory
            ->createForEntityFqn($entity::getEntityFqn())
            ->getEntityFactory()
            ->initialiseEntity($entity);
    }

    protected function assertAllAssociationsAreEmpty(EntityInterface $entity)
    {
        $required    = $entity::getDoctrineStaticMeta()->getRequiredRelationProperties();
        $meta        = $this->getTestedEntityClassMetaData();
        $identifiers = array_flip($meta->getIdentifier());
        foreach ($meta->getAssociationMappings() as $mapping) {
            if (isset($identifiers[$mapping['fieldName']])) {
                continue;
            }
            if (isset($required[$mapping['fieldName']])) {
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
                . '] from the generated ' . static::$testedEntityFqn
            );
        }
    }

    protected function dump(EntityInterface $entity): string
    {
        return (new EntityDebugDumper())->dump($entity, $this->getEntityManager());
    }

    protected function isUniqueField(string $fieldName): bool
    {
        $fieldMapping = $this->getTestedEntityClassMetaData()->getFieldMapping($fieldName);

        return array_key_exists('unique', $fieldMapping) && true === $fieldMapping['unique'];
    }
}
