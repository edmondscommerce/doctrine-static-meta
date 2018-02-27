<?php declare(strict_types=1);

namespace My\Test\Project\Entities;

// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasLineItem\HasLineItemInverseOneToOne;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\HasBrandInterface;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\ReciprocatesBrandInterface;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\HasBrand\HasBrandOwningOneToOne;

// phpcs:enable
class Product implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasLineItemInterface,
    ReciprocatesLineItemInterface,
    HasBrandInterface,
    ReciprocatesBrandInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidateTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasLineItemInverseOneToOne;
    use HasBrandOwningOneToOne;
}
