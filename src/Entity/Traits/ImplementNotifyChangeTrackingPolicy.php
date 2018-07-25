<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

/**
 * Trait ImplementNotifyChangeTrackingPolicy
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/change-tracking-policies.html#notify
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Traits
 */
trait ImplementNotifyChangeTrackingPolicy
{
    /**
     * @var array PropertyChangedListener[]
     */
    private $notifyChangeTrackingListeners = [];

    /**
     * @param PropertyChangedListener $listener
     */
    public function addPropertyChangedListener(PropertyChangedListener $listener): void
    {
        $this->notifyChangeTrackingListeners[] = $listener;
    }

    /**
     * The meta data is set to the entity when the meta data is loaded
     *
     * This call will load the meta data if it has not already been set, which will in turn set it against the Entity
     *
     * @param EntityManagerInterface $entityManager
     */
    public function ensureMetaDataIsSet(EntityManagerInterface $entityManager): void
    {
        if (self::$metaData instanceof ClassMetadata) {
            return;
        }
        $entityManager->getClassMetadata(self::class);
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
        $metaData = static::$metaData;
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
     * To be called from all set methods
     *
     * This method updates the property value, then it runs this through validation
     * If validation fails, it sets the old value back and throws the caught exception
     * If validation passes, it then performs the Doctrine notification for property change
     *
     * @param string $propName
     * @param mixed  $newValue
     *
     * @throws ValidationException
     */
    private function updatePropertyValueThenValidateAndNotify(string $propName, $newValue): void
    {
        if ($this->$propName === $newValue) {
            return;
        }
        $oldValue        = $this->$propName;
        $this->$propName = $newValue;
        if ($this instanceof ValidatedEntityInterface) {
            try {
                $this->validateProperty($propName);
            } catch (ValidationException $e) {
                $this->$propName = $oldValue;
                throw $e;
            }
        }

        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }

    /**
     * Called from the Has___Entities Traits
     *
     * @param string                       $propName
     * @param Collection|EntityInterface[] $entities
     */
    private function setEntityCollectionAndNotify(string $propName, Collection $entities): void
    {
        if ($this->$propName === $entities) {
            return;
        }
        $oldValue        = $this->$propName;
        $this->$propName = $entities;
        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $entities);
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
     * Called from the Has___Entities Traits
     *
     * @param string          $propName
     * @param EntityInterface $entity
     */
    private function removeFromEntityCollectionAndNotify(string $propName, EntityInterface $entity): void
    {
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
