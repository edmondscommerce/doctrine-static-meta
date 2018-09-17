<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\CreatorInterface;

class EntityCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntity';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/' . CreatorInterface::SRC_FOLDER . '/Entities/'
                                 . self::FIND_NAME . '.php';
}