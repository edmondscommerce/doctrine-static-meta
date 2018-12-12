<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Initialiser;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validates either an Entity or a DataTransferObject to ensure that the data is valid as per the defined validation
 * metadata in the Entity
 */
interface EntityDataValidatorInterface
{
    public function __construct(ValidatorInterface $validator, Initialiser $initialiser);

    /**
     * Set an Entity to be validated
     *
     * @param EntityInterface $entity
     *
     * @return EntityDataValidatorInterface
     */
    public function setEntity(EntityInterface $entity): EntityDataValidatorInterface;

    /**
     * Set an data Transfer Object to be validated
     *
     * @param DataTransferObjectInterface $dto
     *
     * @return EntityDataValidatorInterface
     */
    public function setDto(DataTransferObjectInterface $dto): EntityDataValidatorInterface;

    /**
     * Simply check if data is valid
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Validate the whole data, throw exception if invalid
     *
     * @throws ValidationException
     */
    public function validate(): void;

    /**
     * Validate a single data property
     *
     * @param string $propertyName
     *
     * @throws ValidationException
     */
    public function validateProperty(string $propertyName): void;

    /**
     * Perform validation and return any error that are present
     *
     * @return ConstraintViolationListInterface
     */
    public function getErrors(): ConstraintViolationListInterface;

    /**
     * Perform validation and then return a message that details the number and the details of the errors
     *
     * Will return an empty string if there are no errors
     *
     * @return string
     */
    public function getErrorsAsString(): string;
}
