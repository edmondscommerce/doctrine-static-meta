<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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
     * @var string
     */
    private $dtoFqn;

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

    public function buildMetaData(): void
    {
        $builder = new ClassMetadataBuilder($this->metaData);
        $this->loadPropertyDoctrineMetaData($builder);
        $this->loadClassDoctrineMetaData($builder);
        $this->setChangeTrackingPolicy($builder);
        $this->setCustomRepositoryClass($builder);
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

        return $this->staticMethods;
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
    }

    /**
     * Setting the change policy to be Notify - best performance
     *
     * @see http://doctrine-orm.readthedocs.io/en/latest/reference/change-tracking-policies.html
     *
     * @param ClassMetadataBuilder $builder
     */
    public function setChangeTrackingPolicy(ClassMetadataBuilder $builder): void
    {
        $builder->setChangeTrackingPolicyNotify();
    }

    private function setCustomRepositoryClass(ClassMetadataBuilder $builder)
    {
        $repositoryClassName = (new NamespaceHelper())->getRepositoryqnFromEntityFqn($this->reflectionClass->getName());
        $builder->setCustomRepositoryClass($repositoryClassName);
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
     * @return \ts\Reflection\ReflectionClass
     */
    public function getReflectionClass(): \ts\Reflection\ReflectionClass
    {
        return $this->reflectionClass;
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
            'addPropertyChangedListener'     => true,
            'setEntityCollectionAndNotify'   => true,
            'addToEntityCollectionAndNotify' => true,
            'setEntityAndNotify'             => true,
        ];
        $this->setters   = [];
        $reflectionClass = $this->getReflectionClass();
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE) as $method) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'set') || \ts\stringStartsWith($methodName, 'add')) {
                $this->setters[$this->getGetterForSetter($methodName)] = $methodName;
                continue;
            }
        }

        return $this->setters;
    }

    private function getGetterForSetter(string $setterName): string
    {
        $propertyName    = preg_replace('%^(set|add)(.+)%', '$2', $setterName);
        $matchingGetters = [];
        foreach ($this->getGetters() as $getterName) {

            $getterWithoutVerb = preg_replace('%^(get|is|has)(.+)%', '$2', $getterName);
            if (strtolower($getterWithoutVerb) === strtolower($propertyName)) {
                $matchingGetters[] = $getterName;
            }
        }
        if (count($matchingGetters) !== 1) {
            throw new \RuntimeException('Found either less or more than one matching getter for ' .
                                        $propertyName .
                                        ': ' .
                                        print_r($matchingGetters, true));
        }

        return current($matchingGetters);
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
            'getDoctrineStaticMeta' => true,
            'isValid'               => true,
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
     * @return ClassMetadata
     */
    public function getMetaData(): ClassMetadata
    {
        return $this->metaData;
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

}
