<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\PrefixedPrefixedFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

interface HasPrefixedPrefixedFullNameEmbeddableInterface extends EntityInterface
{
    public const PROP_PREFIXED_PREFIXED_FULL_NAME_EMBEDDABLE = 'prefixedPrefixedFullNameEmbeddable';
    public const COLUMN_PREFIX_PREFIXED_FULL_NAME   = 'prefixed_prefixed_full_name_';

    public function getPrefixedPrefixedFullNameEmbeddable(): PrefixedPrefixedFullNameEmbeddableInterface;

    public function setPrefixedPrefixedFullNameEmbeddable(PrefixedPrefixedFullNameEmbeddableInterface $prefixedPrefixedFullNameEmbeddable);
}
