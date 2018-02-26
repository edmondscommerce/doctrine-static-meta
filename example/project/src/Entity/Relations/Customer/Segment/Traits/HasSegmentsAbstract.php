<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Segment;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\HasSegmentsInterface;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\ReciprocatesSegmentInterface;

trait HasSegmentsAbstract
{
    /**
     * @var ArrayCollection|Segment[]
     */
    private $segments;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForSegments(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasSegmentsInterface::PROPERTY_NAME_SEGMENTS, new Valid());
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForSegments(ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|Segment[]
     */
    public function getSegments(): Collection
    {
        return $this->segments;
    }

    /**
     * @param Collection|Segment[] $segments
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setSegments(Collection $segments): UsesPHPMetaDataInterface
    {
        $this->segments = $segments;

        return $this;
    }

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->segments->contains($segment)) {
            $this->segments->add($segment);
            if ($this instanceof ReciprocatesSegmentInterface && true === $recip) {
                $this->reciprocateRelationOnSegment($segment);
            }
        }

        return $this;
    }

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->segments->removeElement($segment);
        if ($this instanceof ReciprocatesSegmentInterface && true === $recip) {
            $this->removeRelationOnSegment($segment);
        }

        return $this;
    }

    /**
     * Initialise the segments property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initSegments()
    {
        $this->segments = new ArrayCollection();

        return $this;
    }
}
