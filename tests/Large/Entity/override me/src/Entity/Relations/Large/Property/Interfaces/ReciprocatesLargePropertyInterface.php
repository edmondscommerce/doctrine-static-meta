<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Property\Interfaces;

use My\Test\Project\Entity\Interfaces\Large\PropertyInterface;

interface ReciprocatesLargePropertyInterface
{
    /**
     * @param PropertyInterface $largeProperty
     *
     * @return self
     */
    public function reciprocateRelationOnLargeProperty(
        PropertyInterface $largeProperty
    ): self;

    /**
     * @param PropertyInterface $largeProperty
     *
     * @return self
     */
    public function removeRelationOnLargeProperty(
        PropertyInterface $largeProperty
    ): self;
}
