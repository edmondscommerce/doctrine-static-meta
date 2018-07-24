<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ImplementNotifyChangeTrackingPolicyInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;

abstract class AbstractEmbeddableObject
{
    /**
     * @var ImplementNotifyChangeTrackingPolicy
     */
    protected $owningEntity;

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

    abstract protected function getPrefix(): string;
}
