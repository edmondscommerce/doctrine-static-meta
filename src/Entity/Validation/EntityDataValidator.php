<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityDataValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityDataValidator implements EntityDataValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EntityInterface|DataTransferObjectInterface
     */
    protected $dataObject;

    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * Set an Entity to be validated
     *
     * @param EntityInterface $entity
     *
     * @return EntityDataValidatorInterface
     */
    public function setEntity(EntityInterface $entity): EntityDataValidatorInterface
    {
        $this->dataObject = $entity;

        return $this;
    }

    /**
     * Set an data Transfer Object to be validated
     *
     * @param DataTransferObjectInterface $dto
     *
     * @return EntityDataValidatorInterface
     */
    public function setDto(DataTransferObjectInterface $dto): EntityDataValidatorInterface
    {
        $this->dataObject = $dto;

        return $this;
    }

    /**
     * Simply check for validity as a boolean, with no error reporting
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->getErrors()->count() === 0;
    }

    /**
     * Perform validation and return any error that are present
     *
     * @return ConstraintViolationListInterface
     */
    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->dataObject);
    }

    /**
     * Perform validation and then return a message that details the number and the details of the errors
     *
     * Will return an empty string if there are no errors
     *
     * @return string
     */
    public function getErrorsAsString(): string
    {
        $errors = $this->getErrors();
        if (0 === $errors->count()) {
            return '';
        }
        $message = 'found ' . $errors->count() . ' errors validating '
                   . \get_class($this->dataObject);
        foreach ($errors as $error) {
            $message .= "\n\n" . $error->getPropertyPath() . ': ' . $error->getMessage();
        }

        return $message;
    }

    /**
     * Validate the whole entity and provide a verbose error report
     *
     * @throws ValidationException
     */
    public function validate(): void
    {
        $errors = $this->getErrors();
        $this->throwExceptionIfErrors($errors);
    }

    /**
     * @param ConstraintViolationListInterface $errors
     *
     * @throws ValidationException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function throwExceptionIfErrors(ConstraintViolationListInterface $errors): void
    {
        if (0 === $errors->count()) {
            return;
        }
        throw ValidationException::create($errors, $this->dataObject);
    }

    /**
     * Validate a single entity property
     *
     * @param string $propertyName
     *
     * @throws ValidationException
     */
    public function validateProperty(string $propertyName): void
    {
        $errors = $this->validator->validateProperty($this->dataObject, $propertyName);
        $this->throwExceptionIfErrors($errors);
    }
}
