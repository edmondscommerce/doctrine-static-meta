<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Product as Product;

interface HasProductInterface
{
    public const PROPERTY_NAME_PRODUCT = 'product';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForProduct(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Product
     */
    public function getProduct(): ?Product;

    /**
     * @param Product|null $product
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setProduct(
        ?Product $product,
        bool $recip = true
    ): HasProductInterface;

    /**
     * @return self
     */
    public function removeProduct(): HasProductInterface;
}
