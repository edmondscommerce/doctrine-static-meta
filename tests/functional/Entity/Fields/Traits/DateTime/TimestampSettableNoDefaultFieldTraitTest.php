<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\TimestampSettableNoDefaultFieldInterface;

class TimestampSettableNoDefaultFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/TimestampSettableNoDefaultFieldTraitTest/';
    protected const TEST_FIELD_FQN =   TimestampSettableNoDefaultFieldTrait::class;
    protected const TEST_FIELD_PROP =  TimestampSettableNoDefaultFieldInterface::PROP_TIMESTAMP_SETTABLE_NO_DEFAULT;
}
