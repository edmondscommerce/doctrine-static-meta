<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Some\Client\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Interfaces\Some\ClientInterface;
use My\Test\Project\Entity\Relations\Some\Client\Interfaces\HasSomeClientInterface;
use My\Test\Project\Entity\Relations\Some\Client\Interfaces\ReciprocatesSomeClientInterface;

/**
 * Trait HasSomeClientAbstract
 *
 * The base trait for relations to a single SomeClient
 *
 * @package Test\Code\Generator\Entity\Relations\SomeClient\Traits
 */
// phpcs:enable
trait HasSomeClientAbstract
{
    /**
     * @var SomeClient|null
     */
    private $someClient;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForSomeClient(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForSomeClient(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasSomeClientInterface::PROPERTY_NAME_SOME_CLIENT,
            new Valid()
        );
    }

    /**
     * @param null|ClientInterface $someClient
     * @param bool                         $recip
     *
     * @return HasSomeClientInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeSomeClient(
        ?ClientInterface $someClient = null,
        bool $recip = true
    ): HasSomeClientInterface {
        if (
            $this instanceof ReciprocatesSomeClientInterface
            && true === $recip
        ) {
            if (!$someClient instanceof EntityInterface) {
                $someClient = $this->getSomeClient();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $someClient->$remover($this, false);
        }

        return $this->setSomeClient(null, false);
    }

    /**
     * @return ClientInterface|null
     */
    public function getSomeClient(): ?ClientInterface
    {
        return $this->someClient;
    }

    /**
     * @param ClientInterface|null $someClient
     * @param bool                         $recip
     *
     * @return HasSomeClientInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setSomeClient(
        ?ClientInterface $someClient,
        bool $recip = true
    ): HasSomeClientInterface {

        $this->setEntityAndNotify('someClient', $someClient);
        if (
            $this instanceof ReciprocatesSomeClientInterface
            && true === $recip
            && null !== $someClient
        ) {
            $this->reciprocateRelationOnSomeClient($someClient);
        }

        return $this;
    }
}
