<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

abstract class AbstractEmbeddableObject
{
    /**
     * @var EntityInterface
     */
    protected $owningEntity;

    protected static function setEmbeddableAndGetBuilder(ClassMetadata $metadata): ClassMetadataBuilder
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setEmbeddable();

        return $builder;
    }

    public function __toString(): string
    {
        return (string)Debug::export($this, 2);
    }

    public function setOwningEntity(EntityInterface $entity): void
    {
        $this->owningEntity = $entity;
    }

    abstract protected function getPrefix(): string;
}
