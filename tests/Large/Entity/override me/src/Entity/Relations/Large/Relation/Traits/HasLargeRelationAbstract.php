<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\HasLargeRelationInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\ReciprocatesLargeRelationInterface;

/**
 * Trait HasLargeRelationAbstract
 *
 * The base trait for relations to a single LargeRelation
 *
 * @package Test\Code\Generator\Entity\Relations\LargeRelation\Traits
 */
// phpcs:enable
trait HasLargeRelationAbstract
{
    /**
     * @var LargeRelation|null
     */
    private $largeRelation;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForLargeRelation(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForLargeRelation(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasLargeRelationInterface::PROPERTY_NAME_LARGE_RELATION,
            new Valid()
        );
    }

    /**
     * @param null|RelationInterface $largeRelation
     * @param bool                         $recip
     *
     * @return HasLargeRelationInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeRelation(
        ?RelationInterface $largeRelation = null,
        bool $recip = true
    ): HasLargeRelationInterface {
        if (
            $this instanceof ReciprocatesLargeRelationInterface
            && true === $recip
        ) {
            if (!$largeRelation instanceof EntityInterface) {
                $largeRelation = $this->getLargeRelation();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $largeRelation->$remover($this, false);
        }

        return $this->setLargeRelation(null, false);
    }

    /**
     * @return RelationInterface|null
     */
    public function getLargeRelation(): ?RelationInterface
    {
        return $this->largeRelation;
    }

    /**
     * @param RelationInterface|null $largeRelation
     * @param bool                         $recip
     *
     * @return HasLargeRelationInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLargeRelation(
        ?RelationInterface $largeRelation,
        bool $recip = true
    ): HasLargeRelationInterface {

        $this->setEntityAndNotify('largeRelation', $largeRelation);
        if (
            $this instanceof ReciprocatesLargeRelationInterface
            && true === $recip
            && null !== $largeRelation
        ) {
            $this->reciprocateRelationOnLargeRelation($largeRelation);
        }

        return $this;
    }
}
