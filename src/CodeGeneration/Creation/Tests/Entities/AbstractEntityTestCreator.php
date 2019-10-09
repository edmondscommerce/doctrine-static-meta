<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use RuntimeException;

class AbstractEntityTestCreator extends AbstractCreator
{
    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/tests/Entities/AbstractEntityTest.php';

    public function createTargetFileObject(?string $newObjectFqn = null): AbstractCreator
    {
        if (null !== $newObjectFqn) {
            throw new RuntimeException('You should not pass a new object FQN to this creator');
        }
        $newObjectFqn = $this->projectRootNamespace . '\\Entities\\AbstractEntityTest';

        return parent::createTargetFileObject($newObjectFqn);
    }
}
