<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\Common\Util\Debug;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetaData;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

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

    /**
     * @var array
     */
    private static $setters;

    /**
     * @var array
     */
    private static $getters;


    /**
     * UsesPHPMetaDataTrait constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->runInitMethods();
    }

    /**
     * Find and run all init methods
     * - defined in relationship traits and generally to init ArrayCollection properties
     *
     * @throws \ReflectionException
     */
    protected function runInitMethods(): void
    {
        if (!static::$reflectionClass instanceof \ReflectionClass) {
            static::$reflectionClass = new \ReflectionClass(static::class);
        }
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
            static::setChangeTrackingPolicy($builder);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Setthing the change policy to be deferred explicit for the moment. Should consider if this needs to be
     * configurable in the future
     *
     * @see http://doctrine-orm.readthedocs.io/en/latest/reference/change-tracking-policies.html
     *
     * @param ClassMetadataBuilder $builder
     */
    public static function setChangeTrackingPolicy(ClassMetadataBuilder $builder)
    {
        $builder->setChangeTrackingPolicyDeferredExplicit();
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
                if (0 === stripos(
                    $methodName,
                    UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META
                )
                ) {
                    static::$methodName($builder);
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.'for '
                .static::$reflectionClass->getName()."::$methodName\n\n"
                .$e->getMessage()
            );
        }
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
        $tableName = MappingHelper::getTableNameForEntityFqn(static::class);
        $builder->setTable($tableName);
        static::setCustomRepositoryClass($builder);
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

                $namespaceName   = self::$reflectionClass->getNamespaceName();
                $namespaceParts  = \explode(AbstractGenerator::ENTITIES_FOLDER_NAME, $namespaceName);
                $entityNamespace = \array_pop($namespaceParts);

                $namespacedShortName = \preg_replace(
                    '/\\\\/',
                    '',
                    $entityNamespace.$singularShortName
                );

                static::$singular = \lcfirst($namespacedShortName);
            }

            return static::$singular;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get an array of setters by name
     *
     * @return array|string[]
     */
    public function getSetters(): array
    {
        if (null !== static::$setters) {
            return static::$setters;
        }
        $skip            = [
            'setChangeTrackingPolicy' => true,
        ];
        static::$setters = [];
        foreach (self::$reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (0 === \strpos($methodName, 'set')) {
                static::$setters[] = $methodName;
                continue;
            }
            if (0 === \strpos($methodName, 'add')) {
                static::$setters[] = $methodName;
                continue;
            }
        }

        return static::$setters;
    }

    /**
     * Get an array of getters by name
     * [];
     * @return array|string[]
     */
    public function getGetters(): array
    {
        if (null !== static::$getters) {
            return static::$getters;
        }
        $skip = [
            'getPlural'    => true,
            'getSingular'  => true,
            'getSetters'   => true,
            'getGetters'   => true,
            'getIdField'   => true,
            'getShortName' => true,
        ];

        static::$getters = [];
        foreach (self::$reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (0 === \strpos($methodName, 'get')) {
                static::$getters[] = $methodName;
                continue;
            }
        }

        return static::$getters;
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
     * Get the short name (without fully qualified namespace) of the current Entity
     *
     * @return string
     */
    public function getShortName(): string
    {
        return static::$reflectionClass->getShortName();
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __toString(): string
    {
        return (string)print_r(Debug::export($this, 2), true);
    }
}
