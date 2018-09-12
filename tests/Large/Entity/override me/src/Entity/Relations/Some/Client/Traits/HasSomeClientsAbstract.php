<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Some\Client\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Some\ClientInterface;
use My\Test\Project\Entity\Relations\Some\Client\Interfaces\HasSomeClientsInterface;
use My\Test\Project\Entity\Relations\Some\Client\Interfaces\ReciprocatesSomeClientInterface;

/**
 * Trait HasSomeClientsAbstract
 *
 * The base trait for relations to multiple SomeClients
 *
 * @package Test\Code\Generator\Entity\Relations\SomeClient\Traits
 */
// phpcs:enable
trait HasSomeClientsAbstract
{
    /**
     * @var ArrayCollection|ClientInterface[]
     */
    private $someClients;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForSomeClients(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasSomeClientsInterface::PROPERTY_NAME_SOME_CLIENTS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForSomeClients(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|ClientInterface[]
     */
    public function getSomeClients(): Collection
    {
        return $this->someClients;
    }

    /**
     * @param Collection|ClientInterface[] $someClients
     *
     * @return self
     */
    public function setSomeClients(
        Collection $someClients
    ): HasSomeClientsInterface {
        $this->setEntityCollectionAndNotify(
            'someClients',
            $someClients
        );

        return $this;
    }

    /**
     * @param ClientInterface|null $someClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addSomeClient(
        ?ClientInterface $someClient,
        bool $recip = true
    ): HasSomeClientsInterface {
        if ($someClient === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('someClients', $someClient);
        if ($this instanceof ReciprocatesSomeClientInterface && true === $recip) {
            $this->reciprocateRelationOnSomeClient(
                $someClient
            );
        }

        return $this;
    }

    /**
     * @param ClientInterface $someClient
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeSomeClient(
        ClientInterface $someClient,
        bool $recip = true
    ): HasSomeClientsInterface {
        $this->removeFromEntityCollectionAndNotify('someClients', $someClient);
        if ($this instanceof ReciprocatesSomeClientInterface && true === $recip) {
            $this->removeRelationOnSomeClient(
                $someClient
            );
        }

        return $this;
    }

    /**
     * Initialise the someClients property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initSomeClients()
    {
        $this->someClients = new ArrayCollection();

        return $this;
    }
}
