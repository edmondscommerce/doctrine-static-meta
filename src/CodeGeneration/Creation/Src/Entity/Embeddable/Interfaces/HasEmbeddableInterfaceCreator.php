<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\AbstractEmbeddableCreator;

class HasEmbeddableInterfaceCreator extends AbstractEmbeddableCreator
{
    public const FIND_NAME = 'HasSkeletonEmbeddableInterface';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/Embeddable/Interfaces/CatName/' . self::FIND_NAME . '.php';

    protected function getNewObjectFqn(): string
    {
        return $this->projectRootNamespace .
               '\\Entity\\Embeddable\\Interfaces\\'
               . $this->catName . '\\Has' . $this->name . 'EmbeddableInterface';
    }
}
