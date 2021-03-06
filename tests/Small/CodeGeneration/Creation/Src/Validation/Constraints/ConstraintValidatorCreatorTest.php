<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Validation\Constraints;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;
// phpcs:enable
/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator
 */
class ConstraintValidatorCreatorTest extends TestCase
{
    /**
     * @test
     * @small
     */
    public function itCanCreateANewFileObjectWithTheCorrectContent(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Validation\\Constraints\\IsBlueConstraintValidator';
        $file         = $this->getConstraintValidatorCreator()
                             ->createTargetFileObject($newObjectFqn)
                             ->getTargetFile();
        $expected     = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * This is a template constraint validator.
 *
 * The Constraint Validator is in charge of actually doing the validation.
 *
 * See the comment on the constructor about brining in dependencies if required to perform the validation
 *
 * @see https://symfony.com/doc/2.6/cookbook/validation/custom_constraint.html
 *
 * Suggest using the `./bin/doctrine dsm:override:create` command to create a new
 * override straight after you generate the file.
 *
 * Then you can edit it and update your override as required using
 * `./bin/doctrine dsm:overrides:update -a fromProject`
 *
 * And then reapply after a new build using
 * `./bin/doctrine dsm:overrides:update -a toProject`
 *
 */
class IsBlueConstraintValidator extends ConstraintValidator
{

    /**
     * If your validator requires dependencies to be injected, then simply declare them here.
     *
     * You also need to add this class to your list of Services in your DI container
     *
     * Finally you need to ensure that your container is configured to use the ContainerConstraintValidatorFactory
     *
     * @see \EdmondsCommerce\DoctrineStaticMeta\Container::configureValidationComponents
     */
    public function __construct()
    {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $propertyValue The value that should be validated
     * @param Constraint $constraint    The constraint for the validation
     */
    public function validate($propertyValue, Constraint $constraint)
    {
        // Implement your validation logic.
        // Return early if the value is valid
        // For example:
        if ($propertyValue === 'isValid') {
            return;
        }

        // Finally, if not valid, add the violation
        $this->context->buildViolation($constraint->message)
                      ->setParameter('{{ string }}', $propertyValue)
                      ->addViolation();
    }
}
PHP;

        $actual = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getConstraintValidatorCreator(): PropertyConstraintValidatorCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new PropertyConstraintValidatorCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
    }
}
