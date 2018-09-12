<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Interfaces;
// phpcs:disable Generic.Files.LineLength.TooLong

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Fields\Interfaces\BooleanFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\DatetimeFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\DecimalFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\FloatFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\IntegerFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\JsonFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\StringFieldInterface;
use My\Test\Project\Entity\Fields\Interfaces\TextFieldInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasOrderAddressesInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\HasPersonInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\ReciprocatesPersonInterface;

// phpcs:enable
interface OrderInterface extends 
    DSM\Interfaces\EntityInterface,
    StringFieldInterface,
    DatetimeFieldInterface,
    FloatFieldInterface,
    DecimalFieldInterface,
    IntegerFieldInterface,
    TextFieldInterface,
    BooleanFieldInterface,
    JsonFieldInterface,
    HasPersonInterface,
    ReciprocatesPersonInterface,
    HasOrderAddressesInterface,
    ReciprocatesOrderAddressInterface
{
}
