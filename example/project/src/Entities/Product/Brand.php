<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Product;

// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Interfaces\Product\BrandInterface;
use My\Test\Project\Entity\Relations\Product\Interfaces\HasProductInterface;
use My\Test\Project\Entity\Relations\Product\Interfaces\ReciprocatesProductInterface;
use My\Test\Project\Entity\Relations\Product\Traits\HasProduct\HasProductInverseOneToOne;

// phpcs:enable
class Brand implements
    BrandInterface,
    HasProductInterface,
    ReciprocatesProductInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidateTrait;
    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
    use HasProductInverseOneToOne;
}
