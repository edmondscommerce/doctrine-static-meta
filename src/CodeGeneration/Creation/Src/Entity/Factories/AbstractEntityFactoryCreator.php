<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use RuntimeException;

class AbstractEntityFactoryCreator extends AbstractCreator
{
    public const FIND_NAME     = 'AbstractEntityFactory';
    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . 'src/Entity/Factories/' . self::FIND_NAME . '.php';

    public function createTargetFileObject(?string $newObjectFqn = null): AbstractCreator
    {
        if (null !== $newObjectFqn) {
            throw new RuntimeException('You should not pass a new object FQN to this creator');
        }
        $newObjectFqn = $this->projectRootNamespace . '\\Entity\\Factories\\AbstractEntityFactory';

        return parent::createTargetFileObject($newObjectFqn);
    }
}
