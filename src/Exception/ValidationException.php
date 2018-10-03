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
        switch (true) {
            case $dataObject instanceof EntityInterface:
                $message = $this->entityExceptionMessage($errors, $dataObject);
                break;
            case $dataObject instanceof DataTransferObjectInterface:
            default:
                $message = $this->dtoExceptionMessage($errors, $dataObject);
        }
        parent::__construct($message, $code, $previous);
    }

    private function entityExceptionMessage(
        ConstraintViolationListInterface $errors,
        EntityInterface $entity
    ): string {
        $message = $this->getErrorsSummary($errors, $entity::getDoctrineStaticMeta()->getShortName());
        $message .= "\n\nFull Data Object Dump:" . (new EntityDebugDumper())->dump($dataObject);

        return $message;
    }

    private function getErrorsSummary(ConstraintViolationListInterface $errors, string $className): string
    {
        $message = 'found ' . $errors->count() . ' errors validating '
                   . $className;
        foreach ($errors as $error) {
            $message .= "\n\n" . $error->getPropertyPath() . ': ' . $error->getMessage();
        }

        return $message;
    }

    private function dtoExceptionMessage(
        ConstraintViolationListInterface $errors,
        DataTransferObjectInterface $dto
    ): string {
        return $this->getErrorsSummary($errors, (new \ReflectionClass($dto))->getShortName());
    }

    public function getInvalidDataObject()
    {
        return $this->dataObject;
    }

    public function getValidationErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
