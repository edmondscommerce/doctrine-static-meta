<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableOnceFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeSettableOnceFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeSettableOnceFieldTrait
 */
class DateTimeSettableOnceFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/DateTimeSettableOnceFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DateTimeSettableOnceFieldTrait::class;
    protected const TEST_FIELD_PROP    = DateTimeSettableOnceFieldInterface::PROP_DATE_TIME_SETTABLE_ONCE;
    protected const TEST_FIELD_DEFAULT = DateTimeSettableOnceFieldInterface::DEFAULT_DATE_TIME_SETTABLE_ONCE;
}
