<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableNoDefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class DateTimeSettableNoDefaultFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE
                                         . '/DateTimeSettableNoDefaultFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DateTimeSettableNoDefaultFieldTrait::class;
    protected const TEST_FIELD_PROP    = DateTimeSettableNoDefaultFieldInterface::PROP_DATE_TIME_SETTABLE_NO_DEFAULT;
    protected const TEST_FIELD_DEFAULT = DateTimeSettableNoDefaultFieldInterface::DEFAULT_DATE_TIME_SETTABLE_NO_DEFAULT;
}
