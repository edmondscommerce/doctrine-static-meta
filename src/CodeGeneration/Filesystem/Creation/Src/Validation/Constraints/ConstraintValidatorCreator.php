<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\AbstractCreator;

class ConstraintValidatorCreator extends AbstractCreator
{
    protected const FIND_NAME = 'TemplateConstraintValidator';

    protected const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                    '/src/Validation/Constraints/' . self::FIND_NAME . '.php';
}