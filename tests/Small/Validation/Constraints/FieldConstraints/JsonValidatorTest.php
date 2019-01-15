<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Validation\Constraints\FieldConstraints;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\Json;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\JsonValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class JsonValidatorTest extends ConstraintValidatorTestCase
{
    public const VALID = [
        '{"testArray": [1,2,3]}' ,
        '{"testObject":{ "firstVar": "a string", "secondVar": 123}}',
        'false',
    ];

    public const INVALID = [
        '{"malformedObject"',
        '{"missingQuotes": in a string}',
        '{"missingCommas": "between" "different":"values"}',
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
     * @param string $value
     *
     * @dataProvider provideValid
     */
    public function noViolationsForValidValues(string $value): void
    {
        $this->validator->validate($value, new Json());

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
     * @param string $value
     *
     * @dataProvider provideInvalid
     */
    public function violationsForInvalidValues(string $value): void
    {
        $this->validator->validate($value, new Json());

        $this->buildViolation(Json::MESSAGE)
             ->setParameter('{{ string }}', $value)
             ->setParameter('{{ error }}', 'Syntax error')
             ->assertRaised();
    }

    protected function createValidator()
    {
        return new JsonValidator();
    }
}
