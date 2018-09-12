<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Person\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\PersonInterface;

interface HasPeopleInterface
{
    public const PROPERTY_NAME_PEOPLE = 'people';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForPeople(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|PersonInterface[]
     */
    public function getPeople(): Collection;

    /**
     * @param Collection|PersonInterface[] $people
     *
     * @return self
     */
    public function setPeople(Collection $people): self;

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
    ): HasPeopleInterface;

    /**
     * @param PersonInterface $person
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removePerson(
        PersonInterface $person,
        bool $recip = true
    ): HasPeopleInterface;

}
