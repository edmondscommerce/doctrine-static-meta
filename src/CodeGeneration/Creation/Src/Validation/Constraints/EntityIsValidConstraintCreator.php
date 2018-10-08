<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;

class EntityIsValidConstraintCreator extends AbstractCreator
{
    protected const FIND_NAME = 'TemplateEntityIsValidConstraint';

    protected const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                    '/src/Validation/Constraints/' . self::FIND_NAME . '.php';
}
