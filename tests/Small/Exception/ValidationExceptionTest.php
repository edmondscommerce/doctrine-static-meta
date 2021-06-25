<?php

declare(strict_types=1);

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
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException
 * @small
 */
class ValidationExceptionTest extends TestCase
{
    /**
     * @var ValidationException
     */
    private ValidationException|\Exception $exception;

    private ConstraintViolationList $errors;

    private \EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface $entity;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function setup():void
    {
        try {
            $this->errors = new ConstraintViolationList();
            $this->entity = MockEntityFactory::createMockEntity();
            throw ValidationException::create($this->errors, $this->entity);
        } catch (ValidationException $e) {
            $this->exception = $e;
        }
    }

    /**
     * @test
     */
    public function getInvalidDataObject(): void
    {
        $expected = $this->entity;
        $actual   = $this->exception->getInvalidDataObject();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getValidationErrors(): void
    {
        $expected = $this->errors;
        $actual   = $this->exception->getValidationErrors();
        self::assertSame($expected, $actual);
    }
}
