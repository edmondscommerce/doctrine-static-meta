<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

trait UsesPHPMetaData
{

    /**
     * @var \ReflectionClass
     */
    private static $reflectionClass;

    /**
     * @var string
     */
    private static
        $singular,
        $plural;


    public function __construct()
    {
        $this->runInitMethods();
    }

    /**
     * Find and all all init methods
     * - defined in relationship traits and generally to init ArrayCollection properties
     */
    protected function runInitMethods()
    {
        $methods = static::$reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $method) {
            if ($method instanceof \ReflectionMethod) {
                $method = $method->getName();
            }
            if (0 === strpos($method, UsesPHPMetaDataInterface::initMethodPrefix)) {
                $this->$method();
            }
        }
    }

    /**
     * Loads the class and property meta data in the class
     *
     * This is the method called by Doctrine to load the meta data
     *
     * @param ClassMetadata $metadata
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadMetaData(ClassMetadata $metadata)
    {
        $builder                 = new ClassMetadataBuilder($metadata);
        static::$reflectionClass = $metadata->getReflectionClass();
        static::loadPropertyMetaData($builder);
        static::loadClassMetaData($builder);
    }

    /**
     * This method will reflect on the entity class and pull out all the methods that begin with
     * static::$propertyMetaDataPrefix which is 'getPropertyMetaFor' by default
     *
     * Once it has an array of methods, it calls them all, passing in the $builder
     *
     * @param ClassMetadataBuilder $builder
     *
     * @throws DoctrineStaticMetaException
     */
    protected static function loadPropertyMetaData(ClassMetadataBuilder $builder)
    {
        $methodName = '__no_method__';
        try {
            $currentClass = static::class;
            // get class level static methods
            if (!static::$reflectionClass instanceof \ReflectionClass || static::$reflectionClass->getName() !== $currentClass) {
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
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if (0 === stripos($methodName, UsesPHPMetaDataInterface::propertyMetaDataMethodPrefix)) {
                    static::$methodName($builder);
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                "Exception when loading meta data for "
                . self::$reflectionClass->getName() . "::$methodName\n\n"
                . $e->getMessage()
            );
        }
    }

    /**
     * Get class level meta data, eg table name
     *
     * @param ClassMetadataBuilder $builder
     *
     * @throws DoctrineStaticMetaException
     */
    protected static function loadClassMetaData(ClassMetadataBuilder $builder)
    {
        $namespaceHelper = new NamespaceHelper();
        $subFqn          = $namespaceHelper->getEntitySubNamespace(
            static::class,
            $namespaceHelper->getEntityNamespaceRootFromEntityReflection(
                $builder->getClassMetadata()->getReflectionClass() ?? new \ReflectionClass(static::class),
                AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER
            )
        );
        $builder->setTable(
            Inflector::tableize(
                str_replace('\\', '', $subFqn)
            )
        );
    }

    /**
     * Get the property name the Entity is mapped by when plural
     *
     * Override it in your entity class if you are using an Entity class name that doesn't pluralize nicely
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function getPlural(): string
    {
        if (null === static::$plural) {
            $singular       = static::getSingular();
            static::$plural = Inflector::pluralize($singular);
        }

        return static::$plural;
    }

    /**
     * Get the property the name the Entity is mapped by when singular
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function getSingular(): string
    {
        if (null === static::$singular) {
            if (null === self::$reflectionClass) {
                self::$reflectionClass = new \ReflectionClass(static::class);
            }
            $shortName        = self::$reflectionClass->getShortName();
            static::$singular = lcfirst(Inflector::singularize($shortName));
        }

        return static::$singular;
    }

    /**
     * Which field is being used for ID - will normally be `id` as implemented by
     * \EdmondsCommerce\DoctrineStaticMeta\Traits\Fields\IdField
     *
     * @return string
     */
    public static function getIdField(): string
    {
        return 'id';
    }


}
