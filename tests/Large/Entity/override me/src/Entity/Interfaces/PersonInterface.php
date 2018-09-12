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
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\HasAttributesAddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\HasAttributesEmailsInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\ReciprocatesAttributesEmailInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\HasCompanyDirectorInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\ReciprocatesCompanyDirectorInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\HasLargeRelationInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\ReciprocatesLargeRelationInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrdersInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;

// phpcs:enable
interface PersonInterface extends 
    DSM\Interfaces\EntityInterface,
    StringFieldInterface,
    DatetimeFieldInterface,
    FloatFieldInterface,
    DecimalFieldInterface,
    IntegerFieldInterface,
    TextFieldInterface,
    BooleanFieldInterface,
    JsonFieldInterface,
    HasAttributesAddressInterface,
    HasAttributesEmailsInterface,
    ReciprocatesAttributesEmailInterface,
    HasCompanyDirectorInterface,
    ReciprocatesCompanyDirectorInterface,
    HasOrdersInterface,
    ReciprocatesOrderInterface,
    HasLargeRelationInterface,
    ReciprocatesLargeRelationInterface
{
}
