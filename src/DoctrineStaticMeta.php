<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class DoctrineStaticMeta
{
    /**
     * @var \ts\Reflection\ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var ClassMetadata
     */
    private $metaData;


    /**
     * @var string
     */
    private $singular;

    /**
     * @var string
     */
    private $plural;

    /**
     * @var array
     */
    private $setters;

    /**
     * @var array
     */
    private $getters;

    /**
     * @var array|null
     */
    private $staticMethods;

    /**
     * DoctrineStaticMeta constructor.
     *
     * @param string $entityFqn
     *
     * @throws \ReflectionException
     */
    public function __construct(string $entityFqn)
    {
        $this->reflectionClass = new \ts\Reflection\ReflectionClass($entityFqn);
    }

    /**
     * @param ClassMetadata $metaData
     *
     * @return DoctrineStaticMeta
     */
    public function setMetaData(ClassMetadata $metaData): self
    {
        $this->metaData = $metaData;

        return $this;
    }


    public function buildMetaData(): void
    {
        $builder = new ClassMetadataBuilder($this->metaData);
        $this->loadPropertyDoctrineMetaData($builder);
        $this->loadClassDoctrineMetaData($builder);
        self::setChangeTrackingPolicy($builder);
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
    private function loadPropertyDoctrineMetaData(ClassMetadataBuilder $builder): void
    {
        $methodName = '__no_method__';
        try {
            $staticMethods = $this->getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if (0 === stripos(
                    $methodName,
                    UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META
                )
                ) {
                    $method->setAccessible(true);
                    $method->invokeArgs(null, [$builder]);
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ' for '
                . $this->reflectionClass->getName() . "::$methodName\n\n"
                . $e->getMessage(),
                $e->getCode(),
                $e
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
    private function loadClassDoctrineMetaData(ClassMetadataBuilder $builder): void
    {
        $tableName = MappingHelper::getTableNameForEntityFqn($this->reflectionClass->getName());
        $builder->setTable($tableName);
        $this->callPrivateStaticMethodOnEntity('setCustomRepositoryClass', [$builder]);
    }

    private function callPrivateStaticMethodOnEntity(string $methodName, array $args): void
    {
        $method = $this->reflectionClass->getMethod($methodName);
        $method->setAccessible(true);
        $method->invokeArgs(null, $args);
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
     * Get an array of all static methods implemented by the current class
     *
     * Merges trait methods
     * Filters out this trait
     *
     * @return array|\ReflectionMethod[]
     * @throws \ReflectionException
     */
    public function getStaticMethods(): array
    {
        if (null !== $this->staticMethods) {
            return $this->staticMethods;
        }
        $this->staticMethods = $this->reflectionClass->getMethods(
            \ReflectionMethod::IS_STATIC
        );
//        // get static methods from traits
//        $traitStaticMethods = [];
//        $traits             = $this->reflectionClass->getTraits();
//        foreach ($traits as $trait) {
//            if ($trait->getShortName() === 'UsesPHPMetaData') {
//                continue;
//            }
//            $traitStaticMethods = $trait->getMethods(
//                \ReflectionMethod::IS_STATIC
//            );
//            array_merge(
//                $staticMethods,
//                $traitStaticMethods
//            );
//        }

        return $this->staticMethods;
    }

    /**
     * Get the property the name the Entity is mapped by when singular
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getSingular(): string
    {
        try {
            if (null === $this->singular) {
                $reflectionClass = $this->getReflectionClass();

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

                $this->singular = \lcfirst($namespacedShortName);
            }

            return $this->singular;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
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
    public function getPlural(): string
    {
        try {
            if (null === $this->plural) {
                $singular     = $this->getSingular();
                $this->plural = Inflector::pluralize($singular);
            }

            return $this->plural;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get an array of setters by name
     *
     * @return array|string[]
     */
    public function getSetters(): array
    {
        if (null !== $this->setters) {
            return $this->setters;
        }
        $skip            = [
            'addPropertyChangedListener' => true,
        ];
        $this->setters   = [];
        $reflectionClass = $this->getReflectionClass();
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'set')) {
                $this->setters[] = $methodName;
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'add')) {
                $this->setters[] = $methodName;
                continue;
            }
        }

        return $this->setters;
    }

    /**
     * Get the short name (without fully qualified namespace) of the current Entity
     *
     * @return string
     */
    public function getShortName(): string
    {
        $reflectionClass = $this->getReflectionClass();

        return $reflectionClass->getShortName();
    }

    /**
     * Get an array of getters by name
     * [];
     *
     * @return array|string[]
     */
    public function getGetters(): array
    {
        if (null !== $this->getters) {
            return $this->getters;
        }
        $skip = [
            'getIdField' => true,
            'isValid'    => true,
        ];

        $this->getters   = [];
        $reflectionClass = $this->getReflectionClass();
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'get')) {
                $this->getters[] = $methodName;
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'is')) {
                $this->getters[] = $methodName;
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'has')) {
                $this->getters[] = $methodName;
                continue;
            }
        }

        return $this->getters;
    }

    /**
     * @return \ts\Reflection\ReflectionClass
     */
    public function getReflectionClass(): \ts\Reflection\ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @return ClassMetadata
     */
    public function getMetaData(): ClassMetadata
    {
        return $this->metaData;
    }
}
