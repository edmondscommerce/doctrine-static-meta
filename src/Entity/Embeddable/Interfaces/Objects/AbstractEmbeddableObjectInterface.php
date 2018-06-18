<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

interface AbstractEmbeddableObjectInterface
{
    /**
     * An embeddable must expose this public static method which is then called when building the meta data in the trait
     *
     * @param ClassMetadataBuilder $builder
     */
    public static function embeddableMeta(ClassMetadataBuilder $builder): void;
}
