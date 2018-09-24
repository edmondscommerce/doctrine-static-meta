<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;

class AbstractEntityRepositoryCreator extends AbstractCreator
{
    public const FIND_NAME     = 'AbstractEntityRepository';
    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/src/Entity/Repositories/' . self::FIND_NAME . '.php';

    public function createTargetFileObject(?string $newObjectFqn = null): AbstractCreator
    {
        if (null !== $newObjectFqn) {
            throw new \RuntimeException('You should not pass a new object FQN to this creator');
        }
        $newObjectFqn = $this->projectRootNamespace . '\\Entity\\Repositories\\AbstractEntityRepository';

        return parent::createTargetFileObject($newObjectFqn);
    }
}
