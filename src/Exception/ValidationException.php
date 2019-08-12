<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\Traits\RelativePathTraceTrait;
use Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use ts\Reflection\ReflectionClass;
use TypeError;

class ValidationException extends DoctrineStaticMetaException
{
    use RelativePathTraceTrait;

    protected $dataObject;

    protected $errors;

    /**
     * @param ConstraintViolationListInterface|ConstraintViolationInterface[] $errors
     * @param DataTransferObjectInterface|EntityInterface                     $dataObject
     * @param int                                                             $code
     * @param Exception|null                                                 $previous
     *
     * @return ValidationException
     */
    public static function create(
        ConstraintViolationListInterface $errors,
        $dataObject,
        $code = 0,
        Exception $previous = null
    ): ValidationException {
        switch (true) {
            case $dataObject instanceof EntityInterface:
                $message = self::entityExceptionMessage($errors, $dataObject);
                break;
            case $dataObject instanceof DataTransferObjectInterface:
                $message = self::dtoExceptionMessage($errors, $dataObject);
                break;
            default:
                $message = 'Unexpected datObject passed to ValidationException: ' . print_r($dataObject, true);
        }
        $exception             = new self($message, $code, $previous);
        $exception->errors     = $errors;
        $exception->dataObject = $dataObject;

        return $exception;
    }

    private static function entityExceptionMessage(
        ConstraintViolationListInterface $errors,
        EntityInterface $entity
    ): string {
        $message =
            self::getErrorsSummary($errors, $entity::getDoctrineStaticMeta()->getReflectionClass()->getName(), $entity);
        $message .= "\n\nFull Data Object Dump:" . (new EntityDebugDumper())->dump($entity);

        return $message;
    }

    /**
     * @param ConstraintViolationListInterface|ConstraintViolationInterface[] $errors
     * @param string                                                          $className
     *
     * @param EntityData                                                      $dataObject
     *
     * @return string
     */
    private static function getErrorsSummary(
        ConstraintViolationListInterface $errors,
        string $className,
        EntityData $dataObject
    ): string {
        $message = "\nFound " . $errors->count() . " errors validating\n" . $className;
        foreach ($errors as $error) {
            $property = $error->getPropertyPath();
            $getter   = 'get' . $property;
            if (method_exists($dataObject, $getter)) {
                try {
                    $value = $dataObject->$getter();
                } catch (TypeError $e) {
                    $message .= "\n\n$property has TypeError: " . $e->getMessage();
                    continue;
                }
                if (is_object($value) === true) {
                    $value = get_class($value);
                }
                $message .= "\n\n$property [$value]: " . $error->getMessage() . ' (code: ' . $error->getCode() . ')';
                continue;
            }
            $message .= "\n\n$property: " . $error->getMessage();
        }

        return $message;
    }

    private static function dtoExceptionMessage(
        ConstraintViolationListInterface $errors,
        DataTransferObjectInterface $dto
    ): string {
        return self::getErrorsSummary($errors, (new \ReflectionClass($dto))->getShortName(), $dto);
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
