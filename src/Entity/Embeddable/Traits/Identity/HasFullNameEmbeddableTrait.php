<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity\HasFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;

trait HasFullNameEmbeddableTrait
{
    /**
     * @var FullNameEmbeddableInterface
     */
    private $fullNameEmbeddable;

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForFullNameEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent(
            'postLoadSetOwningEntityOnFullNameEmbeddable',
            Events::postLoad
        );
        $builder->createEmbedded(
            HasFullNameEmbeddableInterface::PROP_FULL_NAME_EMBEDDABLE,
            FullNameEmbeddable::class
        )
                ->setColumnPrefix(
                    HasFullNameEmbeddableInterface::COLUMN_PREFIX_FULL_NAME
                )
                ->build();
    }

    /**
     * @return mixed
     */
    public function getFullNameEmbeddable(): FullNameEmbeddableInterface
    {
        return $this->fullNameEmbeddable;
    }

    public function postLoadSetOwningEntityOnFullNameEmbeddable(): void
    {
        $this->fullNameEmbeddable->setOwningEntity($this);
    }

    /**
     * Called at construction time
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function initFullNameEmbeddable(): void
    {
        $this->setFullNameEmbeddable(
            FullNameEmbeddable::create(FullNameEmbeddable::DEFAULTS),
            false
        );
    }

    /**
     * @param FullNameEmbeddableInterface $fullNameEmbeddable
     *
     * @param bool                        $notify
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function setFullNameEmbeddable(FullNameEmbeddableInterface $fullNameEmbeddable, bool $notify = true): self
    {
        $this->fullNameEmbeddable = $fullNameEmbeddable;
        $this->fullNameEmbeddable->setOwningEntity($this);
        if (true === $notify) {
            $this->notifyEmbeddablePrefixedProperties(
                HasFullNameEmbeddableInterface::PROP_FULL_NAME_EMBEDDABLE
            );
        }

        return $this;
    }
}
