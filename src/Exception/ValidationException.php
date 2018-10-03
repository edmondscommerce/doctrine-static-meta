<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\Traits\RelativePathTraceTrait;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends DoctrineStaticMetaException
{
    use RelativePathTraceTrait;

    protected $dataObject;

    protected $errors;

    /**
     * ValidationException constructor.
     *
     * @param ConstraintViolationListInterface|ConstraintViolationInterface[] $errors
     * @param DataTransferObjectInterface|EntityInterface                     $dataObject
     * @param int                                                             $code
     * @param \Exception|null                                                 $previous
     */
    public function __construct(
        ConstraintViolationListInterface $errors,
        $dataObject,
        $code = 0,
        \Exception $previous = null
    ) {
        $this->errors     = $errors;
        $this->dataObject = $dataObject;
        switch (true) {
            case $dataObject instanceof EntityInterface:
                $message = $this->entityExceptionMessage($errors, $dataObject);
                break;
            case $dataObject instanceof DataTransferObjectInterface:
                $message = $this->dtoExceptionMessage($errors, $dataObject);
                break;
            default:
                $message = 'Unexpected datObject passed to ValidationException: ' . print_r($dataObject, true);
        }
        parent::__construct($message, $code, $previous);
    }

    private function entityExceptionMessage(
        ConstraintViolationListInterface $errors,
        EntityInterface $entity
    ): string {
        $message = $this->getErrorsSummary($errors, $entity::getDoctrineStaticMeta()->getShortName());
        $message .= "\n\nFull Data Object Dump:" . (new EntityDebugDumper())->dump($entity);

        return $message;
    }

    private function getErrorsSummary(ConstraintViolationListInterface $errors, string $className): string
    {
        $message = 'found ' . $errors->count() . ' errors validating '
                   . $className;
        foreach ($errors as $error) {
            $property = $error->getPropertyPath();
            $getter   = 'get' . $property;
            if (method_exists($this->dataObject, $getter)) {
                $value   = $this->dataObject->$getter();
                $message .= "\n\n$property [$value]: " . $error->getMessage();
                continue;
            }
            $message .= "\n\n$property: " . $error->getMessage();
        }

        return $message;
    }

    private function dtoExceptionMessage(
        ConstraintViolationListInterface $errors,
        DataTransferObjectInterface $dto
    ): string {
        return $this->getErrorsSummary($errors, (new \ReflectionClass($dto))->getShortName());
    }

    public function getInvalidDataObject(): object
    {
        return $this->dataObject;
    }

    public function getValidationErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
