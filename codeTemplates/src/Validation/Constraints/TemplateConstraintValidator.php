<?php declare(strict_types=1);

namespace TemplateNamespace\Validation\Constraints;

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
class TemplateConstraintValidator extends ConstraintValidator
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
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        // TODO: Implement validate() method.
    }
}