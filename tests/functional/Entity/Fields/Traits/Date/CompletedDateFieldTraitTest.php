<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\CompletedDateFieldInterface;

class CompletedDateFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/CompletedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   CompletedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  CompletedDateFieldInterface::PROP_COMPLETED_DATE;
}
