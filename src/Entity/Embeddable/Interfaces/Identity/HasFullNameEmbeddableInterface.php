<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\HasEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;

interface HasFullNameEmbeddableInterface extends HasEmbeddableInterface
{
    public const PROP_FULL_NAME_EMBEDDABLE = 'fullNameEmbeddable';
    public const COLUMN_PREFIX_FULL_NAME   = 'full_name_';

    public function getFullNameEmbeddable(): FullNameEmbeddableInterface;

    public function setFullNameEmbeddable(FullNameEmbeddableInterface $fullNameEmbeddable);
}
