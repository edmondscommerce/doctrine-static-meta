<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ImplementNotifyChangeTrackingPolicyInterface;

interface AbstractEmbeddableObjectInterface
{
    /**
     * @param ClassMetadata<EntityInterface> $metadata
     */
    public static function loadMetadata(ClassMetadata $metadata): void;

    /**
     * @param array<int|string,mixed> $properties
     */
    public static function create(array $properties): static;

    public function setOwningEntity(ImplementNotifyChangeTrackingPolicyInterface $entity): void;

    public function __toString(): string;
}
