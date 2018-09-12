<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\HasLargeRelationsInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\ReciprocatesLargeRelationInterface;

/**
 * Trait HasLargeRelationsAbstract
 *
 * The base trait for relations to multiple LargeRelations
 *
 * @package Test\Code\Generator\Entity\Relations\LargeRelation\Traits
 */
// phpcs:enable
trait HasLargeRelationsAbstract
{
    /**
     * @var ArrayCollection|RelationInterface[]
     */
    private $largeRelations;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForLargeRelations(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasLargeRelationsInterface::PROPERTY_NAME_LARGE_RELATIONS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForLargeRelations(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|RelationInterface[]
     */
    public function getLargeRelations(): Collection
    {
        return $this->largeRelations;
    }

    /**
     * @param Collection|RelationInterface[] $largeRelations
     *
     * @return self
     */
    public function setLargeRelations(
        Collection $largeRelations
    ): HasLargeRelationsInterface {
        $this->setEntityCollectionAndNotify(
            'largeRelations',
            $largeRelations
        );

        return $this;
    }

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
    ): HasLargeRelationsInterface {
        if ($largeRelation === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('largeRelations', $largeRelation);
        if ($this instanceof ReciprocatesLargeRelationInterface && true === $recip) {
            $this->reciprocateRelationOnLargeRelation(
                $largeRelation
            );
        }

        return $this;
    }

    /**
     * @param RelationInterface $largeRelation
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeRelation(
        RelationInterface $largeRelation,
        bool $recip = true
    ): HasLargeRelationsInterface {
        $this->removeFromEntityCollectionAndNotify('largeRelations', $largeRelation);
        if ($this instanceof ReciprocatesLargeRelationInterface && true === $recip) {
            $this->removeRelationOnLargeRelation(
                $largeRelation
            );
        }

        return $this;
    }

    /**
     * Initialise the largeRelations property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initLargeRelations()
    {
        $this->largeRelations = new ArrayCollection();

        return $this;
    }
}
