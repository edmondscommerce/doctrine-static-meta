<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta\RequiredRelation;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use ReflectionException;
use RuntimeException;
use ts\Reflection\ReflectionClass;
use ts\Reflection\ReflectionMethod;

use function array_pop;
use function explode;
use function lcfirst;
use function preg_match;
use function preg_replace;
use function trim;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DoctrineStaticMeta
{
    public const DSM_INIT_METHOD_PREFIX = 'dsmInit';

    /**
     * @var NamespaceHelper
     */
    private static $namespaceHelper;
    /**
     * @var ReflectionHelper
     */
    private static $reflectionHelper;
    /**
     * @var array
     */
    private $embeddableProperties;
    /**
     * @var array
     */
    private $getters;
    /**
     * @var ClassMetadata|\Doctrine\Common\Persistence\Mapping\ClassMetadata|ClassMetadataInfo
     */
    private $metaData;
    /**
     * @var string
     */
    private $plural;
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;
    /**
     * @var RequiredRelation[]
     */
    private $requiredRelationProperties;
    /**
     * @var array
     */
    private $setters;
    /**
     * @var string
     */
    private $singular;
    /**
     * @var array|null
     */
    private $staticMethods;

    /**
     * DoctrineStaticMeta constructor.
     *
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function __construct(string $entityFqn)
    {
        $this->reflectionClass = new ReflectionClass($entityFqn);
        $this->runDsmInitMethods();
    }

    private function runDsmInitMethods(): void
    {
        $methodName = '__no_method__';
        try {
            $staticMethods = $this->getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if (
                \ts\stringStartsWith($methodName, self::DSM_INIT_METHOD_PREFIX)
                ) {
                    $method->setAccessible(true);
                    $method->invokeArgs(null, [$this]);
                }
            }
        } catch (Exception $e) {
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
     * @return array|ReflectionMethod[]
     * @throws ReflectionException
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

    public function setRequiredRelationProperty(RequiredRelation $requiredRelation): self
    {
        $this->requiredRelationProperties[$requiredRelation->getPropertyName()] = $requiredRelation;

        return $this;
    }

    /**
     * @return RequiredRelation[]
     */
    public function getRequiredRelationProperties(): array
    {
        return $this->requiredRelationProperties ?? [];
    }

    public function getMetaData(): ClassMetadata
    {
        if ($this->metaData instanceof ClassMetadata) {
            return $this->metaData;
        }
        $this->metaData = new ClassMetadata($this->reflectionClass->getName());
        $this->buildMetaData();

        return $this->metaData;
    }

    public function setMetaData(ClassMetadata $metaData = null): self
    {
        if (null === $metaData) {
            $this->getMetaData();

            return $this;
        }
        $this->metaData = $metaData;

        return $this;
    }

    public function buildMetaData(): void
    {
        if (false === $this->metaData instanceof ClassMetadataInfo) {
            throw new RuntimeException('Invalid meta data class ' . \ts\print_r($this->metaData, true));
        }
        $builder = new ClassMetadataBuilder($this->metaData);
        $this->loadDoctrineMetaData($builder, UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META);
        $this->loadDoctrineMetaData($builder, UsesPHPMetaDataInterface::METHOD_PREFIX_GET_CLASS_DOCTRINE_META);
        $this->setTableName($builder);
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
     * @param string               $methodPrefix
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function loadDoctrineMetaData(ClassMetadataBuilder $builder, string $methodPrefix): void
    {
        $methodName = '__no_method__';
        try {
            $staticMethods = $this->getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if (
                    0 === stripos(
                        $methodName,
                        $methodPrefix
                    )
                ) {
                    $method->setAccessible(true);
                    $method->invokeArgs(null, [$builder]);
                }
            }
        } catch (Exception $e) {
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
     * Sets the table name for the class
     *
     * @param ClassMetadataBuilder $builder
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function setTableName(ClassMetadataBuilder $builder): void
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

    private function setCustomRepositoryClass(ClassMetadataBuilder $builder): void
    {
        $repositoryClassName = (new NamespaceHelper())->getRepositoryqnFromEntityFqn($this->reflectionClass->getName());
        $builder->setCustomRepositoryClass($repositoryClassName);
    }

    /**
     * Get an array of property names that contain embeddable objects
     *
     * @return array
     * @throws ReflectionException
     */
    public function getEmbeddableProperties(): array
    {
        if (null !== $this->embeddableProperties) {
            return $this->embeddableProperties;
        }
        $traits = $this->reflectionClass->getTraits();
        $return = [];
        foreach ($traits as $traitName => $traitReflection) {
            if (\ts\stringContains($traitName, '\\Entity\\Embeddable\\Traits')) {
                $property                     = $traitReflection->getProperties()[0]->getName();
                $embeddableObjectInterfaceFqn = $this->getTypesFromVarComment(
                    $property,
                    $this->getReflectionHelper()->getTraitProvidingProperty($traitReflection, $property)->getFileName()
                )[0];
                $embeddableObject             = $this->getNamespaceHelper()
                                                     ->getEmbeddableObjectFqnFromEmbeddableObjectInterfaceFqn(
                                                         $embeddableObjectInterfaceFqn
                                                     );
                $return[$property]            = $embeddableObject;
            }
        }

        return $return;
    }

    /**
     * Parse the docblock for a property and get the type, then read the source code to resolve the short type to the
     * FQN of the type. Roll on PHP 7.3
     *
     * @param string $property
     *
     * @param string $filename
     *
     * @return array
     */
    private function getTypesFromVarComment(string $property, string $filename): array
    {
        $docComment = $this->reflectionClass->getProperty($property)->getDocComment();
        preg_match('%@var\s*?(.+)%', $docComment, $matches);
        $traitCode = \ts\file_get_contents($filename);
        $types     = explode('|', $matches[1]);
        $return    = [];
        foreach ($types as $type) {
            $type = trim($type);
            if ('null' === $type) {
                continue;
            }
            if ('ArrayCollection' === $type) {
                continue;
            }
            $arrayNotation = '';
            if ('[]' === substr($type, -2)) {
                $type          = substr($type, 0, -2);
                $arrayNotation = '[]';
            }
            $pattern = "%^use (.+?)\\\\${type}(;| |\[)%m";
            preg_match($pattern, $traitCode, $matches);
            if (!isset($matches[1])) {
                throw new RuntimeException(
                    'Failed finding match for type ' . $type . ' in ' . $filename
                );
            }
            $return[] = $matches[1] . '\\' . $type . $arrayNotation;
        }

        return $return;
    }

    private function getReflectionHelper(): ReflectionHelper
    {
        if (null === self::$reflectionHelper) {
            self::$reflectionHelper = new ReflectionHelper($this->getNamespaceHelper());
        }

        return self::$reflectionHelper;
    }

    private function getNamespaceHelper(): NamespaceHelper
    {
        if (null === self::$namespaceHelper) {
            self::$namespaceHelper = new NamespaceHelper();
        }

        return self::$namespaceHelper;
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
                $this->plural = MappingHelper::pluralize($singular);
            }

            return $this->plural;
        } catch (Exception $e) {
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
                $singularShortName = MappingHelper::singularize($shortName);

                $namespaceName   = $reflectionClass->getNamespaceName();
                $namespaceParts  = explode(AbstractGenerator::ENTITIES_FOLDER_NAME, $namespaceName);
                $entityNamespace = array_pop($namespaceParts);

                $namespacedShortName = preg_replace(
                    '/\\\\/',
                    '',
                    $entityNamespace . $singularShortName
                );

                $this->singular = lcfirst($namespacedShortName);
            }

            return $this->singular;
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return ReflectionClass
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function getSetterNameFromPropertyName(string $property): ?string
    {
        foreach ($this->getSetters() as $setter) {
            if (preg_match('%^(set|add)' . $property . '%i', $setter)) {
                return $setter;
            }
        }

        return null;
    }

    /**
     * Get an array of setters by name
     *
     * @return array|string[]
     * @throws ReflectionException
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
        foreach (
            $reflectionClass->getMethods(
                \ReflectionMethod::IS_PRIVATE | \ReflectionMethod::IS_PUBLIC
            ) as $method
        ) {
            $methodName = $method->getName();
            if (isset($skip[$methodName])) {
                continue;
            }
            if (\ts\stringStartsWith($methodName, 'set')) {
                $this->setters[$this->getGetterForSetter($methodName)] = $methodName;
                continue;
            }
        }

        return $this->setters;
    }

    private function getGetterForSetter(string $setterName): string
    {
        $propertyName    = $this->getPropertyNameFromSetterName($setterName);
        $matchingGetters = [];
        foreach ($this->getGetters() as $getterName) {
            $getterPropertyName = $this->getPropertyNameFromGetterName($getterName);
            if (strtolower($getterPropertyName) === strtolower($propertyName)) {
                $matchingGetters[] = $getterName;
            }
        }
        if (count($matchingGetters) !== 1) {
            throw new RuntimeException(
                'Found either less or more than one matching getter for ' .
                $propertyName . ': ' . print_r($matchingGetters, true)
                . "\n Current Entity: " . $this->getReflectionClass()->getName()
            );
        }

        return current($matchingGetters);
    }

    public function getPropertyNameFromSetterName(string $setterName): string
    {
        $propertyName = preg_replace('%^(set|add)(.+)%', '$2', $setterName);
        $propertyName = lcfirst($propertyName);

        return $propertyName;
    }

    /**
     * Get an array of getters by name
     *
     * @return array|string[]
     * @throws ReflectionException
     */
    public function getGetters(): array
    {
        if (null !== $this->getters) {
            return $this->getters;
        }
        $skip = [
            'getEntityFqn'          => true,
            'getDoctrineStaticMeta' => true,
            'isValid'               => true,
            'getValidator'          => true,
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

    public function getPropertyNameFromGetterName(string $getterName): string
    {
        $propertyName = preg_replace('%^(get|is|has)(.+)%', '$2', $getterName);
        $propertyName = lcfirst($propertyName);

        return $propertyName;
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
}
