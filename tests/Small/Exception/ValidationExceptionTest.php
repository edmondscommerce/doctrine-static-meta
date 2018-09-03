<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Exception;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ValidatedEntityTrait;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ValidationExceptionTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Exception
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException
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
                use ImplementNotifyChangeTrackingPolicy, UsesPHPMetaDataTrait, ValidatedEntityTrait;

                public function __construct()
                {
                    self::getDoctrineStaticMeta()->setMetaData(new ClassMetadata('anon'));
                }

                public function getId()
                {
                    return 1;
                }

                protected static function setCustomRepositoryClass(ClassMetadataBuilder $builder)
                {
                }

            };
            throw new ValidationException($this->errors, $this->entity);
        } catch (ValidationException $e) {
            $this->exception = $e;
        }
    }

    /**
     * @test
     * @small
     * @covers ::getInvalidEntity
     */
    public function getInvalidEntity(): void
    {
        $expected = $this->entity;
        $actual   = $this->exception->getInvalidEntity();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::getValidationErrors
     */
    public function getValidationErrors(): void
    {
        $expected = $this->errors;
        $actual   = $this->exception->getValidationErrors();
        self::assertSame($expected, $actual);
    }
}
