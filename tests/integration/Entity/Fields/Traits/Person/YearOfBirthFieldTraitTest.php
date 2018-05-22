<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person\YearOfBirthFieldInterface;

class YearOfBirthFieldTraitTest extends AbstractFieldTraitIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/YearOfBirthFieldTraitTest/';
    protected const TEST_FIELD_FQN =   YearOfBirthFieldTrait::class;
    protected const TEST_FIELD_PROP =  YearOfBirthFieldInterface::PROP_YEAR_OF_BIRTH;
}
