<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableOnceFieldInterface;

class DateTimeSettableOnceFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE
                            .'/DateTimeSettableOnceFieldTraitTest/';
    protected const TEST_FIELD_FQN =   DateTimeSettableOnceFieldTrait::class;
    protected const TEST_FIELD_PROP =  DateTimeSettableOnceFieldInterface::PROP_DATE_TIME_SETTABLE_ONCE;
    protected const TEST_FIELD_DEFAULT = DateTimeSettableOnceFieldInterface::DEFAULT_DATE_TIME_SETTABLE_ONCE;
}
