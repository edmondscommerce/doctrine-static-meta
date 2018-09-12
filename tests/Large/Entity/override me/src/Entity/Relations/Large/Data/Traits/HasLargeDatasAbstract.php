<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Data\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Large\DataInterface;
use My\Test\Project\Entity\Relations\Large\Data\Interfaces\HasLargeDatasInterface;
use My\Test\Project\Entity\Relations\Large\Data\Interfaces\ReciprocatesLargeDataInterface;

/**
 * Trait HasLargeDatasAbstract
 *
 * The base trait for relations to multiple LargeDatas
 *
 * @package Test\Code\Generator\Entity\Relations\LargeData\Traits
 */
// phpcs:enable
trait HasLargeDatasAbstract
{
    /**
     * @var ArrayCollection|DataInterface[]
     */
    private $largeDatas;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForLargeDatas(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasLargeDatasInterface::PROPERTY_NAME_LARGE_DATAS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForLargeDatas(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|DataInterface[]
     */
    public function getLargeDatas(): Collection
    {
        return $this->largeDatas;
    }

    /**
     * @param Collection|DataInterface[] $largeDatas
     *
     * @return self
     */
    public function setLargeDatas(
        Collection $largeDatas
    ): HasLargeDatasInterface {
        $this->setEntityCollectionAndNotify(
            'largeDatas',
            $largeDatas
        );

        return $this;
    }

    /**
     * @param DataInterface|null $largeData
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addLargeData(
        ?DataInterface $largeData,
        bool $recip = true
    ): HasLargeDatasInterface {
        if ($largeData === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('largeDatas', $largeData);
        if ($this instanceof ReciprocatesLargeDataInterface && true === $recip) {
            $this->reciprocateRelationOnLargeData(
                $largeData
            );
        }

        return $this;
    }

    /**
     * @param DataInterface $largeData
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeData(
        DataInterface $largeData,
        bool $recip = true
    ): HasLargeDatasInterface {
        $this->removeFromEntityCollectionAndNotify('largeDatas', $largeData);
        if ($this instanceof ReciprocatesLargeDataInterface && true === $recip) {
            $this->removeRelationOnLargeData(
                $largeData
            );
        }

        return $this;
    }

    /**
     * Initialise the largeDatas property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initLargeDatas()
    {
        $this->largeDatas = new ArrayCollection();

        return $this;
    }
}
