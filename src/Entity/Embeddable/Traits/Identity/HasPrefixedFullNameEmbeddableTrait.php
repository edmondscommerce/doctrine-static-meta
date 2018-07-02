<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity;

// phpcs:disable
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity\HasPrefixedPrefixedFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\PrefixedPrefixedFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\PrefixedPrefixedFullNameEmbeddable;

trait HasPrefixedPrefixedFullNameEmbeddableTrait
{
    /**
     * @var PrefixedPrefixedFullNameEmbeddable
     */
    private $prefixedPrefixedFullNameEmbeddable;

    /**
     * Called at construction time
     */
    private function initPrefixedFullName()
    {
        $this->prefixedPrefixedFullNameEmbeddable = new PrefixedPrefixedFullNameEmbeddable();
    }

    /**
     * @return mixed
     */
    public function getPrefixedPrefixedFullNameEmbeddable(): PrefixedPrefixedFullNameEmbeddableInterface
    {
        return $this->prefixedPrefixedFullNameEmbeddable;
    }

    /**
     * @param mixed $prefixedPrefixedFullNameEmbeddable
     *
     * @return $this
     */
    public function setPrefixedPrefixedFullNameEmbeddable($prefixedPrefixedFullNameEmbeddable): self
    {
        $this->prefixedPrefixedFullNameEmbeddable = $prefixedPrefixedFullNameEmbeddable;

        return $this;
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForPrefixedPrefixedFullNameEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->createEmbedded(
            HasPrefixedPrefixedFullNameEmbeddableInterface::PROP_PREFIXED_PREFIXED_FULL_NAME_EMBEDDABLE,
            PrefixedPrefixedFullNameEmbeddable::class
        )
                ->setColumnPrefix(
                    HasPrefixedPrefixedFullNameEmbeddableInterface::COLUMN_PREFIX_PREFIXED_FULL_NAME
                )
                ->build();
    }
}
