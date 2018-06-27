<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;

class EnumFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        $minKey = 0;
        $maxKey = count(EnumFieldInterface::ENUM_OPTIONS) - 1;
        $key    = $this->generator->numberBetween($minKey, $maxKey);

        return EnumFieldInterface::ENUM_OPTIONS[$key];
    }

}
