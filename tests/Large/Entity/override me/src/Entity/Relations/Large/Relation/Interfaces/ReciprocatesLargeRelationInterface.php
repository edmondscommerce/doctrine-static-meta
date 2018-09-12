<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Relation\Interfaces;

use My\Test\Project\Entity\Interfaces\Large\RelationInterface;

interface ReciprocatesLargeRelationInterface
{
    /**
     * @param RelationInterface $largeRelation
     *
     * @return self
     */
    public function reciprocateRelationOnLargeRelation(
        RelationInterface $largeRelation
    ): self;

    /**
     * @param RelationInterface $largeRelation
     *
     * @return self
     */
    public function removeRelationOnLargeRelation(
        RelationInterface $largeRelation
    ): self;
}
