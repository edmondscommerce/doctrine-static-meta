<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Validation\Constraints\FieldConstraints;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\DomainName;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\DomainNameValidator;
use Generator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\DomainNameValidator
 */
class DomainNameValidatorTest extends ConstraintValidatorTestCase
{
    public const VALID = [
        'domainname.com' .
        'edmondscommerce.co.uk',
        'www.edmondscommerce.co.uk',
    ];

    public const INVALID = [
        'nodots',
        '//isurl.com',
        '999',
    ];

    public function provideValid(): Generator
    {
        foreach (self::VALID as $value) {
            yield $value => [$value];
        }
    }

    /**
     * @test
     * @small
     *
     * @param string $value
     *
     * @dataProvider provideValid
     */
    public function noViolationsForValidValues(string $value): void
    {
        $this->validator->validate($value, new DomainName());

        $this->assertNoViolation();
    }

    public function provideInvalid(): Generator
    {
        foreach (self::INVALID as $value) {
            yield $value => [$value];
        }
    }

    /**
     * @test
     * @small
     *
     * @param string $value
     *
     * @dataProvider provideInvalid
     */
    public function violationsForInvalidValues(string $value): void
    {
        $this->validator->validate($value, new DomainName());

        $this->buildViolation(sprintf(DomainName::MESSAGE, '"' . $value . '"'))
             ->setParameter('{{ value }}', '"' . $value . '"')
             ->setCode(DomainName::INVALID_DOMAIN_ERROR)
             ->assertRaised();
    }

    protected function createValidator()
    {
        return new DomainNameValidator();
    }
}
