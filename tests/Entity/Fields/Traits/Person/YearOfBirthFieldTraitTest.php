<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person\YearOfBirthFieldInterface;

class YearOfBirthFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/YearOfBirthFieldTraitTest/';
    protected const TEST_FIELD_FQN =   YearOfBirthFieldTrait::class;
    protected const TEST_FIELD_PROP =  YearOfBirthFieldInterface::PROP_YEAR_OF_BIRTH;
}
