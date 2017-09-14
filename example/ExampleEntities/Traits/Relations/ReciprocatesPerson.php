<?php declare(strict_types=1);


namespace Edmonds\DoctrineStaticMeta\ExampleEntities\Traits\Relations;


use Edmonds\DoctrineStaticMeta\ExampleEntities\Person;

trait ReciprocatesPerson
{
    /**
     * This method needs to set the relationship on the person to this entity
     *
     * @param Person $person
     *
     * @return $this
     */
    protected function reciprocateRelationOnPerson(Person $person)
    {
        $method = 'add'.static::getSingular();
        $person->$method($this, false);
    }

    /**
     * This method needs to remove the relationship on the person to this entity
     *
     * @param Person $person
     *
     * @return $this
     */
    protected function removeRelationOnPerson(Person $person)
    {
        $method = 'remove'.static::getSingular();
        $person->$method($this, false);
    }
}
