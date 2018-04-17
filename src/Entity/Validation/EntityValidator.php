<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ValidateInterface
     */
    protected $entity;

    public function setValidator(ValidatorInterface $validator): EntityValidator
    {
        $this->validator = $validator;

        return $this;
    }

    public function setEntity(ValidateInterface $entity): EntityValidator
    {
        $this->entity = $entity;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->validate()->count() === 0;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function validate(): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->entity);
    }
}
