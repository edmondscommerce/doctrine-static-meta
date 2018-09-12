<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Data\Interfaces;

use My\Test\Project\Entity\Interfaces\Large\DataInterface;

interface ReciprocatesLargeDataInterface
{
    /**
     * @param DataInterface $largeData
     *
     * @return self
     */
    public function reciprocateRelationOnLargeData(
        DataInterface $largeData
    ): self;

    /**
     * @param DataInterface $largeData
     *
     * @return self
     */
    public function removeRelationOnLargeData(
        DataInterface $largeData
    ): self;
}
