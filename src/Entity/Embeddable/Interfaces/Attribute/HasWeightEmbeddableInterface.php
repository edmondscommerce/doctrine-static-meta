<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Attribute\WeightEmbeddableInterface;

interface HasWeightEmbeddableInterface
{
    public const PROP_WEIGHT_EMBEDDABLE = 'weightEmbeddable';
    public const COLUMN_PREFIX_WEIGHT   = 'weight_';

    public function getWeightEmbeddable(): WeightEmbeddableInterface;
}
