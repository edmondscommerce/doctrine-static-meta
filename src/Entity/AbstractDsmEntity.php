<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetaData;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Proxy\Proxy;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityDataValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\ValidatorStaticMeta;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use ts\Reflection\ReflectionClass;
use ts\Reflection\ReflectionMethod;

abstract class AbstractDsmEntity implements EntityInterface
{
    /**
     * @var DoctrineStaticMeta
     */
    protected static DoctrineStaticMeta $doctrineStatqicMeta;
    /**
     * @var ValidatorStaticMeta|null
     */
    protected static ?ValidatorStaticMeta $validatorStaticMeta;
    /**
     * @var EntityDataValidatorInterface
     */
    protected EntityDataValidatorInterface $entityDataValidator;
    /**
     * This is a special property that is manipulated via Reflection in the Entity factory.
     *
     * Whilst a transaction is running, validation is suspended, and then at the end of a transaction the full
     * validation is performed
     *
     * @var bool
     */
    protected bool $creationTransactionRunning = false;
    /**
     * @var array PropertyChangedListener[]
     */
    protected array $notifyChangeTrackingListeners = [];

    /**
     * Private constructor
     *
     * @throws ReflectionException
     */
    protected function __construct()
    {
        $this->runInitMethods();
    }

    /**
     * Find and run all init methods
     * - defined in relationship traits and generally to init ArrayCollection properties
     *
     * @throws ReflectionException
     */
    private function runInitMethods(): void
    {
        $reflectionClass = static::getDoctrineStaticMeta()->getReflectionClass();
        $methods         = $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $method) {
            if ($method instanceof ReflectionMethod) {
                $method = $method->getName();
            }
            if (
            \ts\stringStartsWith($method, UsesPHPMetaDataInterface::METHOD_PREFIX_INIT)
            ) {
                $this->$method();
            }
        }
    }

    /**
     * @param EntityFactoryInterface           $factory
     * @param DataTransferObjectInterface|null $dto
     *
     * @return EntityInterface&$this
     * @throws ValidationException
     */
    final public static function create(
        EntityFactoryInterface $factory,
        DataTransferObjectInterface $dto = null
    ): self {
        /** @var EntityInterface $entity */
        $entity = (new ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        if (false === ($entity instanceof EntityInterface)) {
            throw new RuntimeException('Invalid class instance');
        }
        $factory->initialiseEntity($entity);
        if (null !== $dto) {
            $entity->update($dto);

            return $entity;
        }
        $entity->getValidator()->validate();

        return $entity;
    }

    public static function getEntityFqn(): string
    {
        return static::class;
    }

    /**
     * Loads the class and property meta data in the class
     *
     * This is the method called by Doctrine to load the meta data
     *
     * @param DoctrineClassMetaData $metaData
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(DoctrineClassMetaData $metaData): void
    {
        try {
            static::getDoctrineStaticMeta()->setMetaData($metaData)->buildMetaData();
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * This method is called by the Symfony validation component when loading the meta data
     *
     * In this method, we pass around the meta data object and add data to it as required.
     *
     *
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
        static::getValidatorStaticMeta()->addValidatorMetaData($metadata);
    }

    /**
     * Get an instance of the ValidatorStaticMeta object for this Entity
     *
     * @return ValidatorStaticMeta
     */
    private static function getValidatorStaticMeta(): ValidatorStaticMeta
    {
        if (null === static::$validatorStaticMeta) {
            static::$validatorStaticMeta = new ValidatorStaticMeta(static::getDoctrineStaticMeta());
        }

        return static::$validatorStaticMeta;
    }

    /**
     * Update and validate the Entity.
     *
     * The DTO can
     *  - contain data not related to this Entity, it will be ignored
     *  - not have to have all the data for this Entity, it will only update where the DTO has the setter
     *
     * The entity state after update will be validated
     *
     * Will roll back all updates if validation fails
     *
     * @param DataTransferObjectInterface $dto
     *
     * @throws ValidationException
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    final public function update(DataTransferObjectInterface $dto): void
    {
        $backup  = [];
        $setters = static::getDoctrineStaticMeta()->getSetters();
        try {
            foreach ($setters as $getterName => $setterName) {
                if (false === method_exists($dto, $getterName)) {
                    continue;
                }
                $dtoValue = $dto->$getterName();
                if ($dtoValue instanceof UuidInterface && (string)$dtoValue === (string)$this->$getterName()) {
                    continue;
                }
                if (false === $this->creationTransactionRunning) {
                    $gotValue = null;
                    try {
                        $gotValue = $this->$getterName();
                    } catch (TypeError $e) {
                        //Required items will type error on the getter as they have no value
                    }
                    if ($dtoValue === $gotValue) {
                        continue;
                    }
                    $backup[$setterName] = $gotValue;
                }

                $this->$setterName($dtoValue);
            }
            if (true === $this->creationTransactionRunning) {
                return;
            }
            $this->getValidator()->validate();
        } catch (ValidationException | TypeError $e) {
            $reflectionClass = $this::getDoctrineStaticMeta()->getReflectionClass();
            foreach ($backup as $setterName => $backupValue) {
                /**
                 * We have to use reflection here because required property setter will not accept nulls
                 * which may be the backup value, especially on new object creation
                 */
                $propertyName       = $this::getDoctrineStaticMeta()->getPropertyNameFromSetterName($setterName);
                $reflectionProperty = $reflectionClass->getProperty($propertyName);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($this, $backupValue);
            }
            throw $e;
        }
    }

    /**
     * @return DoctrineStaticMeta
     * @throws ReflectionException
     */
    public static function getDoctrineStaticMeta(): DoctrineStaticMeta
    {
        if (null === static::$doctrineStaticMeta) {
            static::$doctrineStaticMeta = new DoctrineStaticMeta(static::class);
        }

        return static::$doctrineStaticMeta;
    }

    public function getValidator(): EntityDataValidatorInterface
    {
        if (!$this->entityDataValidator instanceof EntityDataValidatorInterface) {
            throw new RuntimeException(
                'You must call injectDataValidator before being able to update an Entity'
            );
        }

        return $this->entityDataValidator;
    }

    /**
     * This method is called automatically by the EntityFactory when initialisig the Entity, by way of the
     * EntityDependencyInjector
     *
     * @param EntityDataValidatorInterface $entityDataValidator
     */
    public function injectEntityDataValidator(EntityDataValidatorInterface $entityDataValidator): void
    {
        $this->entityDataValidator = $entityDataValidator;
        $this->entityDataValidator->setEntity($this);
    }

    /**
     * Set a notify change tracking listener (Unit of Work basically). Use the spl_object_hash to protect against
     * registering the same UOW more than once
     *
     * @param PropertyChangedListener $listener
     */
    public function addPropertyChangedListener(PropertyChangedListener $listener): void
    {
        $this->notifyChangeTrackingListeners[spl_object_hash($listener)] = $listener;
    }

    /**
     * If we want to totally disable the notify change, for example in bulk operations
     */
    public function removePropertyChangedListeners(): void
    {
        $this->notifyChangeTrackingListeners = [];
    }

    /**
     * The meta data is set to the entity when the meta data is loaded, however if metadata is cached that wont happen
     * This call ensures that the meta data is set
     *
     * @param EntityManagerInterface $entityManager
     *
     */
    public function ensureMetaDataIsSet(EntityManagerInterface $entityManager): void
    {
        static::getDoctrineStaticMeta()->setMetaData($entityManager->getClassMetadata(static::class));
    }

    /**
     * This notifies the embeddable properties on the owning Entity
     *
     * @param string      $embeddablePropertyName
     * @param null|string $propName
     * @param null        $oldValue
     * @param null        $newValue
     */
    public function notifyEmbeddablePrefixedProperties(
        string $embeddablePropertyName,
        ?string $propName = null,
        $oldValue = null,
        $newValue = null
    ): void {
        if ($oldValue !== null && $oldValue === $newValue) {
            return;
        }
        /**
         * @var ClassMetadata $metaData
         */
        $metaData = static::getDoctrineStaticMeta()->getMetaData();
        foreach ($metaData->getFieldNames() as $fieldName) {
            if (
                true === \ts\stringStartsWith($fieldName, $embeddablePropertyName)
                && false !== \ts\stringContains($fieldName, '.')
            ) {
                if ($fieldName !== null && $fieldName !== "$embeddablePropertyName.$propName") {
                    continue;
                }
                foreach ($this->notifyChangeTrackingListeners as $listener) {
                    //wondering if we can get away with not passing in the values?
                    $listener->propertyChanged($this, $fieldName, $oldValue, $newValue);
                }
            }
        }
    }

    /**
     * @return array
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function jsonSerialize(): array
    {
        $dsm         = static::getDoctrineStaticMeta();
        $toSerialize = [];
        $getters     = $dsm->getGetters();
        foreach ($getters as $getter) {
            /** @var mixed $got */
            $got = $this->$getter();
            if ($got instanceof EntityInterface) {
                continue;
            }
            if ($got instanceof Collection) {
                continue;
            }
            if ($got instanceof Proxy) {
                continue;
            }
            if ($got instanceof UuidInterface) {
                $got = $got->toString();
            }
            if ($got instanceof DateTimeImmutable) {
                $got = $got->format('Y-m-d H:i:s');
            }
            if (method_exists($got, '__toString')) {
                $got = (string)$got;
            }
            if (null !== $got && false === is_scalar($got)) {
                continue;
            }
            $property               = $dsm->getPropertyNameFromGetterName($getter);
            $toSerialize[$property] = $got;
        }

        return $toSerialize;
    }

    /**
     * To be called from all set methods
     *
     * This method updates the property value, then it runs this through validation
     * If validation fails, it sets the old value back and throws the caught exception
     * If validation passes, it then performs the Doctrine notification for property change
     *
     * @param string $propName
     * @param mixed  $newValue
     *
     */
    private function updatePropertyValue(string $propName, $newValue): void
    {
        if ($this->$propName === $newValue) {
            return;
        }
        $oldValue        = $this->$propName;
        $this->$propName = $newValue;
        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }

    /**
     * Called from the Has___Entities Traits
     *
     * @param string          $propName
     * @param EntityInterface $entity
     */
    private function removeFromEntityCollectionAndNotify(string $propName, EntityInterface $entity): void
    {
        if ($this->$propName === null) {
            $this->$propName = new ArrayCollection();
        }
        if ($this->$propName instanceof PersistentCollection) {
            $this->$propName->initialize();
        }
        if (!$this->$propName->contains($entity)) {
            return;
        }
        $oldValue = $this->$propName;
        $this->$propName->removeElement($entity);
        $newValue = $this->$propName;
        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }

    /**
     * Called from the Has___Entities Traits
     *
     * @param string          $propName
     * @param EntityInterface $entity
     */
    private function addToEntityCollectionAndNotify(string $propName, EntityInterface $entity): void
    {
        if ($this->$propName === null) {
            $this->$propName = new ArrayCollection();
        }
        if ($this->$propName->contains($entity)) {
            return;
        }
        $oldValue = $this->$propName;
        $this->$propName->add($entity);
        $newValue = $this->$propName;
        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }

    /**
     * Called from the Has___Entity Traits
     *
     * @param string               $propName
     * @param EntityInterface|null $entity
     */
    private function setEntityAndNotify(string $propName, ?EntityInterface $entity): void
    {
        if ($this->$propName === $entity) {
            return;
        }
        $oldValue        = $this->$propName;
        $this->$propName = $entity;
        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $entity);
        }
    }
}