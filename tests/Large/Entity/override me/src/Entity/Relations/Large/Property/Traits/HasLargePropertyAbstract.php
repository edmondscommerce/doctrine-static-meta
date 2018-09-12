<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Interfaces\Large\PropertyInterface;
use My\Test\Project\Entity\Relations\Large\Property\Interfaces\HasLargePropertyInterface;
use My\Test\Project\Entity\Relations\Large\Property\Interfaces\ReciprocatesLargePropertyInterface;

/**
 * Trait HasLargePropertyAbstract
 *
 * The base trait for relations to a single LargeProperty
 *
 * @package Test\Code\Generator\Entity\Relations\LargeProperty\Traits
 */
// phpcs:enable
trait HasLargePropertyAbstract
{
    /**
     * @var LargeProperty|null
     */
    private $largeProperty;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForLargeProperty(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForLargeProperty(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasLargePropertyInterface::PROPERTY_NAME_LARGE_PROPERTY,
            new Valid()
        );
    }

    /**
     * @param null|PropertyInterface $largeProperty
     * @param bool                         $recip
     *
     * @return HasLargePropertyInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeProperty(
        ?PropertyInterface $largeProperty = null,
        bool $recip = true
    ): HasLargePropertyInterface {
        if (
            $this instanceof ReciprocatesLargePropertyInterface
            && true === $recip
        ) {
            if (!$largeProperty instanceof EntityInterface) {
                $largeProperty = $this->getLargeProperty();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $largeProperty->$remover($this, false);
        }

        return $this->setLargeProperty(null, false);
    }

    /**
     * @return PropertyInterface|null
     */
    public function getLargeProperty(): ?PropertyInterface
    {
        return $this->largeProperty;
    }

    /**
     * @param PropertyInterface|null $largeProperty
     * @param bool                         $recip
     *
     * @return HasLargePropertyInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLargeProperty(
        ?PropertyInterface $largeProperty,
        bool $recip = true
    ): HasLargePropertyInterface {

        $this->setEntityAndNotify('largeProperty', $largeProperty);
        if (
            $this instanceof ReciprocatesLargePropertyInterface
            && true === $recip
            && null !== $largeProperty
        ) {
            $this->reciprocateRelationOnLargeProperty($largeProperty);
        }

        return $this;
    }
}
