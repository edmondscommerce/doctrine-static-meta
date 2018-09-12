<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Person\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\PersonInterface;

interface HasPersonInterface
{
    public const PROPERTY_NAME_PERSON = 'person';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForPerson(ClassMetadataBuilder $builder): void;

    /**
     * @return null|PersonInterface
     */
    public function getPerson(): ?PersonInterface;

    /**
     * @param PersonInterface|null $person
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setPerson(
        ?PersonInterface $person,
        bool $recip = true
    ): HasPersonInterface;

    /**
     * @param null|PersonInterface $person
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removePerson(
        ?PersonInterface $person = null,
        bool $recip = true
    ): HasPersonInterface;
}
