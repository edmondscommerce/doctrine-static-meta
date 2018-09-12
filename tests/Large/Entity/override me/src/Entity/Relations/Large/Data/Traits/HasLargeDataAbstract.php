<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Data\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Large\Data as LargeData;
use My\Test\Project\Entity\Interfaces\Large\DataInterface;
use My\Test\Project\Entity\Relations\Large\Data\Interfaces\HasLargeDataInterface;
use My\Test\Project\Entity\Relations\Large\Data\Interfaces\ReciprocatesLargeDataInterface;

/**
 * Trait HasLargeDataAbstract
 *
 * The base trait for relations to a single LargeData
 *
 * @package Test\Code\Generator\Entity\Relations\LargeData\Traits
 */
// phpcs:enable
trait HasLargeDataAbstract
{
    /**
     * @var LargeData|null
     */
    private $largeData;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForLargeData(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForLargeData(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasLargeDataInterface::PROPERTY_NAME_LARGE_DATA,
            new Valid()
        );
    }

    /**
     * @param null|DataInterface $largeData
     * @param bool                         $recip
     *
     * @return HasLargeDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeData(
        ?DataInterface $largeData = null,
        bool $recip = true
    ): HasLargeDataInterface {
        if (
            $this instanceof ReciprocatesLargeDataInterface
            && true === $recip
        ) {
            if (!$largeData instanceof EntityInterface) {
                $largeData = $this->getLargeData();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $largeData->$remover($this, false);
        }

        return $this->setLargeData(null, false);
    }

    /**
     * @return DataInterface|null
     */
    public function getLargeData(): ?DataInterface
    {
        return $this->largeData;
    }

    /**
     * @param DataInterface|null $largeData
     * @param bool                         $recip
     *
     * @return HasLargeDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLargeData(
        ?DataInterface $largeData,
        bool $recip = true
    ): HasLargeDataInterface {

        $this->setEntityAndNotify('largeData', $largeData);
        if (
            $this instanceof ReciprocatesLargeDataInterface
            && true === $recip
            && null !== $largeData
        ) {
            $this->reciprocateRelationOnLargeData($largeData);
        }

        return $this;
    }
}
