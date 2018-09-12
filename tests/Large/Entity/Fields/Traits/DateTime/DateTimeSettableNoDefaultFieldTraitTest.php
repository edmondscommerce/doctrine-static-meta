<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableNoDefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeSettableNoDefaultFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeSettableNoDefaultFieldTrait
 */
class DateTimeSettableNoDefaultFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/DateTimeSettableNoDefaultFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DateTimeSettableNoDefaultFieldTrait::class;
    protected const TEST_FIELD_PROP    = DateTimeSettableNoDefaultFieldInterface::PROP_DATE_TIME_SETTABLE_NO_DEFAULT;
    protected const TEST_FIELD_DEFAULT = DateTimeSettableNoDefaultFieldInterface::DEFAULT_DATE_TIME_SETTABLE_NO_DEFAULT;
    protected const VALIDATES          = false;
 }
