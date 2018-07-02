<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

interface HasFullNameEmbeddableInterface extends EntityInterface
{
    public const PROP_FULL_NAME_EMBEDDABLE = 'fullNameEmbeddable';
    public const COLUMN_PREFIX_FULL_NAME   = 'full_name_';

    public function getFullNameEmbeddable(): FullNameEmbeddableInterface;

    public function setFullNameEmbeddable(FullNameEmbeddableInterface $fullNameEmbeddable);
}
