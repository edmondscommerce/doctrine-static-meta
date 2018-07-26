<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait EmailAddressFieldTrait
{

    /**
     * @var string|null
     */
    private $emailAddress;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForEmailAddress(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [EmailAddressFieldInterface::PROP_EMAIL_ADDRESS],
            $builder,
            EmailAddressFieldInterface::DEFAULT_EMAIL_ADDRESS,
            true
        );
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForEmailAddress(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS,
            new Email()
        );
    }

    /**
     * @return string|null
     */
    public function getEmailAddress(): ?string
    {
        if (null === $this->emailAddress) {
            return EmailAddressFieldInterface::DEFAULT_EMAIL_ADDRESS;
        }

        return $this->emailAddress;
    }

    /**
     * @param string|null $emailAddress
     *
     * @return self
     */
    public function setEmailAddress(?string $emailAddress): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS,
            $emailAddress
        );

        return $this;
    }
}
