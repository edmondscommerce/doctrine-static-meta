<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Person\Interfaces;

use My\Test\Project\Entity\Interfaces\PersonInterface;

interface ReciprocatesPersonInterface
{
    /**
     * @param PersonInterface $person
     *
     * @return self
     */
    public function reciprocateRelationOnPerson(
        PersonInterface $person
    ): self;

    /**
     * @param PersonInterface $person
     *
     * @return self
     */
    public function removeRelationOnPerson(
        PersonInterface $person
    ): self;
}
