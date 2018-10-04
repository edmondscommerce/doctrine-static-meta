<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface EntityValidatorInterface
{
    public function setValidator(ValidatorInterface $validator): EntityValidatorInterface;

    public function setEntity(EntityInterface $entity): EntityValidatorInterface;

    public function isValid(): bool;

    /**
     * Validate the whole entity
     *
     * @throws ValidationException
     */
    public function validate(): void;

    /**
     * Validate a single entity property
     *
     * @param string $propertyName
     *
     * @throws ValidationException
     */
    public function validateProperty(string $propertyName): void;
}
