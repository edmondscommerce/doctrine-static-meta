<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\YearOfBirthFieldInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait YearOfBirthFieldTrait
{
    /**
     * @var \DateTime
     */
    private $yearOfBirth;

    protected static function getPropertyDoctrineMetaForYearOfBirth(ClassMetadataBuilder $builder): void
    {
        $builder
            ->createField(YearOfBirthFieldInterface::PROPERTY_NAME, Type::DATE_IMMUTABLE)
            ->nullable(true)
            ->build();
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForYearOfBirth(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            YearOfBirthFieldInterface::PROPERTY_NAME,
            new LessThanOrEqual('today')
        );
    }

    /**
     * Get yearOfBirth
     *
     * @return \DateTime
     */
    public function getYearOfBirth(): \DateTime
    {
        return $this->yearOfBirth;
    }

    /**
     * Set yearOfBirth
     *
     * @param \DateTime $yearOfBirth
     *
     * @return $this
     */
    public function setYearOfBirth(\DateTime $yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;

        return $this;
    }
}
