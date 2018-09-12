<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\PersonInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\HasPeopleInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\ReciprocatesPersonInterface;

/**
 * Trait HasPeopleAbstract
 *
 * The base trait for relations to multiple People
 *
 * @package Test\Code\Generator\Entity\Relations\Person\Traits
 */
// phpcs:enable
trait HasPeopleAbstract
{
    /**
     * @var ArrayCollection|PersonInterface[]
     */
    private $people;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForPeople(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasPeopleInterface::PROPERTY_NAME_PEOPLE,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForPeople(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|PersonInterface[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    /**
     * @param Collection|PersonInterface[] $people
     *
     * @return self
     */
    public function setPeople(
        Collection $people
    ): HasPeopleInterface {
        $this->setEntityCollectionAndNotify(
            'people',
            $people
        );

        return $this;
    }

    /**
     * @param PersonInterface|null $person
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addPerson(
        ?PersonInterface $person,
        bool $recip = true
    ): HasPeopleInterface {
        if ($person === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('people', $person);
        if ($this instanceof ReciprocatesPersonInterface && true === $recip) {
            $this->reciprocateRelationOnPerson(
                $person
            );
        }

        return $this;
    }

    /**
     * @param PersonInterface $person
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removePerson(
        PersonInterface $person,
        bool $recip = true
    ): HasPeopleInterface {
        $this->removeFromEntityCollectionAndNotify('people', $person);
        if ($this instanceof ReciprocatesPersonInterface && true === $recip) {
            $this->removeRelationOnPerson(
                $person
            );
        }

        return $this;
    }

    /**
     * Initialise the people property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initPeople()
    {
        $this->people = new ArrayCollection();

        return $this;
    }
}
