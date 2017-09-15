<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Traits\Relations;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Person;

trait HasPeople
{
    use ReciprocatesPerson;

    /**
     * @var ArrayCollection|Person[]
     */
    private $people;

    protected static function getPropertyMetaForPeople(ClassMetadataBuilder $builder)
    {
        $builder->addOwningManyToMany(
            Person::getPlural(),
            Person::class,
            static::getPlural()
        );
    }

    /**
     * @return ArrayCollection|Person[]
     */
    public function getPeople(): ArrayCollection
    {
        return $this->people;
    }

    /**
     * @param ArrayCollection $people
     *
     * @return $this
     */
    public function setPeople(ArrayCollection $people)
    {
        $this->people = $people;

        return $this;
    }

    /**
     * @param Person $person
     * @param bool   $recip
     *
     * @return $this
     */
    public function addPerson(Person $person, bool $recip = true)
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            if (true === $recip) {
                $this->reciprocateRelationOnPerson($person);
            }
        }

        return $this;
    }

    /**
     * @param Person $person
     * @param bool   $recip
     *
     * @return $this
     */
    public function removePerson(Person $person, bool $recip = true)
    {

        $this->people->removeElement($person);
        if (true === $recip) {
            $this->removeRelationOnPerson($person);
        }

        return $this;
    }

    private function initPeople()
    {
        $this->people = new ArrayCollection();

        return $this;
    }

}
