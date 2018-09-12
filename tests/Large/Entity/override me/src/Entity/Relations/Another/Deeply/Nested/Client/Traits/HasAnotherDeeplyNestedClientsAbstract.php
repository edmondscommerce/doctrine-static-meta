<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces\HasAnotherDeeplyNestedClientsInterface;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces\ReciprocatesAnotherDeeplyNestedClientInterface;

/**
 * Trait HasAnotherDeeplyNestedClientsAbstract
 *
 * The base trait for relations to multiple AnotherDeeplyNestedClients
 *
 * @package Test\Code\Generator\Entity\Relations\AnotherDeeplyNestedClient\Traits
 */
// phpcs:enable
trait HasAnotherDeeplyNestedClientsAbstract
{
    /**
     * @var ArrayCollection|ClientInterface[]
     */
    private $anotherDeeplyNestedClients;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForAnotherDeeplyNestedClients(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasAnotherDeeplyNestedClientsInterface::PROPERTY_NAME_ANOTHER_DEEPLY_NESTED_CLIENTS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForAnotherDeeplyNestedClients(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|ClientInterface[]
     */
    public function getAnotherDeeplyNestedClients(): Collection
    {
        return $this->anotherDeeplyNestedClients;
    }

    /**
     * @param Collection|ClientInterface[] $anotherDeeplyNestedClients
     *
     * @return self
     */
    public function setAnotherDeeplyNestedClients(
        Collection $anotherDeeplyNestedClients
    ): HasAnotherDeeplyNestedClientsInterface {
        $this->setEntityCollectionAndNotify(
            'anotherDeeplyNestedClients',
            $anotherDeeplyNestedClients
        );

        return $this;
    }

    /**
     * @param ClientInterface|null $anotherDeeplyNestedClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAnotherDeeplyNestedClient(
        ?ClientInterface $anotherDeeplyNestedClient,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientsInterface {
        if ($anotherDeeplyNestedClient === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('anotherDeeplyNestedClients', $anotherDeeplyNestedClient);
        if ($this instanceof ReciprocatesAnotherDeeplyNestedClientInterface && true === $recip) {
            $this->reciprocateRelationOnAnotherDeeplyNestedClient(
                $anotherDeeplyNestedClient
            );
        }

        return $this;
    }

    /**
     * @param ClientInterface $anotherDeeplyNestedClient
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAnotherDeeplyNestedClient(
        ClientInterface $anotherDeeplyNestedClient,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientsInterface {
        $this->removeFromEntityCollectionAndNotify('anotherDeeplyNestedClients', $anotherDeeplyNestedClient);
        if ($this instanceof ReciprocatesAnotherDeeplyNestedClientInterface && true === $recip) {
            $this->removeRelationOnAnotherDeeplyNestedClient(
                $anotherDeeplyNestedClient
            );
        }

        return $this;
    }

    /**
     * Initialise the anotherDeeplyNestedClients property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initAnotherDeeplyNestedClients()
    {
        $this->anotherDeeplyNestedClients = new ArrayCollection();

        return $this;
    }
}
