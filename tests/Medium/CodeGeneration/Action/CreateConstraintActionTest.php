<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction
 * @medium
 */
class CreateConstraintActionTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . AbstractTest::TEST_TYPE_MEDIUM
                            . '/CreateConstraintActionTest';

    private const CONSTRAINTS_PATH = self::WORK_DIR . '/src/Validation/Constraints';

    private const PROPERTY_CONSTRAINT = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * This is a template constraint. The Constraint does very little other than specify a message and imply a
 * ConstraintValidator with the same FQN as this class, but with `Validator` appended.
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
class IsGreenConstraint extends Constraint
{
    public const VALUE_TYPE = '(template value type)';

    public const MESSAGE = 'The value {{ string }} is not a valid ' . self::VALUE_TYPE;

    public $message = self::MESSAGE;


    /**
     * Returns whether the constraint can be put onto classes, properties or
     * both.
     *
     * This method should return one or more of the constants
     * self::CLASS_CONSTRAINT and self::PROPERTY_CONSTRAINT.
     *
     * @return string|array One or more constant values
     */
    public function getTargets(): string
    {
        self::PROPERTY_CONSTRAINT;
    }
}
PHP;

    private const PROPERTY_CONSTRAINT_VALIDATOR = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Validation\Constraints;

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
class IsGreenConstraintValidator extends ConstraintValidator
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

    private const ENTITY_CONSTRAINT           = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * This is a template Entity constraint.
 *
 * The Constraint does very little other than specify a message and imply a
 * ConstraintValidator with the same FQN as this class, but with `Validator` appended.
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
class IsGreenConstraint extends Constraint
{
    public const VALUE_TYPE = 'TemplateEntity';

    public const MESSAGE = 'The value {{ string }} is not a valid ' . self::VALUE_TYPE;

    public $message = self::MESSAGE;


    /**
     * Returns whether the constraint can be put onto classes, properties or
     * both.
     *
     * This method should return one or more of the constants
     * self::CLASS_CONSTRAINT and self::PROPERTY_CONSTRAINT.
     *
     * @return string|array One or more constant values
     */
    public function getTargets(): string
    {
        self::CLASS_CONSTRAINT;
    }
}
PHP;
    private const ENTITY_CONSTRAINT_VALIDATOR = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
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
class IsGreenConstraintValidator extends ConstraintValidator
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
     * @see https://symfony.com/doc/current/validation/custom_constraint.html#class-constraint-validator
     *
     * @param EntityInterface $entity     The Entity that should be validated
     * @param Constraint      $constraint The constraint for the validation
     */
    public function validate($entity, Constraint $constraint)
    {
        // Implement your validation logic.
        // Return early if the value is valid
        // For example:
        if ($entity->getFoo() > $entity->getBar()) {
            return;
        }

        $invalidPropertyPath = 'foo';

        // Finally, if not valid, add the violation
        $this->context->buildViolation($constraint->message)
                      ->atPath($invalidPropertyPath)
                      ->setParameter('{{ string }}', $entity->getFoo())
                      ->addViolation();
    }
}
PHP
    ;

    public function setup()
    {
        parent::setUp();
        if (is_dir(self::WORK_DIR . '/src/Validation')) {
            $this->getFileSystem()->remove(self::WORK_DIR . '/src/Validation');
        }
    }


    /**
     * @test
     */
    public function itCanCreateConstraintsWithoutSuffix(): void
    {
        $this->getAction()
             ->setConstraintShortName('IsGreen')
             ->run();
        self::assertSame(
            self::PROPERTY_CONSTRAINT,
            \ts\file_get_contents(self::CONSTRAINTS_PATH . '/IsGreenConstraint.php')
        );
        self::assertSame(
            self::PROPERTY_CONSTRAINT_VALIDATOR,
            \ts\file_get_contents(self::CONSTRAINTS_PATH . '/IsGreenConstraintValidator.php')
        );
    }

    private function getAction(): CreateConstraintAction
    {
        /**
         * @var CreateConstraintAction $action
         */
        $action = $this->container->get(CreateConstraintAction::class)
                                  ->setProjectRootDirectory(self::WORK_DIR)
                                  ->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE);

        return $action;
    }

    /**
     * @test
     */
    public function itCanCreateEntityConstraintsWithoutSuffix(): void
    {
        $this->getAction()
             ->setConstraintShortName('IsGreen')
             ->setPropertyOrEntity(CreateConstraintAction::OPTION_ENTITY)
             ->run();
        self::assertSame(
            self::ENTITY_CONSTRAINT,
            \ts\file_get_contents(self::CONSTRAINTS_PATH . '/IsGreenConstraint.php')
        );
        self::assertSame(
            self::ENTITY_CONSTRAINT_VALIDATOR,
            \ts\file_get_contents(self::CONSTRAINTS_PATH . '/IsGreenConstraintValidator.php')
        );
    }

    /**
     * @test
     */
    public function itCanCreateConstraintsWithSuffix(): void
    {
        $this->getAction()
             ->setConstraintShortName('IsGreenConstraint')
             ->run();
        self::assertSame(
            self::PROPERTY_CONSTRAINT,
            \ts\file_get_contents(self::CONSTRAINTS_PATH . '/IsGreenConstraint.php')
        );
        self::assertSame(
            self::PROPERTY_CONSTRAINT_VALIDATOR,
            \ts\file_get_contents(self::CONSTRAINTS_PATH . '/IsGreenConstraintValidator.php')
        );
    }

    public function isThrowsAnExceptionIfConstraintShortNameNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectException('You must call setContraintShortname before calling run');
        $this->getAction()->run();
    }
}
