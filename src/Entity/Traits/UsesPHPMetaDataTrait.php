<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetaData;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

trait UsesPHPMetaDataTrait
{

    /**
     * @var \ts\Reflection\ReflectionClass
     */
    private static $reflectionClass;

    /**
     * @var ClassMetadata
     */
    private static $metaData;

    /**
     * @var string
     */
    private static $singular;

    /**
     * @var string
     */
    private static $plural;

    /**
     * @var array
     */
    private static $setters;

    /**
     * @var array
     */
    private static $getters;

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
            self::$metaData        = $metadata;
            $builder               = new ClassMetadataBuilder($metadata);
            self::$reflectionClass = $metadata->getReflectionClass();
            self::loadPropertyDoctrineMetaData($builder);
            self::loadClassDoctrineMetaData($builder);
            self::setChangeTrackingPolicy($builder);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
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
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function loadPropertyDoctrineMetaData(ClassMetadataBuilder $builder): void
    {
        $methodName = '__no_method__';
        try {
            $staticMethods = self::getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if (0 === stripos(
                        $methodName,
                        UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META
                    )
                ) {
                    self::$methodName($builder);
                }
            }
        } catch (\Exception $e) {
            $reflectionClass = self::getReflectionClass();
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . 'for '
                . $reflectionClass->getName() . "::$methodName\n\n"
                . $e->getMessage()
            );
        }
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
        $reflectionClass = self::getReflectionClass();
        $staticMethods   = $reflectionClass->getMethods(
            \ReflectionMethod::IS_STATIC
        );
        // get static methods from traits
        $traits = $reflectionClass->getTraits();
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
     * Get class level meta data, eg table name, custom repository
     *
     * @param ClassMetadataBuilder $builder
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function loadClassDoctrineMetaData(ClassMetadataBuilder $builder): void
    {
        $tableName = MappingHelper::getTableNameForEntityFqn(self::class);
        $builder->setTable($tableName);
        self::setCustomRepositoryClass($builder);
    }

    /**
     * Setting the change policy to be Notify - best performance
     *
     * @see http://doctrine-orm.readthedocs.io/en/latest/reference/change-tracking-policies.html
     *
     * @param ClassMetadataBuilder $builder
     */
    public static function setChangeTrackingPolicy(ClassMetadataBuilder $builder): void
    {
        $builder->setChangeTrackingPolicyNotify();
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
            if (null === self::$plural) {
                $singular     = self::getSingular();
                self::$plural = Inflector::pluralize($singular);
            }

            return self::$plural;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
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
            if (null === self::$singular) {
                $reflectionClass = self::getReflectionClass();

                $shortName         = $reflectionClass->getShortName();
                $singularShortName = Inflector::singularize($shortName);

                $namespaceName   = $reflectionClass->getNamespaceName();
                $namespaceParts  = \explode(AbstractGenerator::ENTITIES_FOLDER_NAME, $namespaceName);
                $entityNamespace = \array_pop($namespaceParts);

                $namespacedShortName = \preg_replace(
                    '/\\\\/',
                    '',
                    $entityNamespace . $singularShortName
                );

                self::$singular = \lcfirst($namespacedShortName);
            }

            return self::$singular;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
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

    /**
     * In the class itself, we need to specify the repository class name
     *
     * @param ClassMetadataBuilder $builder
     *
     * @return mixed
     */
    abstract protected static function setCustomRepositoryClass(ClassMetadataBuilder $builder);

    /**
     * Get an array of setters by name
     *
     * @return array|string[]
     * @throws \ReflectionException
     */
    public function getSetters(): array
    {
        if (null !== self::$setters) {
            return self::$setters;
        }
        $skip            = [
            'setChangeTrackingPolicy'    => true,
            'addPropertyChangedListener' => true,
        ];
        self::$setters   = [];
        $reflectionClass = self::getReflectionClass();
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'set')) {
                self::$setters[] = $methodName;
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'add')) {
                self::$setters[] = $methodName;
                continue;
            }
        }

        return self::$setters;
    }

    /**
     * Get the short name (without fully qualified namespace) of the current Entity
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getShortName(): string
    {
        $reflectionClass = self::getReflectionClass();

        return $reflectionClass->getShortName();
    }

    /**
     * Get an array of getters by name
     * [];
     *
     * @return array|string[]
     * @throws \ReflectionException
     */
    public function getGetters(): array
    {
        if (null !== self::$getters) {
            return self::$getters;
        }
        $skip = [
            'getPlural'    => true,
            'getSingular'  => true,
            'getSetters'   => true,
            'getGetters'   => true,
            'getIdField'   => true,
            'getShortName' => true,
            'isValid'      => true,
        ];

        self::$getters   = [];
        $reflectionClass = self::getReflectionClass();
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'get')) {
                self::$getters[] = $methodName;
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'is')) {
                self::$getters[] = $methodName;
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'has')) {
                self::$getters[] = $methodName;
                continue;
            }
        }

        return self::$getters;
    }

    /**
     * Find and run all init methods
     * - defined in relationship traits and generally to init ArrayCollection properties
     *
     * @throws \ReflectionException
     */
    protected function runInitMethods(): void
    {
        $reflectionClass = self::getReflectionClass();
        $methods         = $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $method) {
            if ($method instanceof \ReflectionMethod) {
                $method = $method->getName();
            }
            if (\ts\stringContains($method, UsesPHPMetaDataInterface::METHOD_PREFIX_INIT)
                && \ts\stringStartsWith($method, UsesPHPMetaDataInterface::METHOD_PREFIX_INIT)
            ) {
                $this->$method();
            }
        }
    }

    /**
     * @return \ts\Reflection\ReflectionClass
     * @throws \ReflectionException
     */
    private static function getReflectionClass(): \ts\Reflection\ReflectionClass
    {
        if (!self::$reflectionClass instanceof \ts\Reflection\ReflectionClass) {
            self::$reflectionClass = new \ts\Reflection\ReflectionClass(self::class);
        }

        return self::$reflectionClass;
    }
}
