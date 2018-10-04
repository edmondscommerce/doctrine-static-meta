<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator implements EntityValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EntityInterface
     */
    protected $entity;

    public function setValidator(ValidatorInterface $validator): EntityValidatorInterface
    {
        $this->validator = $validator;

        return $this;
    }

    public function setEntity(EntityInterface $entity): EntityValidatorInterface
    {
        $this->entity = $entity;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->validator->validate($this->entity)->count() === 0;
    }

    /**
     * Validate the whole entity
     *
     * @throws ValidationException
     */
    public function validate(): void
    {
        $errors = $this->validator->validate($this->entity);
        $this->throwExceptionIfErrors($errors);
    }

    /**
     * @param ConstraintViolationListInterface $errors
     *
     * @throws ValidationException
     */
    protected function throwExceptionIfErrors(ConstraintViolationListInterface $errors): void
    {
        if ($errors->count() === 0) {
            return;
        }
        throw new ValidationException($errors, $this->entity);
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
        $errors = $this->validator->validateProperty($this->entity, $propertyName);
        $this->throwExceptionIfErrors($errors);
    }
}
