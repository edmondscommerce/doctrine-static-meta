<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Embeddable\FakerData\CatName;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use TemplateNamespace\Entity\Embeddable\Objects\CatName\SkeletonEmbeddable;

class SkeletonEmbeddableFakerData extends AbstractFakerDataProvider
{

    /**
     * This magic method means that the object is callable like a closure,
     * and when that happens this invoke method is called.
     *
     * This method should return your fake data. You can use the generator to pull fake data from if that is useful
     *
     * @return mixed
     */
    public function __invoke()
    {
        $embeddable = new SkeletonEmbeddable(
            $this->generator->text,
            $this->generator->text
        );

        return $embeddable;
    }
}