<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IntegerWithinRangeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;

class IndexedUniqueIntegerFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return $this->generator->unique()->numberBetween(0, Database::MAX_INT_VALUE);
    }
}
