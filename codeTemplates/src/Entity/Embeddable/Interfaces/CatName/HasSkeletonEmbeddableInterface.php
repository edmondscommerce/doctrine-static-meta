<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Embeddable\Interfaces\CatName;

use TemplateNamespace\Entity\Embeddable\Interfaces\Objects\CatName\SkeletonEmbeddableInterface;

interface HasSkeletonEmbeddableInterface
{
    public const PROP_SKELETON_EMBEDDABLE = 'skeletonEmbeddable';
    public const COLUMN_PREFIX_SKELETON   = 'skeleton_';

    public function getSkeletonEmbeddable(): SkeletonEmbeddableInterface;
}