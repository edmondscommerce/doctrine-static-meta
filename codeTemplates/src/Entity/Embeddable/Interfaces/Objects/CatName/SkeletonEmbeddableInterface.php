<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Embeddable\Interfaces\Objects\CatName;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\AbstractEmbeddableObjectInterface;

interface SkeletonEmbeddableInterface extends AbstractEmbeddableObjectInterface
{
    public const EMBEDDED_PROP_PROPERTY_ONE = 'propertyOne';

    public const EMBEDDED_PROP_PROPERTY_TWO = 'propertyTwo';

    public const DEFAULT_PROPERTY_ONE = 'NOT SET';

    public const DEFAULT_PROPERTY_TWO = 'NOT SET';

    /**
     * @return string
     */
    public function getPropertyOne(): string;

    /**
     * @return string
     */
    public function getPropertyTwo(): string;
}