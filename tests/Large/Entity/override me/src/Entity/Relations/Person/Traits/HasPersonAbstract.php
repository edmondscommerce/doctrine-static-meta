<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Interfaces\PersonInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\HasPersonInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\ReciprocatesPersonInterface;

/**
 * Trait HasPersonAbstract
 *
 * The base trait for relations to a single Person
 *
 * @package Test\Code\Generator\Entity\Relations\Person\Traits
 */
// phpcs:enable
trait HasPersonAbstract
{
    /**
     * @var Person|null
     */
    private $person;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForPerson(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForPerson(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasPersonInterface::PROPERTY_NAME_PERSON,
            new Valid()
        );
    }

    /**
     * @param null|PersonInterface $person
     * @param bool                         $recip
     *
     * @return HasPersonInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removePerson(
        ?PersonInterface $person = null,
        bool $recip = true
    ): HasPersonInterface {
        if (
            $this instanceof ReciprocatesPersonInterface
            && true === $recip
        ) {
            if (!$person instanceof EntityInterface) {
                $person = $this->getPerson();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $person->$remover($this, false);
        }

        return $this->setPerson(null, false);
    }

    /**
     * @return PersonInterface|null
     */
    public function getPerson(): ?PersonInterface
    {
        return $this->person;
    }

    /**
     * @param PersonInterface|null $person
     * @param bool                         $recip
     *
     * @return HasPersonInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setPerson(
        ?PersonInterface $person,
        bool $recip = true
    ): HasPersonInterface {

        $this->setEntityAndNotify('person', $person);
        if (
            $this instanceof ReciprocatesPersonInterface
            && true === $recip
            && null !== $person
        ) {
            $this->reciprocateRelationOnPerson($person);
        }

        return $this;
    }
}
