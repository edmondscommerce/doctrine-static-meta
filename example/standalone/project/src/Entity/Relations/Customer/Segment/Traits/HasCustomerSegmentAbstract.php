<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\HasCustomerSegmentInterface;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\ReciprocatesCustomerSegmentInterface;

trait HasCustomerSegmentAbstract
{
    /**
     * @var CustomerSegment|null
     */
    private $customerSegment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCustomerSegment(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCustomerSegment(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasCustomerSegmentInterface::PROPERTY_NAME_CUSTOMER_SEGMENT,
            new Valid()
        );
    }

    /**
     * @return CustomerSegment|null
     */
    public function getCustomerSegment(): ?CustomerSegment
    {
        return $this->customerSegment;
    }

    /**
     * @param CustomerSegment|null $customerSegment
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCustomerSegment(
        ?CustomerSegment $customerSegment,
        bool $recip = true
    ): HasCustomerSegmentInterface {

        $this->customerSegment = $customerSegment;
        if ($this instanceof ReciprocatesCustomerSegmentInterface
            && true === $recip
            && null !== $customerSegment
        ) {
            $this->reciprocateRelationOnCustomerSegment($customerSegment);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function removeCustomerSegment(): HasCustomerSegmentInterface
    {
        $this->customerSegment = null;

        return $this;
    }
}