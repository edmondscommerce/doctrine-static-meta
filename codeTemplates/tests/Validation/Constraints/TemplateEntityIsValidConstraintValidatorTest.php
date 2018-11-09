<?php declare(strict_types=1);

namespace TemplateNamespace\Validation\Constraints;

use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class TemplateEntityIsValidConstraintValidatorTest extends ConstraintValidatorTestCase
{

    public function invalidValuesAreRaised($value, string $invalidPropertyPath, string $valueAsString): void
    {
        $constraint = new TemplateEntityIsValidConstraint(
            [
                'message' => 'myMessage',
            ]
        );

        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
             ->atPath($invalidPropertyPath)
             ->setParameter('{{ string }}', $valueAsString)
             ->addViolation();
    }

    protected function createValidator()
    {
        return new TemplateEntityIsValidConstraintValidator();
    }
}