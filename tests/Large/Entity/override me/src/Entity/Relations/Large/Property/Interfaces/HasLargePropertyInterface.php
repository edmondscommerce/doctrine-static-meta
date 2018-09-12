<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Property\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Large\PropertyInterface;

interface HasLargePropertyInterface
{
    public const PROPERTY_NAME_LARGE_PROPERTY = 'largeProperty';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForLargeProperty(ClassMetadataBuilder $builder): void;

    /**
     * @return null|PropertyInterface
     */
    public function getLargeProperty(): ?PropertyInterface;

    /**
     * @param PropertyInterface|null $largeProperty
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLargeProperty(
        ?PropertyInterface $largeProperty,
        bool $recip = true
    ): HasLargePropertyInterface;

    /**
     * @param null|PropertyInterface $largeProperty
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeProperty(
        ?PropertyInterface $largeProperty = null,
        bool $recip = true
    ): HasLargePropertyInterface;
}
