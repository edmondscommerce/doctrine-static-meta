<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\TimestampSettableOnceFieldInterface;

class TimestampSettableOnceFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/TimestampSettableOnceFieldTraitTest/';
    protected const TEST_FIELD_FQN =   TimestampSettableOnceFieldTrait::class;
    protected const TEST_FIELD_PROP =  TimestampSettableOnceFieldInterface::PROP_TIMESTAMP_SETTABLE_ONCE;
}
