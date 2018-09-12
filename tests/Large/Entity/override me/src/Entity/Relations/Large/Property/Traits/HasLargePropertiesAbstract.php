<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Large\PropertyInterface;
use My\Test\Project\Entity\Relations\Large\Property\Interfaces\HasLargePropertiesInterface;
use My\Test\Project\Entity\Relations\Large\Property\Interfaces\ReciprocatesLargePropertyInterface;

/**
 * Trait HasLargePropertiesAbstract
 *
 * The base trait for relations to multiple LargeProperties
 *
 * @package Test\Code\Generator\Entity\Relations\LargeProperty\Traits
 */
// phpcs:enable
trait HasLargePropertiesAbstract
{
    /**
     * @var ArrayCollection|PropertyInterface[]
     */
    private $largeProperties;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForLargeProperties(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasLargePropertiesInterface::PROPERTY_NAME_LARGE_PROPERTIES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForLargeProperties(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|PropertyInterface[]
     */
    public function getLargeProperties(): Collection
    {
        return $this->largeProperties;
    }

    /**
     * @param Collection|PropertyInterface[] $largeProperties
     *
     * @return self
     */
    public function setLargeProperties(
        Collection $largeProperties
    ): HasLargePropertiesInterface {
        $this->setEntityCollectionAndNotify(
            'largeProperties',
            $largeProperties
        );

        return $this;
    }

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
    ): HasLargePropertiesInterface {
        if ($largeProperty === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('largeProperties', $largeProperty);
        if ($this instanceof ReciprocatesLargePropertyInterface && true === $recip) {
            $this->reciprocateRelationOnLargeProperty(
                $largeProperty
            );
        }

        return $this;
    }

    /**
     * @param PropertyInterface $largeProperty
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeProperty(
        PropertyInterface $largeProperty,
        bool $recip = true
    ): HasLargePropertiesInterface {
        $this->removeFromEntityCollectionAndNotify('largeProperties', $largeProperty);
        if ($this instanceof ReciprocatesLargePropertyInterface && true === $recip) {
            $this->removeRelationOnLargeProperty(
                $largeProperty
            );
        }

        return $this;
    }

    /**
     * Initialise the largeProperties property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initLargeProperties()
    {
        $this->largeProperties = new ArrayCollection();

        return $this;
    }
}
