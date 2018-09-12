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
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces\HasAnotherDeeplyNestedClientInterface;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces\ReciprocatesAnotherDeeplyNestedClientInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\HasAttributesAddressesInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\ReciprocatesAttributesAddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\HasAttributesEmailsInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\HasCompanyDirectorsInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\ReciprocatesCompanyDirectorInterface;
use My\Test\Project\Entity\Relations\Some\Client\Interfaces\HasSomeClientInterface;
use My\Test\Project\Entity\Relations\Some\Client\Interfaces\ReciprocatesSomeClientInterface;

// phpcs:enable
interface CompanyInterface extends 
    DSM\Interfaces\EntityInterface,
    StringFieldInterface,
    DatetimeFieldInterface,
    FloatFieldInterface,
    DecimalFieldInterface,
    IntegerFieldInterface,
    TextFieldInterface,
    BooleanFieldInterface,
    JsonFieldInterface,
    HasCompanyDirectorsInterface,
    ReciprocatesCompanyDirectorInterface,
    HasAttributesAddressesInterface,
    ReciprocatesAttributesAddressInterface,
    HasAttributesEmailsInterface,
    HasSomeClientInterface,
    ReciprocatesSomeClientInterface,
    HasAnotherDeeplyNestedClientInterface,
    ReciprocatesAnotherDeeplyNestedClientInterface
{
}
