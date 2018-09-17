<?php declare(strict_types=1);

namespace TemplateNamespace\Validation\Constraints;

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
class TemplateContraint extends Constraint
{
    public const VALUE_TYPE = '(template value type)';

    public const MESSAGE = 'The value %s is not a valid ' . self::VALUE_TYPE;

    public $message = self::MESSAGE;
}