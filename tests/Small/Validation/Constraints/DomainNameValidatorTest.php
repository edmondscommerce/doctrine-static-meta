<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\DomainName;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\DomainNameValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

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

    public function provideValid(): \Generator
    {
        foreach (self::VALID as $value) {
            yield $value => [$value];
        }
    }

    /**
     * @test
     * @small
     *
     * @param $value
     *
     * @dataProvider provideValid
     */
    public function noViolationsForValidValues($value)
    {
        $this->validator->validate($value, new DomainName());

        $this->assertNoViolation();
    }

    public function provideInvalid(): \Generator
    {
        foreach (self::INVALID as $value) {
            yield $value => [$value];
        }
    }

    /**
     * @test
     * @small
     *
     * @param $value
     *
     * @dataProvider provideInvalid
     */
    public function violationsForInvalidValues($value)
    {
        $this->validator->validate($value, new DomainName());

        $this->buildViolation(DomainName::MESSAGE)
             ->setParameter('{{ value }}', '"' . $value . '"')
             ->setCode(DomainName::INVALID_DOMAIN_ERROR)
             ->assertRaised();
    }

    protected function createValidator()
    {
        return new DomainNameValidator();
    }
}