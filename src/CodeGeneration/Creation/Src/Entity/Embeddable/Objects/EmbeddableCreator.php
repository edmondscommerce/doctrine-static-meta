<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Objects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\AbstractEmbeddableCreator;

class EmbeddableCreator extends AbstractEmbeddableCreator
{
    public const FIND_NAME = 'SkeletonEmbeddable';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/Embeddable/Objects/CatName/' . self::FIND_NAME . '.php';

    protected function getNewObjectFqn(): string
    {
        return $this->projectRootNamespace .
               '\\Entity\\Embeddable\\Objects\\'
               . $this->catName . '\\' . $this->name . 'Embeddable';
    }
}