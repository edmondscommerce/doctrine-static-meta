<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;
use  My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\HasCustomerSegmentsInterface;
use  My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\ReciprocatesCustomerSegmentInterface;

trait HasCustomerSegmentsAbstract
{
    /**
     * @var ArrayCollection|CustomerSegment[]
     */
    private $customerSegments;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCustomerSegments(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasCustomerSegmentsInterface::PROPERTY_NAME_CUSTOMER_SEGMENTS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCustomerSegments(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|CustomerSegment[]
     */
    public function getCustomerSegments(): Collection
    {
        return $this->customerSegments;
    }

    /**
     * @param Collection|CustomerSegment[] $customerSegments
     *
     * @return self
     */
    public function setCustomerSegments(Collection $customerSegments): self
    {
        $this->customerSegments = $customerSegments;

        return $this;
    }

    /**
     * @param CustomerSegment $customerSegment
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCustomerSegment(
        CustomerSegment $customerSegment,
        bool $recip = true
    ): self {
        if (!$this->customerSegments->contains($customerSegment)) {
            $this->customerSegments->add($customerSegment);
            if ($this instanceof ReciprocatesCustomerSegmentInterface && true === $recip) {
                $this->reciprocateRelationOnCustomerSegment($customerSegment);
            }
        }

        return $this;
    }

    /**
     * @param CustomerSegment $customerSegment
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCustomerSegment(
        CustomerSegment $customerSegment,
        bool $recip = true
    ): self {
        $this->customerSegments->removeElement($customerSegment);
        if ($this instanceof ReciprocatesCustomerSegmentInterface && true === $recip) {
            $this->removeRelationOnCustomerSegment($customerSegment);
        }

        return $this;
    }

    /**
     * Initialise the customerSegments property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initCustomerSegments()
    {
        $this->customerSegments = new ArrayCollection();

        return $this;
    }
}
