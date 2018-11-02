<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\Objects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\AbstractEmbeddableCreator;

class EmbeddableInterfaceCreator extends AbstractEmbeddableCreator
{
    public const FIND_NAME = 'SkeletonEmbeddableInterface';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/Embeddable/Interfaces/Objects/CatName/' . self::FIND_NAME . '.php';

    protected function getNewObjectFqn(): string
    {
        return $this->projectRootNamespace .
               '\\Entity\\Embeddable\\Interfaces\\Objects\\'
               . $this->catName . '\\' . $this->name . 'EmbeddableInterface';
    }


}