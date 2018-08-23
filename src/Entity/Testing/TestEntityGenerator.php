<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\PersistentCollection;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use Faker;

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
    protected static $generator;
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
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * An array of fieldNames to class names that are to be instantiated as column formatters as required
     *
     * @var array|string[]
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
     * @var \ts\Reflection\ReflectionClass
     */
    protected $testedEntityReflectionClass;
    /**
     * @var EntitySaverFactory
     */
    protected $entitySaverFactory;
    /**
     * @var EntityValidatorFactory
     */
    protected $entityValidatorFactory;

    /**
     * TestEntityGenerator constructor.
     *
     * @param float|null                     $seed
     * @param array|string[]                 $fakerDataProviderClasses
     * @param \ts\Reflection\ReflectionClass $testedEntityReflectionClass
     * @param EntitySaverFactory             $entitySaverFactory
     * @param EntityValidatorFactory         $entityValidatorFactory
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(
        ?float $seed,
        array $fakerDataProviderClasses,
        \ts\Reflection\ReflectionClass $testedEntityReflectionClass,
        EntitySaverFactory $entitySaverFactory,
        EntityValidatorFactory $entityValidatorFactory
    ) {
        $this->initFakerGenerator($seed);
        $this->fakerDataProviderClasses    = $fakerDataProviderClasses;
        $this->testedEntityReflectionClass = $testedEntityReflectionClass;
        $this->entitySaverFactory          = $entitySaverFactory;
        $this->entityValidatorFactory      = $entityValidatorFactory;
    }

    /**
     * @param float|null $seed
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function initFakerGenerator(?float $seed): void
    {
        if (null === self::$generator) {
            self::$generator = Faker\Factory::create();
            if (null !== $seed) {
                self::$generator->seed($seed);
            }
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param EntityInterface        $generated
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function addAssociationEntities(
        EntityManagerInterface $entityManager,
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
            if (\interface_exists($mappingEntityPluralInterface)
                && $this->testedEntityReflectionClass->implementsInterface($mappingEntityPluralInterface)
            ) {
                $this->assertSame(
                    $mappingEntityClass::getPlural(),
                    $mapping['fieldName'],
                    sprintf($errorMessage, ' mapping should be plural')
                );
                $getter = 'get' . $mappingEntityClass::getPlural();
                $method = 'add' . $mappingEntityClass::getSingular();
            } else {
                $this->assertSame(
                    $mappingEntityClass::getSingular(),
                    $mapping['fieldName'],
                    sprintf($errorMessage, ' mapping should be singular')
                );
                $getter = 'get' . $mappingEntityClass::getSingular();
                $method = 'set' . $mappingEntityClass::getSingular();
            }
            $this->assertInArray(
                strtolower($method),
                $methods,
                sprintf($errorMessage, $method . ' method is not defined')
            );
            $currentlySet = $generated->$getter();
            switch (true) {
                case $currentlySet === null:
                case $currentlySet === []:
                case $currentlySet instanceof PersistentCollection:
                    $generated->$method($mappingEntity);
                    break;
            }
        }
    }

    /**
     * Generate an Entity. Optionally provide an offset from the first entity
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $class
     *
     * @param int                    $offset
     *
     * @return EntityInterface
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function generateEntity(
        EntityManagerInterface $entityManager,
        string $class,
        int $offset = 0
    ): EntityInterface {

        $result = $this->generateEntities($entityManager, $class, 1, $offset);

        return current($result);
    }

    /**
     * Generate Entities.
     *
     * Optionally discard the first generated entities up to the value of offset
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityFqn
     * @param int                    $num
     *
     * @param int                    $offset
     *
     * @return array|EntityInterface[]
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function generateEntities(
        EntityManagerInterface $entityManager,
        string $entityFqn,
        int $num,
        int $offset = 0
    ): array {
        $this->entityManager = $entityManager;
        $columnFormatters    = $this->generateColumnFormatters($entityManager, $entityFqn);
        $meta                = $entityManager->getClassMetadata($entityFqn);
        $entities            = [];
        for ($i = 0; $i < ($num + $offset); $i++) {
            $entity = new $entityFqn($this->entityValidatorFactory);
            $this->fillColumns($entity, $columnFormatters, $meta);
            if ($i < $offset) {
                continue;
            }
            $entities[] = $entity;
        }
        $this->entitySaverFactory->getSaverForEntityFqn($entityFqn)->saveAll($entities);

        return $entities;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityFqn
     *
     * @return array
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function generateColumnFormatters(EntityManagerInterface $entityManager, string $entityFqn): array
    {
        $meta              = $entityManager->getClassMetadata($entityFqn);
        $guessedFormatters = (new Faker\ORM\Doctrine\EntityPopulator($meta))->guessColumnFormatters(self::$generator);
        $customFormatters  = [];
        $mappings          = $meta->getAssociationMappings();
        $this->initialiseColumnFormatters($meta, $mappings, $guessedFormatters);
        $fieldNames = $meta->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            if (isset($customFormatters[$fieldName])) {
                continue;
            }
            if (true === $this->addFakerDataProviderToColumnFormatters($customFormatters, $fieldName, $entityFqn)) {
                continue;
            }
            $fieldMapping = $meta->getFieldMapping($fieldName);
            if (true === ($fieldMapping['unique'] ?? false)) {
                $this->addUniqueColumnFormatter($fieldMapping, $customFormatters, $fieldName);
                continue;
            }
        }

        return array_merge($guessedFormatters, $customFormatters);
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
    ): void {
        foreach ($mappings as $mapping) {
            if ($meta->isCollectionValuedAssociation($mapping['fieldName'])) {
                $columnFormatters[$mapping['fieldName']] = new ArrayCollection();
                continue;
            }

            if (isset($mapping['joinColumns']) && count($mapping['joinColumns']) === 1
                && ($mapping['joinColumns'][0]['nullable'] ?? null) === false
            ) {
                $columnFormatters[$mapping['fieldName']] = function () use ($mapping) {
                    $entity = $this->generateEntity($this->entityManager, $mapping['targetEntity']);
                    $this->entitySaverFactory->getSaverForEntity($entity)->save($entity);

                    return $entity;
                };
                continue;
            }
            $columnFormatters[$mapping['fieldName']] = null;
        }
    }

    /**
     * Add a faker data provider to the columnFormatters array (by reference) if there is one available
     *
     * Handles instantiating and caching of the data providers
     *
     * @param array  $columnFormatters
     * @param string $fieldName
     *
     * @param string $entityFqn
     *
     * @return bool
     */
    protected function addFakerDataProviderToColumnFormatters(
        array &$columnFormatters,
        string $fieldName,
        string $entityFqn
    ): bool {
        foreach ([
                     $entityFqn . '-' . $fieldName,
                     $fieldName,
                 ] as $key) {
            if (!isset($this->fakerDataProviderClasses[$key])) {
                continue;
            }
            if (!isset($this->fakerDataProviderObjects[$key])) {
                $class                                = $this->fakerDataProviderClasses[$key];
                $this->fakerDataProviderObjects[$key] = new $class(self::$generator);
            }
            $columnFormatters[$fieldName] = $this->fakerDataProviderObjects[$key];

            return true;
        }
        return false;
    }

    protected function addUniqueColumnFormatter(array &$fieldMapping, array &$columnFormatters, string $fieldName): void
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
                                                    . print_r($fieldMapping, true));
        }
    }

    protected function getUniqueString(): string
    {
        $string = 'unique string: ' . $this->getUniqueInt() . md5((string)time());
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

    protected function fillColumns(EntityInterface $entity, array &$columnFormatters, ClassMetadata $meta): void
    {
        foreach ($columnFormatters as $field => $formatter) {
            if (null !== $formatter) {
                try {
                    $value = \is_callable($formatter) ? $formatter($entity) : $formatter;
                } catch (\InvalidArgumentException $ex) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Failed to generate a value for %s::%s: %s',
                            \get_class($entity),
                            $field,
                            $ex->getMessage()
                        )
                    );
                }
                $meta->reflFields[$field]->setValue($entity, $value);
            }
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
}
