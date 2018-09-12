<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Interfaces\Large;
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
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\HasLargeRelationsInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\ReciprocatesLargeRelationInterface;

// phpcs:enable
interface PropertyInterface extends 
    DSM\Interfaces\EntityInterface,
    StringFieldInterface,
    DatetimeFieldInterface,
    FloatFieldInterface,
    DecimalFieldInterface,
    IntegerFieldInterface,
    TextFieldInterface,
    BooleanFieldInterface,
    JsonFieldInterface,
    HasLargeRelationsInterface,
    ReciprocatesLargeRelationInterface
{
}
