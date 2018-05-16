<?php declare(strict_types=1);

namespace My\Test\Project\Entities;

// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use My\Test\Project\Entity\Fields\Interfaces\Attribute\PriceFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\Attribute\SKUFieldInterface;
use My\Test\Project\Entity\Fields\Traits\Attribute\PriceFieldTrait;
use My\Test\Project\Entity\Fields\Traits\Attribute\SKUFieldTrait;
use My\Test\Project\Entity\Interfaces\ProductInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasOrderLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesOrderLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItem\HasOrderLineItemInverseOneToOne;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\HasProductBrandInterface;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\ReciprocatesProductBrandInterface;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrand\HasProductBrandOwningOneToOne;

// phpcs:enable
class Product implements
    ProductInterface,
    HasOrderLineItemInterface,
    ReciprocatesOrderLineItemInterface,
    HasProductBrandInterface,
    ReciprocatesProductBrandInterface,
    SKUFieldInterface,
    PriceFieldInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidatedEntityTrait;
    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
    use HasOrderLineItemInverseOneToOne;
    use HasProductBrandOwningOneToOne;
    use SKUFieldTrait;
    use PriceFieldTrait;

    public function __construct()
    {
        $this->runInitMethods();
    }
}