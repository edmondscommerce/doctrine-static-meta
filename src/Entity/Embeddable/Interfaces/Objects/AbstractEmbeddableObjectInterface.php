<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;

interface AbstractEmbeddableObjectInterface
{
    public static function loadMetadata(ClassMetadata $metadata): void;

    public function setOwningEntity(ImplementNotifyChangeTrackingPolicy $entity): void;

    public function __toString(): string;
}
