<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person\EmailFieldInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait EmailFieldTrait
{

    /**
     * @var string|null
     */
    private $email;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForEmail(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleStringFields(
            [EmailFieldInterface::PROP_EMAIL],
            $builder,
            true
        );
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForEmail(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            EmailFieldInterface::PROP_EMAIL,
            new Email()
        );
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this|EmailFieldInterface
     */
    public function setEmail(?string $email)
    {
        $this->email = $email;
        if ($this instanceof ValidateInterface) {
            $this->setNeedsValidating();
        }
        return $this;
    }
}
