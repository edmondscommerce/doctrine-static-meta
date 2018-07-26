<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\EntityManagerInterface;
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
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ValidationExceptionTest extends TestCase
{
    /**
     * @var ValidationException
     */
    private $exception;

    private $errors;

    private $entity;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setup()
    {
        try {
            $this->errors = new ConstraintViolationList();
            $this->entity = new class implements EntityInterface
            {
                public function getId()
                {
                    return;
                }

                public static function loadMetadata(DoctrineClassMetaData $metadata): void
                {
                    return;
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
                    return;
                }

                public function injectValidator(EntityValidatorInterface $validator)
                {
                    return;
                }

                public function isValid(): bool
                {
                    return false;
                }

                public function validate()
                {
                    return;
                }

                public function validateProperty(string $propertyName)
                {
                    return;
                }


                /**
                 * Adds a listener that wants to be notified about property changes.
                 *
                 * @param PropertyChangedListener $listener
                 *
                 * @return void
                 */
                public function addPropertyChangedListener(PropertyChangedListener $listener): void
                {
                    return;
                }

                public function getGetters(): array
                {
                    return [];
                }

                public function getSetters(): array
                {
                    return [];
                }

                public function notifyEmbeddablePrefixedProperties(
                    string $embeddablePropertyName,
                    ?string $propName = null,
                    $oldValue = null,
                    $newValue = null
                ): void {
                    return;
                }

                public function ensureMetaDataIsSet(EntityManagerInterface $entityManager): void
                {
                    return;
                }
            };
            throw new ValidationException($this->errors, $this->entity);
        } catch (ValidationException $e) {
            $this->exception = $e;
        }
    }

    public function testGetInvalidEntity(): void
    {
        $expected = $this->entity;
        $actual   = $this->exception->getInvalidEntity();
        self::assertSame($expected, $actual);
    }

    public function testGetValidationErrors(): void
    {
        $expected = $this->errors;
        $actual   = $this->exception->getValidationErrors();
        self::assertSame($expected, $actual);
    }
}
