<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Relation\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;

interface HasLargeRelationInterface
{
    public const PROPERTY_NAME_LARGE_RELATION = 'largeRelation';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForLargeRelation(ClassMetadataBuilder $builder): void;

    /**
     * @return null|RelationInterface
     */
    public function getLargeRelation(): ?RelationInterface;

    /**
     * @param RelationInterface|null $largeRelation
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLargeRelation(
        ?RelationInterface $largeRelation,
        bool $recip = true
    ): HasLargeRelationInterface;

    /**
     * @param null|RelationInterface $largeRelation
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeRelation(
        ?RelationInterface $largeRelation = null,
        bool $recip = true
    ): HasLargeRelationInterface;
}
