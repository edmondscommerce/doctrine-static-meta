<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\FakerData\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class FullNameEmbeddableFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return FullNameEmbeddable::create(
            [
                FullNameEmbeddable::EMBEDDED_PROP_TITLE       => $this->generator->title,
                FullNameEmbeddable::EMBEDDED_PROP_FIRSTNAME   => $this->generator->firstName,
                FullNameEmbeddable::EMBEDDED_PROP_MIDDLENAMES => [
                    $this->generator->firstName,
                    $this->generator->firstName,
                ],
                FullNameEmbeddable::EMBEDDED_PROP_LASTNAME    => $this->generator->lastName,
                FullNameEmbeddable::EMBEDDED_PROP_SUFFIX      => $this->generator->jobTitle,
            ]
        );
    }
}
