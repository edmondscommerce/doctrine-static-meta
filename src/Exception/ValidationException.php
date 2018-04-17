<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\Traits\RelativePathTraceTrait;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends DoctrineStaticMetaException
{
    use RelativePathTraceTrait;

    protected $entity;

    protected $errors;

    /**
     * ValidationException constructor.
     *
     * @param ConstraintViolationListInterface|ConstraintViolationInterface[] $errors
     * @param EntityInterface                                                 $entity
     * @param int                                                             $code
     * @param \Exception|null                                                 $previous
     */
    public function __construct(
        ConstraintViolationListInterface $errors,
        EntityInterface $entity,
        $code = 0,
        \Exception $previous = null
    ) {
        $this->entity = $entity;
        $this->errors = $errors;

        $message = 'found '.$errors->count().' errors validating entity '.$entity->getShortName();
        foreach ($errors as $error) {
            $message .= "\n".$error->getMessage();
        }

        parent::__construct($message, $code, $previous);
    }

    public function getInvalidEntity(): EntityInterface
    {
        return $this->entity;
    }

    public function getValidationErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
