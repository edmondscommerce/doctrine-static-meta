<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Property\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Large\PropertyInterface;

interface HasLargePropertiesInterface
{
    public const PROPERTY_NAME_LARGE_PROPERTIES = 'largeProperties';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForLargeProperties(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|PropertyInterface[]
     */
    public function getLargeProperties(): Collection;

    /**
     * @param Collection|PropertyInterface[] $largeProperties
     *
     * @return self
     */
    public function setLargeProperties(Collection $largeProperties): self;

    /**
     * @param PropertyInterface|null $largeProperty
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addLargeProperty(
        ?PropertyInterface $largeProperty,
        bool $recip = true
    ): HasLargePropertiesInterface;

    /**
     * @param PropertyInterface $largeProperty
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeProperty(
        PropertyInterface $largeProperty,
        bool $recip = true
    ): HasLargePropertiesInterface;

}
