<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Exception;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\MockEntityFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ValidationExceptionTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Exception
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException
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
            $this->entity = MockEntityFactory::createMockEntity();
            throw new ValidationException($this->errors, $this->entity);
        } catch (ValidationException $e) {
            $this->exception = $e;
        }
    }

    /**
     * @test
     * @small
     *      */
    public function getInvalidEntity(): void
    {
        $expected = $this->entity;
        $actual   = $this->exception->getInvalidDataObject();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     *      */
    public function getValidationErrors(): void
    {
        $expected = $this->errors;
        $actual   = $this->exception->getValidationErrors();
        self::assertSame($expected, $actual);
    }
}
