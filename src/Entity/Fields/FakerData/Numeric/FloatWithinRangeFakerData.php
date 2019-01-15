<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\FloatWithinRangeFieldInterface;

class FloatWithinRangeFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return $this->generator->randomFloat(
            2,
            FloatWithinRangeFieldInterface::MIN_FLOAT_WITHIN_RANGE,
            FloatWithinRangeFieldInterface::MAX_FLOAT_WITHIN_RANGE
        );
    }
}
