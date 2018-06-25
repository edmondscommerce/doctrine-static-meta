<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
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
     * @param float|null         $seed
     * @param array              $fakerDataProviderClasses
     * @param \ReflectionClass   $testedEntityReflectionClass
     * @param EntitySaverFactory $entitySaverFactory
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(
        ?float $seed,
        array $fakerDataProviderClasses,
        \ReflectionClass $testedEntityReflectionClass,
        EntitySaverFactory $entitySaverFactory
    ) {
        $this->generator = Faker\Factory::create();
        if (null !== $seed) {
            $this->generator->seed($seed);
        }
        $this->fakerDataProviderClasses    = $fakerDataProviderClasses;
        $this->testedEntityReflectionClass = $testedEntityReflectionClass;
        $this->entitySaverFactory          = $entitySaverFactory;
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $class
     *
     * @return EntityInterface
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function generateEntity(EntityManager $entityManager, string $class): EntityInterface
    {

        $result = $this->generateEntities($entityManager, $class, 1);

        return $result[0];
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $class
     * @param int           $num
     *
     * @return array|EntityInterface[]
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function generateEntities(EntityManager $entityManager, string $class, int $num): array
    {
        $customColumnFormatters = $this->generateColumnFormatters($entityManager, $class);
        $populator              = new Populator($this->generator, $entityManager);
        $populator->addEntity($class, $num, $customColumnFormatters);

        $result = $populator->execute($entityManager, false);

        return $result[ltrim($class, '\\')];
    }

    /**
     * @param EntityManager   $entityManager
     * @param EntityInterface $generated
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.ElseExpression)
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
            $mappingEntity      = $this->generateEntity($entityManager, $mappingEntityClass);
            $errorMessage       = "Error adding association entity $mappingEntityClass to $class: %s";
            $this->entitySaverFactory->getSaverForEntity($mappingEntity)->save($mappingEntity);
            $mappingEntityPluralInterface = $namespaceHelper->getHasPluralInterfaceFqnForEntity($mappingEntityClass);
            if ($this->testedEntityReflectionClass->implementsInterface($mappingEntityPluralInterface)) {
                $this->assertSame(
                    $mappingEntityClass::getPlural(),
                    $mapping['fieldName'],
                    sprintf($errorMessage, ' mapping should be plural')
                );
                $method = 'add'.$mappingEntityClass::getSingular();
            } else {
                $this->assertSame(
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
    protected function assertSame($expected, $actual, string $error): void
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
        $this->initialiseColumnFormatters($meta, $mappings, $columnFormatters);
        $fieldNames = $meta->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            if (isset($columnFormatters[$fieldName])) {
                continue;
            }
            if (true === $this->setFakerDataProvider($columnFormatters, $fieldName)) {
                continue;
            }
            $fieldMapping = $meta->getFieldMapping($fieldName);
            if (true === ($fieldMapping['unique'] ?? false)) {
                $this->addUniqueColumnFormatter($fieldMapping, $columnFormatters, $fieldName);
                continue;
            }
        }

        return $columnFormatters;
    }

    protected function addUniqueColumnFormatter(array &$fieldMapping, array &$columnFormatters, string $fieldName)
    {
        switch ($fieldMapping['type']) {
            case 'string':
                $columnFormatters[$fieldName] = $this->getUniqueString();
                break;
            case 'integer':
            case 'bigint':
                $columnFormatters[$fieldName] = $this->getUniqueInt();
                break;
            default:
                throw new \InvalidArgumentException('unique field has an unsupported type: '
                                                    .print_r($fieldMapping, true));
        }
    }

    /**
     * Loop through mappings and initialise empty array collections for colection valued mappings, or null if not
     *
     * @param ClassMetadataInfo $meta
     * @param array             $mappings
     * @param array             $columnFormatters
     */
    protected function initialiseColumnFormatters(
        ClassMetadataInfo $meta,
        array &$mappings,
        array &$columnFormatters
    ) {
        foreach ($mappings as $mapping) {
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $columnFormatters[$mapping['fieldName']] = new ArrayCollection();
                continue;
            }
            $columnFormatters[$mapping['fieldName']] = null;
        }
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
