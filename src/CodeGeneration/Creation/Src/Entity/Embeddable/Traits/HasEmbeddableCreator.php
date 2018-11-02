<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Traits;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\AbstractEmbeddableCreator;

class HasEmbeddableCreator extends AbstractEmbeddableCreator
{
    public const FIND_NAME = 'HasSkeletonEmbeddable';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/Embeddable/Traits/CatName/' . self::FIND_NAME . '.php';

    protected function getNewObjectFqn(): string
    {
        return $this->projectRootNamespace .
               '\\Entity\\Embeddable\\Traits\\'
               . $this->catName . '\\Has' . $this->name . 'Embeddable';
    }
}