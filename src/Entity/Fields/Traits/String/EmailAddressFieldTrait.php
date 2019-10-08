<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
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
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    protected static function validatorMetaForPropertyEmailAddress(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS,
            [
                new Email(),
                new Length(
                    [
                        'min' => 0,
                        'max' => Database::MAX_VARCHAR_LENGTH,
                    ]
                ),
            ]
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
    private function setEmailAddress(?string $emailAddress): self
    {
        $this->updatePropertyValue(
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS,
            $emailAddress
        );

        return $this;
    }
}
