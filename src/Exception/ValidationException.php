<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\Traits\RelativePathTraceTrait;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends DoctrineStaticMetaException
{
    use RelativePathTraceTrait;

    protected $entity;

    protected $errors;

    public function __construct(
        $message,
        ConstraintViolationListInterface $errors,
        IdFieldInterface $entity,
        $code = 0,
        \Exception $previous = null
    ) {
        $this->entity = $entity;

        parent::__construct($message, $code, $previous);
    }

    public function getInvalidEntity()
    {
        return $this->entity;
    }

    public function getValidationErrors()
    {
        return $this->errors;
    }
}
