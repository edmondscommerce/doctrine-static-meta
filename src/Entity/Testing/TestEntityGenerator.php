<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use Faker;
use Faker\ORM\Doctrine\Populator;

/**
 * Class TestEntityGenerator
 *
 * This class handles utilising Faker to build up an Entity and then also possible build associated entities and handle
 * the association
 *
 * Unique columns are guaranteed to have a totally unique value in this particular process, but not between processes
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Testing
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestEntityGenerator
{
    /**
     * @var Faker\Generator
     */
    protected $generator;


    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * These two are used to keep track of unique fields and ensure we dont accidently make apply none unique values
     *
     * @var array
     */
    private static $uniqueStrings = [];
    /**
     * @var int
     */
    private static $uniqueInt;


    /**
     * An array of fieldNames to class names that are to be instantiated as column formatters as required
     *
     * @var array
     */
    protected $fakerDataProviderClasses;

    /**
     * A cache of instantiated column data providers
     *
     * @var array
     */
    protected $fakerDataProviderObjects = [];

    /**
     * Reflection of the tested entity
     *
     * @var \ReflectionClass
     */
    protected $testedEntityReflectionClass;
    /**
     * @var EntitySaverFactory
     */
    protected $entitySaverFactory;

    /**
     * TestEntityGenerator constructor.
     *
     * @param EntityManager      $entityManager
     * @param float|null         $seed
     * @param array              $fakerDataProviderClasses
     * @param \ReflectionClass   $testedEntityReflectionClass
     * @param EntitySaverFactory $entitySaverFactory
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(
        EntityManager $entityManager,
        ?float $seed,
        array $fakerDataProviderClasses,
        \ReflectionClass $testedEntityReflectionClass,
        EntitySaverFactory $entitySaverFactory
    ) {
        $this->entityManager = $entityManager;
        $this->generator     = Faker\Factory::create();
        if (null !== $seed) {
            $this->generator->seed($seed);
        }
        $this->fakerDataProviderClasses    = $fakerDataProviderClasses;
        $this->testedEntityReflectionClass = $testedEntityReflectionClass;
        $this->entitySaverFactory          = $entitySaverFactory;
    }

    /**
     * @param string $class
     *
     * @return EntityInterface
     * @throws ConfigException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function generateEntity(string $class): EntityInterface
    {
        $customColumnFormatters = $this->generateColumnFormatters($this->entityManager, $class);
        $populator              = new Populator($this->generator, $this->entityManager);
        $populator->addEntity($class, 1, $customColumnFormatters);

        return $populator->execute(null, false)[$class][0];
    }

    /**
     * @param EntityManager   $entityManager
     * @param EntityInterface $generated
     *
     * @throws ConfigException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @throws \ErrorException
     */
    public function addAssociationEntities(
        EntityManager $entityManager,
        EntityInterface $generated
    ): void {
        $class    = $this->testedEntityReflectionClass->getName();
        $meta     = $entityManager->getClassMetadata($class);
        $mappings = $meta->getAssociationMappings();
        if (empty($mappings)) {
            return;
        }
        $namespaceHelper = new NamespaceHelper();
        $methods         = array_map('strtolower', get_class_methods($generated));
        foreach ($mappings as $mapping) {
            $mappingEntityClass = $mapping['targetEntity'];
            $mappingEntity      = $this->generateEntity($mappingEntityClass);
            $errorMessage       = "Error adding association entity $mappingEntityClass to $class: %s";
            $this->entitySaverFactory->getSaverForEntity($mappingEntity)->save($mappingEntity);
            $mappingEntityPluralInterface = $namespaceHelper->getHasPluralInterfaceFqnForEntity($mappingEntityClass);
            if ($this->testedEntityReflectionClass->implementsInterface($mappingEntityPluralInterface)) {
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
            $this->assertInArray(
                strtolower($method),
                $methods,
                sprintf($errorMessage, $method.' method is not defined')
            );
            $generated->$method($mappingEntity);
        }
    }

    /**
     * Stub of PHPUnit Assertion method
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $error
     *
     * @throws \ErrorException
     */
    protected function assertEquals($expected, $actual, string $error): void
    {
        if ($expected !== $actual) {
            throw new \ErrorException($error);
        }
    }

    /**
     * Stub of PHPUnit Assertion method
     *
     * @param mixed  $needle
     * @param array  $haystack
     * @param string $error
     *
     * @throws \ErrorException
     */
    protected function assertInArray($needle, array $haystack, string $error): void
    {
        if (false === \in_array($needle, $haystack, true)) {
            throw new \ErrorException($error);
        }
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $class
     *
     * @return array
     * @throws \Doctrine\ORM\Mapping\MappingException
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
        $fieldNames = $meta->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            if (!isset($columnFormatters[$fieldName])) {
                if (true === $this->setFakerDataProvider($columnFormatters, $fieldName)) {
                    continue;
                }
            }
            $fieldMapping = $meta->getFieldMapping($fieldName);
            if (true === ($fieldMapping['unique'] ?? false)) {
                switch ($fieldMapping['type']) {
                    case 'string':
                        $columnFormatters[$fieldName] = $this->getUniqueString();
                        break;
                    case 'integer':
                        $columnFormatters[$fieldName] = $this->getUniqueInt();
                        break;
                    default:
                        throw new \InvalidArgumentException('unique field has an unsupported type: '
                                                            .print_r($fieldMapping, true));
                }
            }
        }

        return $columnFormatters;
    }

    protected function getUniqueString(): string
    {
        $string = 'unique string: '.$this->getUniqueInt().md5((string)time());
        while (isset(self::$uniqueStrings[$string])) {
            $string                       = md5((string)time());
            self::$uniqueStrings[$string] = true;
        }

        return $string;
    }

    protected function getUniqueInt(): int
    {
        return ++self::$uniqueInt;
    }

    /**
     * Add a faker data provider to the columnFormatters array (by reference) if there is one available
     *
     * Handles instantiating and caching of the data providers
     *
     * @param array  $columnFormatters
     * @param string $fieldName
     *
     * @return bool
     */
    protected function setFakerDataProvider(array &$columnFormatters, string $fieldName): bool
    {
        if (!isset($this->fakerDataProviderClasses[$fieldName])) {
            return false;
        }
        if (!isset($this->fakerDataProviderObjects[$fieldName])) {
            $class                                      = $this->fakerDataProviderClasses[$fieldName];
            $this->fakerDataProviderObjects[$fieldName] = new $class($this->generator);
        }
        $columnFormatters[$fieldName] = $this->fakerDataProviderObjects[$fieldName];

        return true;
    }
}
