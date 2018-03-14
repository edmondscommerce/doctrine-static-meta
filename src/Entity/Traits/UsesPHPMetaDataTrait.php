<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetaData;

trait UsesPHPMetaDataTrait
{

    /**
     * @var \ReflectionClass
     */
    private static $reflectionClass;

    /**
     * @var string
     */
    private static $singular;

    /**
     * @var string
     */
    private static $plural;


    public function __construct()
    {
        $this->runInitMethods();
    }

    /**
     * Find and run all init methods
     * - defined in relationship traits and generally to init ArrayCollection properties
     */
    protected function runInitMethods(): void
    {
        $methods = static::$reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $method) {
            if ($method instanceof \ReflectionMethod) {
                $method = $method->getName();
            }
            if (0 === strpos($method, UsesPHPMetaDataInterface::METHOD_PREFIX_INIT)) {
                $this->$method();
            }
        }
    }

    /**
     * Loads the class and property meta data in the class
     *
     * This is the method called by Doctrine to load the meta data
     *
     * @param DoctrineClassMetaData $metadata
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(DoctrineClassMetaData $metadata): void
    {
        try {
            $builder                 = new ClassMetadataBuilder($metadata);
            static::$reflectionClass = $metadata->getReflectionClass();
            static::loadPropertyDoctrineMetaData($builder);
            static::loadClassDoctrineMetaData($builder);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * This method will reflect on the entity class and pull out all the methods that begin with
     * UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META
     *
     * Once it has an array of methods, it calls them all, passing in the $builder
     *
     * @param ClassMetadataBuilder $builder
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function loadPropertyDoctrineMetaData(ClassMetadataBuilder $builder): void
    {
        $methodName = '__no_method__';
        try {
            $staticMethods = static::getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if (0 === stripos($methodName, UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META)) {
                    static::$methodName($builder);
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.'for '
                .self::$reflectionClass->getName()."::$methodName\n\n"
                .$e->getMessage()
            );
        }
    }

    /**
     * Get class level meta data, eg table name
     *
     * @param ClassMetadataBuilder $builder
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function loadClassDoctrineMetaData(ClassMetadataBuilder $builder): void
    {
        $tableName = MappingHelper::getTableNameForEntityFqn(static::class, self::$reflectionClass);
        $builder->setTable($tableName);
    }

    /**
     * Get an array of all static methods implemented by the current class
     *
     * Merges trait methods
     * Filters out this trait
     *
     * @return array|\ReflectionMethod[]
     * @throws \ReflectionException
     */
    protected static function getStaticMethods(): array
    {
        $currentClass = static::class;
        // get class level static methods
        if (!static::$reflectionClass instanceof \ReflectionClass
            || static::$reflectionClass->getName() !== $currentClass
        ) {
            static::$reflectionClass = new \ReflectionClass($currentClass);
        }
        $staticMethods = static::$reflectionClass->getMethods(
            \ReflectionMethod::IS_STATIC
        );
        // get static methods from traits
        $traits = self::$reflectionClass->getTraits();
        foreach ($traits as $trait) {
            if ($trait->getShortName() === 'UsesPHPMetaData') {
                continue;
            }
            $traitStaticMethods = $trait->getMethods(
                \ReflectionMethod::IS_STATIC
            );
            array_merge(
                $staticMethods,
                $traitStaticMethods
            );
        }

        return $staticMethods;
    }



    /**
     * Get the property name the Entity is mapped by when plural
     *
     * Override it in your entity class if you are using an Entity class name that doesn't pluralize nicely
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPlural(): string
    {
        try {
            if (null === static::$plural) {
                $singular       = static::getSingular();
                static::$plural = Inflector::pluralize($singular);
            }

            return static::$plural;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the property the name the Entity is mapped by when singular
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getSingular(): string
    {
        try {
            if (null === static::$singular) {
                if (null === self::$reflectionClass) {
                    self::$reflectionClass = new \ReflectionClass(static::class);
                }

                $shortName         = self::$reflectionClass->getShortName();
                $singularShortName = Inflector::singularize($shortName);

                $namespaceName = self::$reflectionClass->getNamespaceName();
                $namespaceParts = \explode(AbstractGenerator::ENTITIES_FOLDER_NAME, $namespaceName);
                $entityNamespace = \array_pop($namespaceParts);

                $namespacedShortName = \preg_replace(
                    '/\\\\/',
                    '',
                    $entityNamespace . $singularShortName);

                static::$singular = \lcfirst($namespacedShortName);
            }

            return static::$singular;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Which field is being used for ID - will normally be `id` as implemented by
     * \EdmondsCommerce\DoctrineStaticMeta\Fields\Traits\IdField
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getIdField(): string
    {
        return 'id';
    }
}
