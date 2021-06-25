<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Embeddable\Traits\CatName;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Embeddable\Interfaces\CatName\HasSkeletonEmbeddableInterface;
use TemplateNamespace\Entity\Embeddable\Interfaces\Objects\CatName\SkeletonEmbeddableInterface;
use TemplateNamespace\Entity\Embeddable\Objects\CatName\SkeletonEmbeddable;

trait HasSkeletonEmbeddableTrait
{
    /**
     * @var SkeletonEmbeddableInterface
     */
    private SkeletonEmbeddableInterface $skeletonEmbeddable;

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForSkeletonEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent(
            'postLoadSetOwningEntityOnSkeletonEmbeddable',
            Events::postLoad
        );
        $builder->createEmbedded(
            HasSkeletonEmbeddableInterface::PROP_SKELETON_EMBEDDABLE,
            SkeletonEmbeddable::class
        )
                ->setColumnPrefix(
                    HasSkeletonEmbeddableInterface::COLUMN_PREFIX_SKELETON
                )
                ->build();
    }

    /**
     * @return mixed
     */
    public function getSkeletonEmbeddable(): SkeletonEmbeddableInterface
    {
        return $this->skeletonEmbeddable;
    }

    public function postLoadSetOwningEntityOnSkeletonEmbeddable(): void
    {
        $this->skeletonEmbeddable->setOwningEntity($this);
    }

    /**
     * Called at construction time
     */
    private function initSkeletonEmbeddable(): void
    {
        $this->setSkeletonEmbeddable(
            new SkeletonEmbeddable(
                SkeletonEmbeddableInterface::DEFAULT_PROPERTY_ONE,
                SkeletonEmbeddableInterface::DEFAULT_PROPERTY_TWO
            ),
            false
        );
    }

    /**
     * @param SkeletonEmbeddable $skeletonEmbeddable
     *
     * @param bool               $notify
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function setSkeletonEmbeddable(SkeletonEmbeddable $skeletonEmbeddable, bool $notify = true): self
    {
        $this->skeletonEmbeddable = $skeletonEmbeddable;
        $this->skeletonEmbeddable->setOwningEntity($this);
        if (true === $notify) {
            $this->notifyEmbeddablePrefixedProperties(
                HasSkeletonEmbeddableInterface::PROP_SKELETON_EMBEDDABLE
            );
        }

        return $this;
    }
}