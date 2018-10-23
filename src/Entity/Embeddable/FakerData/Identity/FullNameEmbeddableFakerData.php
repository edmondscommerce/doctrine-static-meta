<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\FakerData\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class FullNameEmbeddableFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        $embeddable = new FullNameEmbeddable();
        $embeddable->setFirstName($this->generator->firstName);
        $embeddable->setLastName($this->generator->lastName);
        $embeddable->setMiddleNames([$this->generator->firstName, $this->generator->firstName]);
        $embeddable->setTitle($this->generator->title());
        $embeddable->setSuffix($this->generator->title);

        return $embeddable;
    }

}