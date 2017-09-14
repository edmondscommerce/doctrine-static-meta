<?php declare(strict_types=1);


namespace Edmonds\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties;

use Edmonds\DoctrineStaticMeta\Properties\PhoneNumber;

trait ReciprocatesPhoneNumber
{
    /**
     * This method needs to set the relationship on the phoneNumber to this entity
     * @param PhoneNumber $phoneNumber
     * @return $this
     */
    protected function reciprocateRelationOnPhoneNumber(PhoneNumber $phoneNumber)
    {
        $method = 'add'.static::getSingular();
        $phoneNumber->$method($this, false);
    }

    /**
     * This method needs to remove the relationship on the phoneNumber to this entity
     * @param PhoneNumber $phoneNumber
     * @return $this
     */
    protected function removeRelationOnPhoneNumber(PhoneNumber $phoneNumber)
    {
        $method = 'remove'.static::getSingular();
        $phoneNumber->$method($this, false);
    }
}
