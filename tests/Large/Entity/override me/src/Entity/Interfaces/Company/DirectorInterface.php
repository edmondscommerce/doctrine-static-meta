<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Interfaces\Company;
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
use My\Test\Project\Entity\Relations\Company\Interfaces\HasCompaniesInterface;
use My\Test\Project\Entity\Relations\Company\Interfaces\ReciprocatesCompanyInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\HasLargeRelationsInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\ReciprocatesLargeRelationInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\HasPersonInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\ReciprocatesPersonInterface;

// phpcs:enable
interface DirectorInterface extends 
    DSM\Interfaces\EntityInterface,
    StringFieldInterface,
    DatetimeFieldInterface,
    FloatFieldInterface,
    DecimalFieldInterface,
    IntegerFieldInterface,
    TextFieldInterface,
    BooleanFieldInterface,
    JsonFieldInterface,
    HasCompaniesInterface,
    ReciprocatesCompanyInterface,
    HasPersonInterface,
    ReciprocatesPersonInterface,
    HasLargeRelationsInterface,
    ReciprocatesLargeRelationInterface
{
}
