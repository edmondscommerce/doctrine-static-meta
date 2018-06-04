<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetaData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

/**
 * Class ValidationExceptionTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Exception
 * @SupressWarnings(PHPMD.UnusedLocalVariable)
 */
class ValidationExceptionTest extends TestCase
{
    /**
     * @var ValidationException
     */
    private $exception;

    private $errors;

    private $entity;

    public function setup()
    {
        try {
            $this->errors = new ConstraintViolationList();
            $this->entity = new class implements EntityInterface
            {
                public function getId()
                {
                    // TODO: Implement getId() method.
                }

                public static function loadMetadata(DoctrineClassMetaData $metadata): void
                {
                    // TODO: Implement loadMetadata() method.
                }

                public static function getPlural(): string
                {
                    return '';
                }

                public static function getSingular(): string
                {
                    return '';
                }

                public static function getIdField(): string
                {
                    return '';
                }

                public function getShortName(): string
                {
                    return '';
                }

                public function __toString(): string
                {
                    return '';
                }

                public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
                {
                    // TODO: Implement loadValidatorMetaData() method.
                }

                public function injectValidator(EntityValidatorInterface $validator)
                {
                    // TODO: Implement injectValidator() method.
                }

                public function isValid(): bool
                {
                    return false;
                }

                public function validate()
                {
                    // TODO: Implement validate() method.
                }

                public function validateProperty(string $propertyName)
                {
                    // TODO: Implement validateProperty() method.
                }


            };
            throw new ValidationException($this->errors, $this->entity);
        } catch (ValidationException $e) {
            $this->exception = $e;
        }
    }

    public function testGetInvalidEntity()
    {
        $expected = $this->entity;
        $actual   = $this->exception->getInvalidEntity();
        $this->assertSame($expected, $actual);
    }

    public function testGetValidationErrors()
    {
        $expected = $this->errors;
        $actual   = $this->exception->getValidationErrors();
        $this->assertSame($expected, $actual);
    }
}
