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
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\HasAttributesAddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\HasAttributesEmailsInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\ReciprocatesAttributesEmailInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\HasCompanyDirectorsInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\ReciprocatesCompanyDirectorInterface;
use My\Test\Project\Entity\Relations\Company\Interfaces\HasCompanyInterface;
use My\Test\Project\Entity\Relations\Large\Data\Interfaces\HasLargeDatasInterface;
use My\Test\Project\Entity\Relations\Large\Property\Interfaces\HasLargePropertyInterface;
use My\Test\Project\Entity\Relations\Large\Property\Interfaces\ReciprocatesLargePropertyInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasOrderAddressesInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\HasPersonInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\ReciprocatesPersonInterface;

// phpcs:enable
interface RelationInterface extends 
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
    HasCompanyDirectorsInterface,
    ReciprocatesCompanyDirectorInterface,
    HasLargeDatasInterface,
    HasPersonInterface,
    ReciprocatesPersonInterface,
    HasLargePropertyInterface,
    ReciprocatesLargePropertyInterface,
    HasOrderAddressesInterface,
    ReciprocatesOrderAddressInterface,
    HasCompanyInterface
{
}
