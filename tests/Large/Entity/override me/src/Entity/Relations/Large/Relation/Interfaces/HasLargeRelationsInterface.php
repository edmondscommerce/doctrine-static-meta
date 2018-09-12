<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Relation\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;

interface HasLargeRelationsInterface
{
    public const PROPERTY_NAME_LARGE_RELATIONS = 'largeRelations';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForLargeRelations(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|RelationInterface[]
     */
    public function getLargeRelations(): Collection;

    /**
     * @param Collection|RelationInterface[] $largeRelations
     *
     * @return self
     */
    public function setLargeRelations(Collection $largeRelations): self;

    /**
     * @param RelationInterface|null $largeRelation
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addLargeRelation(
        ?RelationInterface $largeRelation,
        bool $recip = true
    ): HasLargeRelationsInterface;

    /**
     * @param RelationInterface $largeRelation
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeRelation(
        RelationInterface $largeRelation,
        bool $recip = true
    ): HasLargeRelationsInterface;

}
