<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity;
// phpcs:disable
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity\HasFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;

trait HasFullNameEmbeddableTrait
{
    /**
     * @var FullNameEmbeddable
     */
    private $fullNameEmbeddable;

    /**
     * Called at construction time
     */
    private function initFullName()
    {
        $this->fullNameEmbeddable = new FullNameEmbeddable();
    }

    /**
     * @return mixed
     */
    public function getFullNameEmbeddable(): FullNameEmbeddableInterface
    {
        return $this->fullNameEmbeddable;
    }

    /**
     * @param mixed $fullNameEmbeddable
     *
     * @return $this
     */
    public function setFullNameEmbeddable($fullNameEmbeddable): self
    {
        $this->fullNameEmbeddable = $fullNameEmbeddable;

        return $this;
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForFullNameEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->createEmbedded(HasFullNameEmbeddableInterface::PROP_FULL_NAME_EMBEDDABLE, FullNameEmbeddable::class)
                ->setColumnPrefix(HasFullNameEmbeddableInterface::COLUMN_PREFIX_FULL_NAME)
                ->build();
    }
}
