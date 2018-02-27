<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Order;

// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrder\HasOrderManyToOne;
use My\Test\Project\Entity\Relations\Product\Interfaces\HasProductInterface;
use My\Test\Project\Entity\Relations\Product\Interfaces\ReciprocatesProductInterface;
use My\Test\Project\Entity\Relations\Product\Traits\HasProduct\HasProductOwningOneToOne;

// phpcs:enable
class LineItem implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasOrderInterface,
    ReciprocatesOrderInterface,
    HasProductInterface,
    ReciprocatesProductInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidateTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasOrderManyToOne;
    use HasProductOwningOneToOne;
}
