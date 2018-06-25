<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class NullableStringFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return random_int(0, 1) === 1 ? null : $this->generator->realText(100);
    }
}
