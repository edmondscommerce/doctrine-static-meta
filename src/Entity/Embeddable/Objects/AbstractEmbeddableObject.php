<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

abstract class AbstractEmbeddableObject
{
    protected static function setEmbeddableAndGetBuilder(ClassMetadata $metadata): ClassMetadataBuilder
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setEmbeddable();

        return $builder;
    }

    public function __toString(): string
    {
        return (string)print_r($this, true);
    }
}
