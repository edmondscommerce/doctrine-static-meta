<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person\YearOfBirthFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait YearOfBirthFieldTrait
{
    /**
     * @var \DateTime
     */
    private $yearOfBirth;

    protected static function metaForYearOfBirth(ClassMetadataBuilder $builder): void
    {
        $builder
            ->createField(YearOfBirthFieldInterface::PROP_YEAR_OF_BIRTH, Type::DATE_IMMUTABLE)
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
    protected static function validatorMetaForYearOfBirth(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            YearOfBirthFieldInterface::PROP_YEAR_OF_BIRTH,
            new LessThanOrEqual('today')
        );
    }

    /**
     * Get yearOfBirth
     *
     * @return \DateTime
     */
    public function getYearOfBirth(): ?\DateTimeImmutable
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
    public function setYearOfBirth(?\DateTimeImmutable $yearOfBirth): self
    {
        $this->yearOfBirth = $yearOfBirth;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(YearOfBirthFieldInterface::PROP_YEAR_OF_BIRTH);
        }

        return $this;
    }
}
