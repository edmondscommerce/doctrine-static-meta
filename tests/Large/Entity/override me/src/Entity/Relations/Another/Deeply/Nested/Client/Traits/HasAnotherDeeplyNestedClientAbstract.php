<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Another\Deeply\Nested\Client as AnotherDeeplyNestedClient;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces\HasAnotherDeeplyNestedClientInterface;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces\ReciprocatesAnotherDeeplyNestedClientInterface;

/**
 * Trait HasAnotherDeeplyNestedClientAbstract
 *
 * The base trait for relations to a single AnotherDeeplyNestedClient
 *
 * @package Test\Code\Generator\Entity\Relations\AnotherDeeplyNestedClient\Traits
 */
// phpcs:enable
trait HasAnotherDeeplyNestedClientAbstract
{
    /**
     * @var AnotherDeeplyNestedClient|null
     */
    private $anotherDeeplyNestedClient;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForAnotherDeeplyNestedClient(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForAnotherDeeplyNestedClient(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasAnotherDeeplyNestedClientInterface::PROPERTY_NAME_ANOTHER_DEEPLY_NESTED_CLIENT,
            new Valid()
        );
    }

    /**
     * @param null|ClientInterface $anotherDeeplyNestedClient
     * @param bool                         $recip
     *
     * @return HasAnotherDeeplyNestedClientInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAnotherDeeplyNestedClient(
        ?ClientInterface $anotherDeeplyNestedClient = null,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientInterface {
        if (
            $this instanceof ReciprocatesAnotherDeeplyNestedClientInterface
            && true === $recip
        ) {
            if (!$anotherDeeplyNestedClient instanceof EntityInterface) {
                $anotherDeeplyNestedClient = $this->getAnotherDeeplyNestedClient();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $anotherDeeplyNestedClient->$remover($this, false);
        }

        return $this->setAnotherDeeplyNestedClient(null, false);
    }

    /**
     * @return ClientInterface|null
     */
    public function getAnotherDeeplyNestedClient(): ?ClientInterface
    {
        return $this->anotherDeeplyNestedClient;
    }

    /**
     * @param ClientInterface|null $anotherDeeplyNestedClient
     * @param bool                         $recip
     *
     * @return HasAnotherDeeplyNestedClientInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAnotherDeeplyNestedClient(
        ?ClientInterface $anotherDeeplyNestedClient,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientInterface {

        $this->setEntityAndNotify('anotherDeeplyNestedClient', $anotherDeeplyNestedClient);
        if (
            $this instanceof ReciprocatesAnotherDeeplyNestedClientInterface
            && true === $recip
            && null !== $anotherDeeplyNestedClient
        ) {
            $this->reciprocateRelationOnAnotherDeeplyNestedClient($anotherDeeplyNestedClient);
        }

        return $this;
    }
}
