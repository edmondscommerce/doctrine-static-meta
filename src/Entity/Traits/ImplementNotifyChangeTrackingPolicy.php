<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

/**
 * Trait ImplementNotifyChangeTrackingPolicy
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/change-tracking-policies.html#notify
 */
trait ImplementNotifyChangeTrackingPolicy
{

    /**
     * @var array PropertyChangedListener[]
     */
    private $notifyChangeTrackingListeners = [];

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
    public function removePropertyChangedListeners()
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
        self::getDoctrineStaticMeta()->setMetaData($entityManager->getClassMetadata(self::class));
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
        $metaData = self::getDoctrineStaticMeta()->getMetaData();
        foreach ($metaData->getFieldNames() as $fieldName) {
            if (true === \ts\stringStartsWith($fieldName, $embeddablePropertyName)
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
     * @param string                       $propName
     * @param Collection|EntityInterface[] $entities
     */
    private function setEntityCollectionAndNotify(string $propName, Collection $entities): void
    {
        //If you are trying to set an empty collection, we need to actually loop through and remove them all
        $oldValue = clone $this->$propName;
        if ($entities->count() === 0 && $this->$propName->count() > 0) {
            foreach ($this->$propName as &$entity) {
                $this->removeFromEntityCollectionAndNotify($propName, $entity);
            }
            foreach ($this->notifyChangeTrackingListeners as $listener) {
                $listener->propertyChanged($this, $propName, $oldValue, $this->$propName);
            }

            return;
        }
        //otherwise, we need to loop through and add everything from the new collection
        foreach ($entities as &$entity) {
            if ($this->$propName->contains($entity)) {
                continue;
            }
            $this->addToEntityCollectionAndNotify($propName, $entity);
        }
        //and then remove everything in our colletion that is not in the new collection
        foreach ($this->$propName as &$entity) {
            if ($entities->contains($entity)) {
                continue;
            }
            $this->removeFromEntityCollectionAndNotify($propName, $entity);
        }
        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $this->$propName);
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
