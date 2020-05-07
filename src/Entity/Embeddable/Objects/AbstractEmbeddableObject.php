<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ImplementNotifyChangeTrackingPolicyInterface;

abstract class AbstractEmbeddableObject
{
    /**
     * @var ImplementNotifyChangeTrackingPolicyInterface
     */
    protected ImplementNotifyChangeTrackingPolicyInterface $owningEntity;


    protected static function setEmbeddableAndGetBuilder(ClassMetadata $metadata): ClassMetadataBuilder
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setEmbeddable();

        return $builder;
    }

    abstract public function __toString(): string;

    public function setOwningEntity(ImplementNotifyChangeTrackingPolicyInterface $entity): void
    {
        $this->owningEntity = $entity;
    }

    /**
     * If we are attached to an owning Entity, then we need to use it to Notify the Unit of Work about changes
     *
     * If we are not attached, then do nothing. When we are attached, this should be triggered automatically
     *
     * @param null|string $propName
     * @param null|mixed  $oldValue
     * @param null|mixed  $newValue
     */
    protected function notifyEmbeddablePrefixedProperties(
        ?string $propName = null,
        $oldValue = null,
        $newValue = null
    ): void {
        if (null === $this->owningEntity) {
            return;
        }
        $this->owningEntity->notifyEmbeddablePrefixedProperties(
            $this->getPrefix(),
            $propName,
            $oldValue,
            $newValue
        );
    }

    abstract protected function getPrefix(): string;
}
