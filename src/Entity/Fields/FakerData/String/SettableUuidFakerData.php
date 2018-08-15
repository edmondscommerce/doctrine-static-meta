<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class SettableUuidFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return $this->generator->unique()->uuid;
    }
}
