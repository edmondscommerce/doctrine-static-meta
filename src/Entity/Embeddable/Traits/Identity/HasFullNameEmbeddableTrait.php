<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity\HasFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;

trait HasFullNameEmbeddableTrait
{
    private $fullNameEmbeddable;

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
     * @return HasFullNameEmbeddableInterface
     */
    public function setFullNameEmbeddable($fullNameEmbeddable): HasFullNameEmbeddableInterface
    {
        $this->fullNameEmbeddable = $fullNameEmbeddable;

        return $this;
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForFullNameEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->createEmbedded(HasFullNameEmbeddableInterface::PROP_FULL_NAME, FullNameEmbeddable::class)
                ->setColumnPrefix(HasFullNameEmbeddableInterface::COLUMN_PREFIX_FULL_NAME)
                ->build();
    }


}
