<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity;

use Doctrine\ORM\Events;
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
    private function initEmbeddableFullName(): void
    {
        $this->fullNameEmbeddable = new FullNameEmbeddable();
        $this->fullNameEmbeddable->setOwningEntity($this);
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

    public function postLoadSetOwningEntity(): void
    {
        $this->fullNameEmbeddable->setOwningEntity($this);
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForFullNameEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent('postLoadSetOwningEntity', Events::postLoad);
        $builder->createEmbedded(
            HasFullNameEmbeddableInterface::PROP_FULL_NAME_EMBEDDABLE,
            FullNameEmbeddable::class
        )
                ->setColumnPrefix(
                    HasFullNameEmbeddableInterface::COLUMN_PREFIX_FULL_NAME
                )
                ->build();
    }
}
