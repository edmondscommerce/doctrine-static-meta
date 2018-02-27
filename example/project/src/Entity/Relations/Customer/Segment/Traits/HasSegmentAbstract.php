<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Segment;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\HasSegmentInterface;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\ReciprocatesSegmentInterface;

trait HasSegmentAbstract
{
    /**
     * @var Segment|null
     */
    private $segment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForSegment(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForSegments(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasSegmentInterface::PROPERTY_NAME_SEGMENT, new Valid());
    }

    /**
     * @return Segment|null
     */
    public function getSegment(): ?Segment
    {
        return $this->segment;
    }

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesSegmentInterface && true === $recip) {
            $this->reciprocateRelationOnSegment($segment);
        }
        $this->segment = $segment;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeSegment(): UsesPHPMetaDataInterface
    {
        $this->segment = null;

        return $this;
    }
}
